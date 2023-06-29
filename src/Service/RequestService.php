<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Model\Request;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;

class RequestService
{
    public function generateImage(Request $request): string
    {
        $body = json_encode($request->getPayload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $options = [];
        $options[RequestOptions::HEADERS] = ['content-type' => 'application/json'];
        $options[RequestOptions::BODY] = $body;

        try {
            $client = new Client();
            $apiResponse = $client->request($request->getMethod(), $request->getUri(), $options);
            $responseBody = json_decode($apiResponse->getBody()->getContents(), true);

            $response = new Response();
            $response->headers->add(['Content-Type' => 'image/jpeg']);

            return base64_decode($responseBody['images'][0]);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
