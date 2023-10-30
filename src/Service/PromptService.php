<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\BasilicomPromptConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OllamaPromptConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OpenAIPromptConfig;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Service\Brand\ColorService;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptExtractor;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptPreset;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\BasilicomStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\OllamaStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\OpenAiStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\SimpleStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Prompting\Strategy;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\PageSnippet;
use Psr\Container\ContainerInterface;

class PromptService
{
    private PromptExtractor $promptExtractor;
    private ConfigurationService $configurationService;
    private ColorService $colorService;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface   $container,
        PromptExtractor      $promptExtractor,
        ConfigurationService $configurationService,
        ColorService         $colorService
    ) {
        $this->promptExtractor = $promptExtractor;
        $this->configurationService = $configurationService;
        $this->colorService = $colorService;
        $this->container = $container;
    }

    public function getPrompt(
        ?string                                 $prompt,
        PageSnippet|DataObject|Asset\Image|null $element = null,
        bool                                    $isBrandingEnabled = false
    ): Prompt {
        $positivePrompt = $prompt ?? '';
        $context = Prompt::CONTEXT_OBJECT;
        if (empty($positivePrompt) && $element !== null) {
            if ($element instanceof PageSnippet) {
                $positivePrompt = $this->promptExtractor->createPromptFromPimcoreElement($element);
                $context = Prompt::CONTEXT_DOCUMENT;
            } elseif ($element instanceof DataObject) {
                $positivePrompt = $this->promptExtractor->createPromptFromPimcoreElement($element);
                $context = Prompt::CONTEXT_OBJECT;
            }
        }

        if ($isBrandingEnabled) {
            $brandColors = $this->configurationService->getBrandColors();
            $brandColorNames = array_filter(array_map(fn (string $color) => $this->colorService->getColorName($color), $brandColors));
            foreach ($brandColorNames as $brandColorName) {
                if (strrpos($positivePrompt, $brandColorName) === false) {
                    $positivePrompt = $positivePrompt . ',' . sprintf('((shades of %s))', $brandColorName);
                }
            }
        }

        $prompt = new Prompt();
        $prompt->setPositive($positivePrompt);
        $prompt->setNegative(implode(',', [PromptPreset::DEFAULT_NEGATIVE_PROMPT, PromptPreset::SFW_NEGATIVE_PROMPT]));
        $prompt->setContext($context);

        return $this->enhancePrompt($prompt);
    }

    private function enhancePrompt(Prompt $prompt): Prompt
    {
        $promptEnhancementConfig = $this->configurationService->getPromptEnhancementConfiguration();
        if ($promptEnhancementConfig instanceof BasilicomPromptConfig) {
            $strategy = $this->container->get(BasilicomStrategy::class);
        } elseif ($promptEnhancementConfig instanceof OllamaPromptConfig) {
            $strategy = $this->container->get(OllamaStrategy::class);
        } elseif ($promptEnhancementConfig instanceof OpenAIPromptConfig) {
            $strategy = $this->container->get(OpenAiStrategy::class);
        } else {
            $strategy = $this->container->get(SimpleStrategy::class);
        }

        /** @var Strategy $strategy */
        return $strategy->enhancePrompt($prompt);
    }
}
