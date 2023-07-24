<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestService
{
    /**
     * @throws Exception
     */
    public function callApi(ServiceRequest $request): ResponseInterface
    {
        $headers = $this->getHeaders($request);

        $options = [
            RequestOptions::HEADERS => $headers,
            RequestOptions::VERIFY => false
        ];

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($request->isMultiPart()) {
                $options[RequestOptions::MULTIPART] = $request->getPayload();
            } else {
                $options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
                $options[RequestOptions::BODY] = json_encode($request->getPayload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        } elseif ($request->getMethod() === Request::METHOD_GET) {
            $options[RequestOptions::QUERY] = $request->getPayload();
        }

        try {
            return (new Client())->request($request->getMethod(), $request->getUri(), $options);
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function getHeaders(ServiceRequest $request): array
    {
        return array_merge(
            [
                'Accept' => 'application/json',
            ],
            $request->getHeaders()
        );
    }
}
