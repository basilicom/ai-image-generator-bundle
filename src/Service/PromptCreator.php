<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Service;

class PromptCreator
{
    /**
     * @todo
     *      get mood
     *      get color scheme
     *      get context
     *      get style
     */
    public function createPromptParts(PageSnippet|DataObject $element): array
    {
        $promptParts = [];
        if ($element instanceof PageSnippet) {
            $promptParts = [
                ...$promptParts,
                ...$this->getPromptFromDocumentContext($element),
            ];
        } elseif ($element instanceof DataObject) {
            $promptParts = [
                ...$promptParts,
                ...$this->getPromptFromDataObjectContext($element)
            ];
        }

        if (empty($promptParts)) {
            $promptParts = ['an inspiring market, emotional, camcorder effect'];
        }

        return [
            ...$promptParts,
            '8k, uhd, soft lighting, high quality',
        ];
    }

    protected function getPromptFromDocumentContext(?PageSnippet $page): array
    {
        if (!$page) {
            return [];
        }

        // todo ==> translate (?)
        $prompts = [];
        if ($page instanceof Page) {
            $title = $page->getTitle();
            if (!empty($title)) {
                $prompts[] = '((' . $title . '))';
            }

            $description = $page->getDescription();
            if (!empty($description)) {
                $prompts[] = '(' . $description . ')';
            }
        }

        return [
            ...$prompts,
            ...$this->extractImportantTexts($page)
        ];
    }

    private function extractImportantTexts(PageSnippet $page): array
    {
        $pageContent = Service::render($page);

        $contents = [];
        preg_match_all('/<h1[^>]*>(.*?)<\/h1>/i', $pageContent, $h1Matches);
        foreach ($h1Matches[1] as $h1Content) {
            $contents[] = strip_tags($h1Content);
        }

        // Match <h2> tags and extract their contents
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $pageContent, $h2Matches);
        foreach ($h2Matches[1] as $h2Content) {
            $contents[] = strip_tags($h2Content);
        }

        if (empty($contents)) {
            // Match <h3> tags and extract their contents
            preg_match_all('/<h3[^>]*>(.*?)<\/h3>/i', $pageContent, $h3Matches);
            foreach ($h3Matches[1] as $h3Content) {
                $contents[] = strip_tags($h3Content);
            }
        }

        if (empty($contents)) {
            // Match <h3> tags and extract their contents
            preg_match_all('/<h4[^>]*>(.*?)<\/h4>/i', $pageContent, $h4Matches);
            foreach ($h4Matches[1] as $h4Content) {
                $contents[] = strip_tags($h4Content);
            }
        }

        return array_filter($contents);
    }

    private function getPromptFromDataObjectContext(DataObject $object): array
    {
        $contents = [];

        $possibleFields = [
            'title',
            'name',
            'productName',
        ];

        foreach ($possibleFields as $field) {
            $method = 'get' . ucfirst($field);
            if (method_exists($object, $method)) {
                $contents[] = strip_tags($object->$method());
            }
        }

        return array_filter($contents);
    }
}
