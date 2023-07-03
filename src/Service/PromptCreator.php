<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Service;

class PromptCreator
{
    /**
     * @todo
     *      get mood
     *      get color scheme
     *      get context
     */
    public function createPromptParts(Page|DataObject $element): array
    {
        if ($element instanceof Page) {
            $promptParts = $this->getPromptFromDocumentContext($element);
        } elseif ($element instanceof DataObject) {
            // todo
        }

        if (empty($promptParts)) {
            $promptParts = ['an inspiring market, emotional, camcorder effect'];
        }

        return [
            ...$promptParts,
            '8k, uhd, dslr,soft lighting,high quality,Fujifilm XT3',
        ];
    }

    protected function getPromptFromDocumentContext(?Page $page): array
    {
        if (!$page) {
            return [];
        }

        // todo ==> translate (?)
        $title = $page->getTitle();
        $description = $page->getDescription();
        $texts = $this->extractImportantTexts($page);

        return [
            '((' . $title . '))',
            '(' . $description . ')',
            ...$texts
        ];
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
