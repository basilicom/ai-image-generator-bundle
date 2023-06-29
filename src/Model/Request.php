<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

class Request
{
    private string $uri;
    private string $method;
    private array $payload;

    public function __construct(string $uri, string $method, array $payload)
    {

        $this->uri = $uri;
        $this->method = $method;
        $this->payload = $payload;
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
}
