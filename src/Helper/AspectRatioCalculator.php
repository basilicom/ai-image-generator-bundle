<?php

namespace Basilicom\AiImageGeneratorBundle\Helper;

use Basilicom\AiImageGeneratorBundle\Model\AspectRatio;

class AspectRatioCalculator
{
    public const DEFAULT_ASPECT_RATIO = '16:9';

    public function isValidAspectRatio(string $aspectRatio): bool
    {
        return in_array($aspectRatio, [
            '16:9',
            '4:3',
            '3:2',
            '16:10',
            '5:4',
            '1:1',
            '21:9',
        ]);
    }

    public function getAspectRatioFromDimensions(int $width, int $height): string
    {
        if ($width <= 0 || $height <= 0) {
            return '1:1';
        }

        // Find the greatest common divisor using the Euclidean algorithm
        $gcd = $this->findGCD($width, $height);

        // Divide both width and height by the GCD to simplify the aspect ratio
        $aspectWidth = $width / $gcd;
        $aspectHeight = $height / $gcd;

        // Check for common aspect ratios
        $commonRatios = [
            [16, 9],
            [4, 3],
            [3, 2],
            [16, 10],
            [5, 4],
            [1, 1],
            [21, 9],
        ];

        $closestRatio = $commonRatios[0];
        $closestDifference = PHP_INT_MAX;

        foreach ($commonRatios as $ratio) {
            $difference = abs($aspectWidth / $aspectHeight - $ratio[0] / $ratio[1]);
            if ($difference < $closestDifference) {
                $closestRatio = $ratio;
                $closestDifference = $difference;
            }
        }

        // Return the closest common aspect ratio as a string
        return $closestRatio[0] . ':' . $closestRatio[1];
    }

    private function findGCD($a, $b): float|int
    {
        while ($b != 0) {
            $remainder = $a % $b;
            $a = $b;
            $b = $remainder;
        }

        return abs($a);
    }

    public function calculateAspectRatio(string $aspectRatio, int $modelBaseSize = 512, ?int $baseIncrement = null): AspectRatio
    {
        list($aspectWidth, $aspectHeight) = explode(':', $aspectRatio);

        $width = $modelBaseSize;
        $height = $modelBaseSize;

        if ($width * $aspectHeight > $height * $aspectWidth) {
            $width = $height * $aspectWidth / $aspectHeight;
        } else {
            $height = $width * $aspectHeight / $aspectWidth;
        }

        $width = floor($width);
        $height = floor($height);

        if ($baseIncrement === null) {
            return new AspectRatio($aspectRatio, $width, $height);
        }

        $width = $this->calculateClosestIncrementOfValue($width, $baseIncrement);
        $height = $this->calculateClosestIncrementOfValue($height, $baseIncrement);

        // Make sure the adjusted dimensions are not larger than 512
        $width = min($width, $modelBaseSize);
        $height = min($height, $modelBaseSize);

        return new AspectRatio($aspectRatio, $width, $height);
    }

    private function calculateClosestIncrementOfValue(int $value, int $increment): int
    {
        $remainder = $value % $increment;

        return $remainder > $increment / 2 ? $value + $increment - $remainder : $value - $remainder;
    }
}
