<?php

namespace Tests\Unit\Service\Brand;

use Basilicom\AiImageGeneratorBundle\Service\Brand\ColorService;
use PHPUnit\Framework\TestCase;

class ColorServiceTest extends TestCase
{
    public static function getColorNameDataProvider(): array
    {
        return [
            ['#000', 'black'],
            ['#000000', 'black'],
            ['#0000ff', 'blue'],
        ];
    }

    /**
     * @test
     * @dataProvider getColorNameDataProvider
     */
    public function getColorName(string $color, string $expectedResult)
    {
        // prepare
        $classUnderTest = new ColorService();

        // test
        $result = $classUnderTest->getColorName($color);

        // verify
        $this->assertEquals($expectedResult, $result);
    }
}
