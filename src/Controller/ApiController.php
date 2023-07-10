<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Service\ImageGenerationService;
use Basilicom\AiImageGeneratorBundle\Service\LockManager;
use Basilicom\AiImageGeneratorBundle\Service\PromptCreator;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\PageSnippet;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ApiController extends AbstractController
{
    private LockManager $lockManager;
    private LoggerInterface $logger;
    private ImageGenerationService $imageGenerationService;
    private ConfigurationService $configurationService;
    private AspectRatioCalculator $aspectRatioCalculator;
    private PromptCreator $promptCreator;

    public function __construct(
        ImageGenerationService $imageGenerationService,
        PromptCreator          $promptCreator,
        AspectRatioCalculator  $aspectRatioCalculator,
        ConfigurationService   $configurationService,
        LockManager            $lockManager,
        LoggerInterface        $logger
    ) {
        $this->imageGenerationService = $imageGenerationService;
        $this->configurationService = $configurationService;
        $this->lockManager = $lockManager;
        $this->logger = $logger;
        $this->aspectRatioCalculator = $aspectRatioCalculator;
        $this->promptCreator = $promptCreator;
    }

    // todo ==> based on "accept" header, return image or json!

    #[Route(
        '/generate',
        name: 'ai_image_by_prompt',
        requirements: [
            'prompt' => '.+',
            'negativePrompt' => '.+',
            'seed' => '\d+',
            'width' => '\d+',
            'height' => '\d+'
        ],
        defaults: [
            'negativePrompt' => PromptCreator::DEFAULT_NEGATIVE_PROMPT,
            'seed' => -1,
            'width' => 512,
            'height' => 512
        ],
        methods: ['POST']
    )]
    public function generateByPrompt(Request $request): Response
    {
        $requestData = (array) json_decode($request->getContent(), true);

        $prompt = (array)$requestData['prompt'];
        $negativePrompt = (array)($requestData['negativePrompt'] ?? PromptCreator::DEFAULT_NEGATIVE_PROMPT);
        $width = (int)($requestData['width'] ?? 512);
        $height = (int)($requestData['height'] ?? 512);
        $seed = (int)($requestData['seed'] ?? -1);
        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($width, $height);

        $config = $this->configurationService->getConfiguration();
        $config->setPromptParts($prompt);
        $config->setNegativePromptParts($negativePrompt);
        $config->setAspectRatio($aspectRatio);
        $config->setSeed($seed);
        $config->setUpscale($width > 512 || $height > 512); // todo => base sizes should be configurable/dependent by model

        return $this->generateImage($request, fn () => $this->imageGenerationService->generateImage($config));
    }

    #[Route(
        '/generate/{context}-{id}',
        name: 'ai_image_by_element_context',
        requirements: [
            'context' => '.+',
            'id' => '\d+',
            'width' => '\d+',
            'height' => '\d+'
        ],
        methods: ['POST']
    )]
    public function generateByElementContext(Request $request): Response
    {
        $requestData = (array) json_decode($request->getContent(), true);

        $context = (string)$request->get('context');
        $contextElementId = (int)$request->get('id');
        $width = (int)($requestData['width'] ?? 512);
        $height = (int)($requestData['height'] ?? 512);

        $element = match ($context) {
            'document' => PageSnippet::getById($contextElementId),
            'object' => DataObject::getById($contextElementId),
        };

        $prompt = $this->promptCreator->createPromptFromPimcoreElement($element);
        $negativePrompt = PromptCreator::DEFAULT_NEGATIVE_PROMPT;

        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($width, $height);

        $config = $this->configurationService->getConfiguration();
        $config->setPromptParts($prompt);
        $config->setNegativePromptParts([$negativePrompt]);
        $config->setAspectRatio($aspectRatio);
        $config->setUpscale($width > 512 || $height > 512); // todo => base sizes should be configurable/dependent by model

        return $this->generateImage(
            $request,
            fn () => $this->imageGenerationService->generateImage($config)
        );
    }

    #[Route('/upscale/{id}', name: 'ai_image_upscale', methods: ['POST'])]
    public function upscale(Request $request): Response
    {
        $config = $this->configurationService->getConfiguration();
        $config->setUpscale(true);

        return $this->generateImage(
            $request,
            fn () => $this->imageGenerationService->upscaleImage($config, (int)$request->get('id'))
        );
    }

    private function generateImage(Request $request, callable $imageGenerationMethod): Response
    {
        if ($this->lockManager->isLocked()) {
            return $this->respond($request, null, Response::HTTP_FORBIDDEN, 'Currently generating image, please wait.');
        }

        try {
            $this->lockManager->lock();

            $generatedAsset = $imageGenerationMethod();

            $this->lockManager->unlock();

            return $this->respond($request, $generatedAsset);
        } catch (Exception|Throwable $e) {
            $this->logger->error('Error when generating AI images: ' . $e->getMessage());
            $this->lockManager->unlock();

            $message = $_ENV['APP_ENV'] !== 'prod' ? $e->getMessage() : 'Unable to generate AI image.';

            return $this->respond($request, null, Response::HTTP_INTERNAL_SERVER_ERROR, $message);
        }
    }

    private function respond(
        Request $request,
        ?Asset  $generatedAsset,
        int     $statusCode = Response::HTTP_OK,
        string  $message = ''
    ): Response {
        $acceptHeader = $request->headers->get('Accept');
        if (str_contains($acceptHeader, 'image/')) {
            if ($statusCode === Response::HTTP_OK) {
                $response = new Response($generatedAsset->getData());
                $response->headers->set('Content-Type', $generatedAsset->getMimeType());
            } else {
                $response = new Response($message, $statusCode);
            }

            return $response;
        } else {
            $payload = $statusCode === Response::HTTP_OK
                ? ['success' => true, 'id' => $generatedAsset->getId(), 'image' => base64_encode($generatedAsset->getData())]
                : ['success' => false, 'message' => $message];

            return $this->json($payload, $statusCode);
        }
    }
}
