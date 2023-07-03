<?php

namespace Basilicom\AiImageGeneratorBundle\Helper;

class AspectRatioCalculator
{
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


    public function calculateAspectRatio(string $aspectRatio, ?int $baseIncrement = null): array
    {
        list($aspectWidth, $aspectHeight) = explode(':', $aspectRatio);

        $width = 512;
        $height = 512;

        if ($width * $aspectHeight > $height * $aspectWidth) {
            $width = $height * $aspectWidth / $aspectHeight;
        } else {
            $height = $width * $aspectHeight / $aspectWidth;
        }

        $width = floor($width);
        $height = floor($height);

        if ($baseIncrement === null) {
            return [
                'width' => $width,
                'height' => $height,
            ];
        }

        $width = $this->getClosestIncrement($width, $baseIncrement);
        $height = $this->getClosestIncrement($height, $baseIncrement);

        // Make sure the adjusted dimensions are not larger than 512
        $width = min($width, 512);
        $height = min($height, 512);

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    private function getClosestIncrement(int $value, int $increment): int
    {
        // Calculate the closest increment of a value
        $remainder = $value % $increment;

        return $remainder > $increment / 2 ? $value + $increment - $remainder : $value - $remainder;
    }
}
