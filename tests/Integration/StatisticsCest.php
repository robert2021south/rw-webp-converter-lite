<?php
namespace Tests\Integration;

use RobertWP\WebPConverterLite\Admin\Services\Statistics;
use Tests\Support\IntegrationTester;


class StatisticsCest
{
    public function global_stats_should_be_correct(IntegrationTester $I): void
    {
        $stats = Statistics::get_instance()->get_global_stats();

        $I->assertArrayHasKey('total_images', $stats);
        $I->assertArrayHasKey('converted_images', $stats);
        $I->assertArrayHasKey('space_saved', $stats);
    }
}
