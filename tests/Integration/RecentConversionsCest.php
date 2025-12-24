<?php
namespace Tests\Integration;

use RobertWP\WebPConverterLite\Admin\Services\RecentConversions;
use Tests\Support\IntegrationTester;


class RecentConversionsCest
{
    public function add_and_remove_records(IntegrationTester $I): void
    {
        $recent = RecentConversions::get_instance();

        $recent->add_record([
            'id' => 123,
            'webp_path' => '/tmp/test.webp'
        ]);

        $I->assertCount(1, $recent->get_records());

        $recent->remove_records_for_attachment(123);
        $I->assertEmpty($recent->get_records());
    }
}
