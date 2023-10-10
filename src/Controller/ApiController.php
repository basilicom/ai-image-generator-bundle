<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\FeatureEnum;
use Basilicom\AiImageGeneratorBundle\Model\InpaintingMask;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\ImageGenerationService;
use Basilicom\AiImageGeneratorBundle\Service\LockManager;
use Basilicom\AiImageGeneratorBundle\Service\PromptCreator;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\PageSnippet;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    #[Route(
        '/generate/{context}-{id}',
        name: 'ai_image_by_element_context',
        requirements: [
            'prompt' => '.+',
            'context' => '.+',
            'id' => '\d+',
            'aspectRatio' => '\d+\:\d+',
        ],
        methods: ['POST']
    )]
    public function generateByElementContext(Request $request): Response
    {
        // create a regex for 16:9
        preg_match_all('/(\d+)x(\d+)/', $request->get('aspectRatio'), $matches);
        $payload = json_decode($request->getContent(), true);

        $context = (string)$request->get('context');
        $contextElementId = (int)$request->get('id');
        $aspectRatio = $this->aspectRatioCalculator->isValidAspectRatio((string)$payload['aspectRatio'])
            ? ((string)$payload['aspectRatio'])
            : AspectRatioCalculator::DEFAULT_ASPECT_RATIO;

        $element = match ($context) {
            'document' => PageSnippet::getById($contextElementId),
            'object' => DataObject::getById($contextElementId),
        };

        $payload = json_decode($request->getContent(), true);

        $prompt = $payload['prompt'] ?? '';
        $prompt = empty($prompt) ? $this->promptCreator->createPromptFromPimcoreElement($element) : [$prompt];
        $negativePrompt = PromptCreator::DEFAULT_NEGATIVE_PROMPT;

        $config = $this->configurationService->getServiceConfiguration(FeatureEnum::TXT_2_IMG);
        $config->setPromptParts($prompt);
        $config->setNegativePromptParts([$negativePrompt]);
        $config->setAspectRatio($aspectRatio);

        return $this->process(
            $request,
            fn () => $this->imageGenerationService->generateImage($config)
        );
    }

    #[Route('/upscale/{id}', name: 'ai_image_upscale', methods: ['POST'])]
    public function upscale(Request $request): Response
    {
        $config = $this->configurationService->getServiceConfiguration(FeatureEnum::UPSCALE);

        return $this->process(
            $request,
            fn () => $this->imageGenerationService->upscaleImage($config, (int)$request->get('id'))
        );
    }

    #[Route(
        '/vary/{id}',
        name: 'ai_image_vary',
        requirements: [
            'prompt' => '.+',
            'seed' => '\d+'
        ],
        methods: ['POST']
    )]
    public function vary(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $assetId = (int)$request->get('id');
        $asset = Asset\Image::getById($assetId);
        if (!$asset) {
            return $this->respond($request, null, Response::HTTP_NOT_FOUND, 'No such asset');
        }

        $prompt = (string)($payload['prompt'] ?? $asset->getMetadata(MetaDataEnum::PROMPT));
        $negativePrompt = (string)$asset->getMetadata(MetaDataEnum::NEGATIVE_PROMPT);
        $seed = (int)($payload['seed'] ?? $asset->getMetadata(MetaDataEnum::SEED));
        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($asset->getWidth(), $asset->getHeight());

        $config = $this->configurationService->getServiceConfiguration(FeatureEnum::IMAGE_VARIATIONS);
        $config->setPromptParts([$prompt]);
        $config->setNegativePromptParts([$negativePrompt]);
        $config->setAspectRatio($aspectRatio);
        $config->setSeed($seed);

        return $this->process(
            $request,
            fn () => $this->imageGenerationService->varyImage($config, $asset)
        );
    }

    #[Route(
        '/inpaint/background/{id}',
        name: 'ai_image_inpaint_background',
        requirements: [
            'prompt' => '.+'
        ],
        methods: ['POST']
    )]
    public function inpaintBackground(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $assetId = (int)$request->get('id');
        $asset = Asset\Image::getById($assetId);
        if (!$asset) {
            return $this->respond($request, null, Response::HTTP_NOT_FOUND, 'No such asset');
        }

        $prompt = (string)($payload['prompt'] ?? $asset->getMetadata(MetaDataEnum::PROMPT));
        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($asset->getWidth(), $asset->getHeight());

        $config = $this->configurationService->getServiceConfiguration(FeatureEnum::INPAINT_BACKGROUND);
        $config->setPromptParts([$prompt]);
        $config->setAspectRatio($aspectRatio);

        return $this->process(
            $request,
            fn () => $this->imageGenerationService->inpaintBackground($config, (int)$request->get('id'))
        );
    }

    #[Route(
        '/inpaint/{id}',
        name: 'ai_image_inpaint',
        requirements: [
            'prompt' => '.+',
            'draft' => 'true|false',
        ],
        methods: ['POST']
    )]
    public function inpaint(Request $request): Response
    {
        $payload = $request->request->all();

        $assetId = (int)$request->get('id');
        $asset = Asset\Image::getById($assetId);
        if (!$asset) {
            return $this->respond($request, null, Response::HTTP_NOT_FOUND, 'No such asset');
        }

        $prompt = (string)($payload['prompt'] ?? $asset->getMetadata(MetaDataEnum::PROMPT));
        $isDraft = (bool)$payload['draft'];
        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($asset->getWidth(), $asset->getHeight());

        /** @var UploadedFile $mask */
        $mask = $request->files->get('mask');
        $mask = new InpaintingMask(base64_encode($mask->getContent()));

        $config = $this->configurationService->getServiceConfiguration(FeatureEnum::INPAINT);
        $config->setPromptParts([$prompt]);
        $config->setAspectRatio($aspectRatio);
        $config->setInpaintingMask($mask);

        return $this->process(
            $request,
            fn () => $this->imageGenerationService->inpaint($config, (int)$request->get('id'), !$isDraft)
        );
    }

    #[Route(
        '/save/{id}',
        name: 'ai_image_save',
        methods: ['POST']
    )]
    public function saveAsset(Request $request): Response
    {
        $assetId = (int)$request->get('id');
        $asset = Asset\Image::getById($assetId);
        if (!$asset) {
            return $this->respond($request, null, Response::HTTP_NOT_FOUND, 'No such asset');
        }

        /** @var UploadedFile $image */
        $image = $request->files->get('data');
        $asset->setData($image->getContent());
        $asset->save();

        return $this->process($request, fn () => $asset);
    }

    private function process(Request $request, callable $imageGenerationMethod): Response
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
        Request      $request,
        ?Asset\Image $generatedAsset,
        int          $statusCode = Response::HTTP_OK,
        string       $message = ''
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
