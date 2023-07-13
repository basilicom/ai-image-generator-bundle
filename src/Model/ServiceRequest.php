<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

class ServiceRequest
{
    private string $uri;
    private string $method;
    private array $payload;
    private array $headers;
    private bool $isMultiPart;

    public function __construct(string $uri, string $method, array $payload, array $headers = [], bool $isMultiPart = false)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->payload = $payload;
        $this->headers = $headers;
        $this->isMultiPart = $isMultiPart;
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

    public function isMultiPart(): bool
    {
        return $this->isMultiPart;
    }
}
