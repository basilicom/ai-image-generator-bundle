<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Service\ImageGenerationService;
use Basilicom\AiImageGeneratorBundle\Service\LockManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private LockManager $lockManager;
    private LoggerInterface $logger;
    private ImageGenerationService $imageGenerationService;

    public function __construct(
        ImageGenerationService $imageGenerationService,
        LockManager            $lockManager,
        LoggerInterface        $logger
    ) {
        $this->imageGenerationService = $imageGenerationService;
        $this->lockManager = $lockManager;
        $this->logger = $logger;
    }

    /** todo
     *      ==> generate prompt and negative prompt
     *              => via ChatGPT
     *      ==> let user create negative prompts/prompts as presets
     *      ==> regenerate image based on meta data directly in Asset-preview and store as new version
     *      ==> API Adapter for Midjourney
     *      ==> add button to image object field
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
     * @throws Exception
     */
    #[Route('/generate', name: 'generate_image_route', methods: ['GET'])]
    public function default(Request $request): JsonResponse
    {
        if ($this->lockManager->isLocked()) {
            return $this->returnError('Currently generating image, please wait.', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->lockManager->lock();

            $generatedImage = $this->imageGenerationService->generateImage($request);

            $this->lockManager->unlock();

            return new JsonResponse(
                [
                    'success' => true,
                    'id' => $generatedImage->getId()
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            $this->logger->error('Error when generating AI images: ' . $e->getMessage());
            $this->lockManager->unlock();
            if ($_ENV['APP_ENV'] !== 'prod') {
                return $this->returnError($e->getMessage());
            } else {
                return $this->returnError('Unable to generate AI image.');
            }
        }
    }

    #[Route('/upscale', name: 'upscale_image_route', methods: ['POST'])]
    public function upscale(Request $request): JsonResponse
    {
        if ($this->lockManager->isLocked()) {
            return $this->returnError('Currently generating image, please wait.', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->lockManager->lock();

            $upscaledImage = $this->imageGenerationService->upscaleImage($request);

            $this->lockManager->unlock();

            return new JsonResponse(
                [
                    'success' => true,
                    'id' => $upscaledImage->getId()
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            $this->logger->error('Error when generating AI images: ' . $e->getMessage());
            $this->lockManager->unlock();
            if ($_ENV['APP_ENV'] !== 'prod') {
                return $this->returnError($e->getMessage());
            } else {
                return $this->returnError('Unable to generate AI image.');
            }
        }
    }

    protected function returnError(string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse(['success' => false, 'message' => $message], $statusCode);
    }
}
