<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;

class RequestService
{
    /**
     * @throws Exception
     */
    public function generateImage(ServiceRequest $request): array
    {
        $options = [];
        $options[RequestOptions::HEADERS] = array_merge(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            $request->getHeaders()
        );

        $options[RequestOptions::VERIFY] = false;

        if ($request->getMethod() === Request::METHOD_POST) {
            $options[RequestOptions::BODY] = json_encode($request->getPayload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif ($request->getMethod() === Request::METHOD_GET) {
            $options[RequestOptions::QUERY] = $request->getPayload();
        }

        try {
            $client = new Client();
            $apiResponse = $client->request($request->getMethod(), $request->getUri(), $options);

            return json_decode($apiResponse->getBody()->getContents(), true);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
