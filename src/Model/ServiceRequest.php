<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

class ServiceRequest
{
    private string $uri;
    private string $method;
    private array $payload;
    private array $headers;

    public function __construct(string $uri, string $method, array $payload, array $headers = [])
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->payload = $payload;
        $this->headers = $headers;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
