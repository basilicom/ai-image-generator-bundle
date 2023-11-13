<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

class Prompt
{
    public const CONTEXT_OBJECT = 'object';
    public const CONTEXT_DOCUMENT = 'document';
    public const CONTEXT_ASSET = 'asset';

    private string $positive = '';
    private string $negative = '';
    private string $context = self::CONTEXT_OBJECT;

    public function setPositive(string $positive): void
    {
        $this->positive = $positive;
    }

    public function getPositive(): string
    {
        return $this->positive;
    }

    public function setNegative(string $negative): void
    {
        $this->negative = $negative;
    }

    public function getNegative(): string
    {
        return $this->negative;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

}
