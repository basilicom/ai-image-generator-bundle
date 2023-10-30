<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Service\Brand\ColorService;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptExtractor;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\PageSnippet;

class PromptService
{
    public const DEFAULT_NEGATIVE_PROMPT = [
        '(nsfw,nude,naked:1.5)',
        '(semi-realistic,cgi,3d,render,sketch,cartoon,drawing,anime:1.4)',
        '(extra fingers,mutated hands,poorly drawn hands,mutation,fused fingers,too many fingers:1.5)',
        '(malformed limbs,bad proportions,bad anatomy,extra limbs:1.5)',
        '(missing arms,missing legs,extra arms,extra legs:1.5)',
        '(cloned face,poorly drawn face,long nec:1.5)',
        '(text,watermark:1.5)',
        'cropped',
        'out of frame',
        'worst quality',
        'low quality',
        'jpeg artifacts',
        'ugly',
        'duplicate',
        'morbid',
        'mutilated',
        'blurry',
        'dehydrated',
        'disfigured',
        'deformed',
        'gross proportions',
    ];

    private PromptExtractor $promptExtractor;
    private ConfigurationService $configurationService;
    private ColorService $colorService;

    public function __construct(
        PromptExtractor      $promptExtractor,
        ConfigurationService $configurationService,
        ColorService         $colorService
    ) {
        $this->promptExtractor = $promptExtractor;
        $this->configurationService = $configurationService;
        $this->colorService = $colorService;
    }

    public function getPrompt(?string $prompt, PageSnippet|DataObject|Asset\Image|null $element = null, bool $isBrandingEnabled = false): string
    {
        $prompt = $prompt ?? '';
        if (empty($prompt) && $element !== null) {
            if ($element instanceof Asset\Image) {
                $prompt = $element->getMetadata(MetaDataEnum::PROMPT);
            } elseif ($element instanceof PageSnippet || $element instanceof DataObject) {
                $prompt = $this->promptExtractor->createPromptFromPimcoreElement($element);
            }
        }

        if ($isBrandingEnabled) {
            $brandColors = $this->configurationService->getBrandColors();
            $brandColorNames = array_filter(array_map(fn (string $color) => $this->colorService->getColorName($color), $brandColors));
            foreach ($brandColorNames as $brandColorName) {
                if (strrpos($prompt, $brandColorName) === false) {
                    $prompt = sprintf('((shades of %s)),', $brandColorName) . $prompt;
                }
            }
        }

        $prompt = '(masterpiece,best quality),' . $prompt . ',8k,uhd,soft lighting,high quality';

        return $this->sanitizePrompt($prompt);
    }

    private function sanitizePrompt(string $prompt): string
    {
        $wordsToRemove = [
            'a',
            'an',
            'the'
        ];

        $prompt = strtolower($prompt);
        $prompt = preg_replace('/(\ba\b|\ban\b) (\w+)/i', '$1 $2,', $prompt);

        foreach ($wordsToRemove as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/';
            $prompt = preg_replace($pattern, '', $prompt);
        }

        $prompt = preg_replace('/\s+/', ' ', $prompt);
        $prompt = str_replace(', ', ',', $prompt);

        return trim($prompt);
    }
}
