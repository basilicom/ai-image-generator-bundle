<?php

namespace Tests\Unit\Service;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Service\Brand\ColorService;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptExtractor;
use Basilicom\AiImageGeneratorBundle\Service\PromptService;
use PHPUnit\Framework\TestCase;

class PromptCreatorTest extends TestCase
{
    public static function getPromptDataProvider(): array
    {
        return [
            [
                'A tree in the middle of a street with an apple in the crown',
                'tree,in middle of street,with apple,in crown'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider getPromptDataProvider
     */
    public function getPrompt(string $prompt, string $expectedResult)
    {
        // prepare
        $configurationService = $this->createMock(ConfigurationService::class);
        $colorService = $this->createMock(ColorService::class);
        $promptExtractor = $this->createMock(PromptExtractor::class);

        $classUnderTest = new PromptService($promptExtractor, $configurationService, $colorService);

        // test
        $result = $classUnderTest->getPrompt($prompt);

        // verify
        $this->assertEquals($expectedResult, $result);
    }
}
