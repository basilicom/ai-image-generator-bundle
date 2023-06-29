<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Basilicom\AiImageGeneratorBundle\Model\RequestFactory;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /** todo
     *      ==> generate prompt and negative prompt
     *              => via ChatGPT
     *              => create context
     *      ==> let user create negative prompts/prompts as presets
     *      ==> generate bundle
     *      ==> API Adapter for automatic1111
     *      ==> API Adapter for DreamStudio
     *      ==> API Adapter for Midjourney
     *      ==> store Asset and return Asset itself
     *      ==> add button to image editable
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
    #[Route('/', name: 'generate_image_route')]
    public function default(RequestFactory $requestFactory, RequestService $requestService): Response
    {
        $request = $requestFactory->getRequest();
        $generatedImage = $requestService->generateImage($request);

        $response = new Response();
        $response->headers->add(['Content-Type' => 'image/jpeg']);
        $response->setContent($generatedImage);

        return $response;
    }
}
