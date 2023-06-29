<?php

namespace Basilicom\AiImageGeneratorBundle\Helper;

class AspectRatioCalculator
{
    public static function calculateAspectRatio(string $aspectRatio): array
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
