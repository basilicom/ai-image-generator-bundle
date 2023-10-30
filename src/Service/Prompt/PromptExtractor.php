<?php

namespace Basilicom\AiImageGeneratorBundle\Service\Prompt;

use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Service;
use ReflectionClass;

class PromptExtractor
{
    public function createPromptFromPimcoreElement(PageSnippet|DataObject $element): string
    {
        if ($element instanceof PageSnippet) {
            $prompt = $this->getPromptFromDocumentContext($element);
        } elseif ($element instanceof DataObject) {
            $prompt = $this->getPromptFromDataObjectContext($element);
        }

        return !empty($prompt)
            ? $prompt
            : 'an inspiring market, emotional, camcorder effect';
    }

    private function getPromptFromDocumentContext(?PageSnippet $page): string
    {
        if (!$page) {
            return '';
        }

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

        return implode(',', [
            ...$prompts,
            ...$this->extractImportantTexts($page)
        ]);
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

    private function getPromptFromDataObjectContext(DataObject $object): string
    {
        $contents = [(new ReflectionClass($object))->getShortName()];
        $possibleFields = [
            'key',
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

        return implode(',', array_filter($contents));
    }
}
