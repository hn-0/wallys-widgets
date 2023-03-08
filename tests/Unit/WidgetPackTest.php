<?php

namespace Tests\Feature;

use App\Services\WidgetService;
use Tests\TestCase;

class WidgetPackTest extends TestCase
{
    /**
     * @dataProvider provideWidgetData
     */
    public function testWidget($input, $expectedResult): void
    {
        $this->assertEquals($expectedResult, WidgetService::getMinimumPacksRequired($input));
    }

    public function provideWidgetData()
    {
        return [
            [
                1,
                [250 => 1]
            ],
            [
                250,
                [250 => 1]
            ],
            [
                251,
                [500 => 1]
            ],
            [
                501,
                [250 => 1, 500 => 1]
            ],
            [
                12001,
                [250 => 1, 2000 => 1, 5000 => 2]
            ],
            // Extra
            [
                1234567,
                [250 => 1, 500 => 1, 2000 => 2, 5000 => 246]
            ],
        ];
    }
}
