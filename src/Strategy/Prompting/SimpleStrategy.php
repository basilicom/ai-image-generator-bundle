<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\Prompting;

use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptPreset;

class SimpleStrategy extends Strategy
{
    public function enhancePrompt(Prompt $prompt): Prompt
    {
        $prompt = parent::enhancePrompt($prompt);

        $valuablePresets = PromptPreset::PRESETS;
        if ($prompt->getContext() === Prompt::CONTEXT_OBJECT) {
            unset($valuablePresets[PromptPreset::CINEMATIC]);
            unset($valuablePresets[PromptPreset::DIGITAL_ART]);
            unset($valuablePresets[PromptPreset::ADVERTISEMENT_POSTER]);
        }

        $style = array_rand($valuablePresets);

        $prompt->setPositive($this->promptPreset->getPositivePrompt($prompt, $style));
        $prompt->setNegative($this->promptPreset->getNegativePrompt($prompt, $style));

        return $prompt;
    }
}
