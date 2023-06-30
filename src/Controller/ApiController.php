<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Model\RequestFactory;
use Basilicom\AiImageGeneratorBundle\Service\LockManager;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Exception;
use Pimcore\Model\Asset;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private RequestFactory $requestFactory;
    private RequestService $requestService;
    private LockManager $lockManager;
    private LoggerInterface $logger;

    public function __construct(
        RequestFactory  $requestFactory,
        RequestService  $requestService,
        LockManager     $lockManager,
        LoggerInterface $logger
    ) {
        $this->requestFactory = $requestFactory;
        $this->requestService = $requestService;
        $this->lockManager = $lockManager;
        $this->logger = $logger;
    }

    /** todo
     *      ==> generate prompt and negative prompt
     *              => via ChatGPT
     *              => create context
     *      ==> let user create negative prompts/prompts as presets
     *      ==> API Adapter for DreamStudio
     *      ==> API Adapter for Midjourney
     *      ==> add button to image editable
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
     */
    #[Route('', name: 'generate_image_route')]
    public function default(): JsonResponse
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
            $request = $this->requestFactory->getRequest();
            $generatedImage = $this->requestService->generateImage($request);

            $imagePath = Asset\Service::createFolderByPath('/ai-images');

            $asset = new Asset();
            $asset->setKey(uniqid('ai-image') . '.png');
            $asset->setType('image');
            $asset->setData($generatedImage);
            $asset->setParent($imagePath);
            $asset->save();

            $resultPayload = [
                'success' => true,
                'id' => $asset->getId()
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
}
