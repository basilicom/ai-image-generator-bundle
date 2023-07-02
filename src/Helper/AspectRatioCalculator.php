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


    public function calculateAspectRatio(string $aspectRatio): array
    {
        list($aspectWidth, $aspectHeight) = explode(':', $aspectRatio);

        /** These are optimal width and height */
        $width = 512;
        $height = 512;

        if ($width * $aspectHeight > $height * $aspectWidth) {
            $width = $height * $aspectWidth / $aspectHeight;
        } else {
            $height = $width * $aspectHeight / $aspectWidth;
        }

        return [
            'width' => $width,
            'height' => $height
        ];
    }
}
