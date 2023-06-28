<?php

declare(strict_types=1);

namespace Basilicom\AiImageGeneratorBundle\Controller;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/', name: 'generate_image_route')]
    public function default(): Response
    {
        $client = new Client();

        $uri = 'http://host.docker.internal:7860/sdapi/v1/txt2img'; // todo ==> read config
        $method = Request::METHOD_POST;

        $aspectRatio = $this->calculateAspectRatio('16:9');

        // todo ==> generate prompt and negative prompt
        //              => via ChatGPT
        //              => create context
        //      ==> let user create negative prompts/prompts as presets
        //      ==> generate bundle
        //      ==> API Adapter for automatic1111
        //      ==> API Adapter for DreamStudio
        //      ==> API Adapter for Midjourney
        //      ==> store Asset and return Asset itself
        //      ==> add button to image editable

        $payload = [
            'steps' => 10, // todo ==> read config
            'sd_model_checkpoint' => 'realisticVisionV20_v20', // todo ==> read config

            'prompt' => 'b&w photo of cat sitting on a stone, half body, body, high detailed skin, skin pores, coastline, overcast weather, wind, waves, 8k uhd, dslr, soft lighting, high quality, film grain, Fujifilm XT3',
            'negative_prompt' => '(semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck',

            'seed' => -1,
            'batch_size' => 1,

            'width' => $aspectRatio['width'],
            'height' => $aspectRatio['height'],

            'sampler_index' => 'Euler a',
            'cfg_scale' => 7,
        ];

        // todo ==> controlnet
        /**
        "alwayson_scripts": {
        "controlnet": {
        "args": [
        {
        "input_image": $encodedImage,
        "module": "depth",
        "model": "diff_control_sd15_depth_fp16 [978ef0a1]"
        }
        ]
        }
        }
         */

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $options = [];
        $options[RequestOptions::HEADERS] = ['content-type' => 'application/json'];
        $options[RequestOptions::BODY] = $body;

        try {
            $apiResponse = $client->request($method, $uri, $options);
            $responseBody = json_decode($apiResponse->getBody()->getContents(), true);

            $response = new Response();
            $response->headers->add(['Content-Type' => 'image/jpeg']);
            $response->setContent(base64_decode($responseBody['images'][0]));

            return $response;
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }


    protected function calculateAspectRatio(string $aspectRatio): array
    {
        // Split the aspect ratio into width and height
        list($aspectWidth, $aspectHeight) = explode(':', $aspectRatio);

        // Calculate the width and height based on the aspect ratio
        $width = 512;
        $height = 512;

        // Check if width needs to be adjusted based on height
        if ($width * $aspectHeight > $height * $aspectWidth) {
            $width = $height * $aspectWidth / $aspectHeight;
        } else {
            $height = $width * $aspectHeight / $aspectWidth;
        }

        // Return the new width and height as an array
        return [
            'width' => $width,
            'height' => $height
        ];
    }
}
