<?php

namespace Tests\Unit\Service;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Service\Brand\ColorService;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptExtractor;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptPreset;
use Basilicom\AiImageGeneratorBundle\Service\PromptService;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\SimpleStrategy;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PromptCreatorTest extends TestCase
{
    public static function getPromptDataProvider(): array
    {
        $expectedPrompt = new Prompt();
        $expectedPrompt->setPositive('tree,in middle of street,with apple,in crown');
        $expectedPrompt->setNegative(implode(',', [PromptPreset::DEFAULT_NEGATIVE_PROMPT, PromptPreset::SFW_NEGATIVE_PROMPT]));

        return [
            ['A tree in the middle of a street with an apple in the crown', $expectedPrompt]
        ];
    }

    /**
     * @test
     * @dataProvider getPromptDataProvider
     */
    public function getPrompt(string $prompt, Prompt $expectedResult)
    {
        // prepare
        $simpleStrategyMock = $this->createMock(SimpleStrategy::class);
        $simpleStrategyMock
            ->expects($this->once())
            ->method('enhancePrompt')
            ->willReturn($expectedResult);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturn($simpleStrategyMock);

        $configurationService = $this->createMock(ConfigurationService::class);
        $colorService = $this->createMock(ColorService::class);
        $promptExtractor = $this->createMock(PromptExtractor::class);

        $classUnderTest = new PromptService($containerMock, $promptExtractor, $configurationService, $colorService);

        // test
        $result = $classUnderTest->getPrompt($prompt);

        // verify
        $this->assertEquals($expectedResult->getPositive(), $result->getPositive());
    }
}
