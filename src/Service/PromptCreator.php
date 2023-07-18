<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Service;
use ReflectionClass;

class PromptCreator
{
    public const DEFAULT_NEGATIVE_PROMPT = '(((nsfw, nude, naked))), (semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck';

    public function createPromptFromPimcoreElement(PageSnippet|DataObject $element): array
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
                (new ReflectionClass($element))->getShortName(),
                ...$this->getPromptFromDataObjectContext($element)
            ];
        }

        if (empty($promptParts)) {
            $promptParts = ['an inspiring market, emotional, camcorder effect'];
        }

        $promptParts[] = '(best quality, masterpiece)';

        return [
            ...$promptParts,
            '8k, uhd, soft lighting, high quality',
        ];
    }

    private function getPromptFromDocumentContext(?PageSnippet $page): array
    {
        if (!$page) {
            return [];
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
            'description',
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
