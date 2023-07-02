<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Service;

class PromptCreator
{
    public const CONTEXT_DOCUMENT = 'document';
    public const CONTEXT_OBJECT = 'object';

    /**
     * @todo
     *      get mood
     *      get color scheme
     *      get context
     */
    public function createPrompt(string $context, int $id): string
    {
        $prompt = match ($context) {
            self::CONTEXT_DOCUMENT => $this->getPromptFromDocumentContext(Page::getById($id)),
            // todo ==> support object context
        };

        if (empty($prompt)) {
            $prompt = 'an inspiring market, Emotional, camcorder effect';
        }

        $prompt .= ', 8k uhd, dslr, soft lighting, high quality, Fujifilm XT3';

        return $prompt;
    }

    protected function getPromptFromDocumentContext(?Page $page): string
    {
        if (!$page) {
            return '';
        }

        // todo ==> translate (?)
        $title = $page->getTitle();
        $description = $page->getDescription();
        $texts = $this->extractImportantTexts($page);

        return sprintf('((%s)), (%s), %s', $title, $description, implode(', ', $texts));
    }

    private function extractImportantTexts(Page $page): array
    {
        $pageContent = Service::render($page);

        $contents = [];
        preg_match_all('/<h1[^>]*>(.*?)<\/h1>/i', $pageContent, $h1Matches);
        foreach ($h1Matches[1] as $h1_content) {
            $contents[] = strip_tags($h1_content);
        }

        // Match <h2> tags and extract their contents
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $pageContent, $h2Matches);
        foreach ($h2Matches[1] as $h2_content) {
            $contents[] = strip_tags($h2_content);
        }

        return $contents;
    }
}
