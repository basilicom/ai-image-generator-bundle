<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequestFactory;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Service\PromptCreator;
use Basilicom\AiImageGeneratorBundle\Service\LockManager;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;
use Pimcore\Model\Asset;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private ServiceRequestFactory $requestFactory;
    private RequestService $requestService;
    private LockManager $lockManager;
    private LoggerInterface $logger;
    private PromptCreator $promptCreator;
    private AspectRatioCalculator $aspectRatioCalculator;
    private ConfigurationService $configurationService;

    public function __construct(
        ServiceRequestFactory $requestFactory,
        RequestService        $requestService,
        LockManager           $lockManager,
        PromptCreator         $promptCreator,
        AspectRatioCalculator $aspectRatioCalculator,
        ConfigurationService  $configurationService,
        LoggerInterface       $logger
    ) {
        $this->requestFactory = $requestFactory;
        $this->requestService = $requestService;
        $this->lockManager = $lockManager;
        $this->logger = $logger;
        $this->promptCreator = $promptCreator;
        $this->aspectRatioCalculator = $aspectRatioCalculator;
        $this->configurationService = $configurationService;
    }

    /** todo
     *      ==> generate prompt and negative prompt
     *              => via ChatGPT
     *      ==> let user create negative prompts/prompts as presets
     *      ==> API Adapter for DreamStudio
     *      ==> API Adapter for Midjourney
     *      ==> add button to image object field
     *      ==> upscaling
     *      ==> controlnet
     *               "alwayson_scripts": {
     *                  "controlnet": {
     *                  "args": [
     *                      {
     *                          "input_image": $encodedImage, // source image to be get preprocessed image to be applied on source
     *                          "module": "depth",
     *                          "model": "diff_control_sd15_depth_fp16 [978ef0a1]"
     *                      }
     *                  ]
     *                  }
     *               }
     */
    #[Route('', name: 'generate_image_route')]
    public function default(Request $request): JsonResponse
    {
        if ($this->lockManager->isLocked()) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Currently generating image, please wait.'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $statusCode = Response::HTTP_OK;
        $resultPayload = [];
        try {
            $this->lockManager->lock();

            $config = $this->getServiceRequestConfig($request);
            $serviceRequest = $this->requestFactory->createServiceRequest($config);
            $generatedImage = $this->requestService->generateImage($serviceRequest);

            $resultPayload = [
                'success' => true,
                'id' => $this->createAsset($generatedImage, $config)->getId()
            ];
        } catch (Exception $e) {
            $this->logger->error('Error when generating AI images: ' . $e->getMessage());
        } finally {
            if (isset($e)) {
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                $resultPayload = ['success' => false, 'message' => 'Unable to generate AI image.'];
            }

            $this->lockManager->unlock();
        }

        return new JsonResponse($resultPayload, $statusCode);
    }

    private function getServiceRequestConfig(Request $request): AbstractConfiguration
    {
        $context = (string)$request->get('context');
        $id = (int)$request->get('id');
        $width = (int)$request->get('width');
        $height = (int)$request->get('height');

        $prompt = $this->promptCreator->createPrompt($context, $id);
        // todo
        $negativePrompt = '(semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck';

        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($width, $height);

        $config = $this->configurationService->getConfiguration();
        $config->setPrompt($prompt);
        $config->setNegativePrompt($negativePrompt);
        $config->setAspectRatio($aspectRatio);

        return $config;
    }

    private function createAsset(AiImage $generatedImage, AbstractConfiguration $requestConfig): Asset
    {
        $asset = new Asset();
        $asset->setParent(Asset\Service::createFolderByPath('/ai-images'));
        $asset->setKey(uniqid('ai-image') . '.png');
        $asset->setType('image');
        $asset->setData($generatedImage->getData());

        $asset->addMetadata('prompt', 'input', $requestConfig->getPrompt());
        $asset->addMetadata('negative-prompt', 'input', $requestConfig->getNegativePrompt());

        return $asset->save();
    }
}
