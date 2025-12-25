<?php
namespace Tests\Integration;

use RobertWP\WebPConverterLite\Utils\Helper;
use Tests\Support\IntegrationTester;

class HelperImageCompressionTextCest
{
    /**
     * 原始大小为 0，返回 N/A
     */
    public function returnsNAWhenOriginalSizeIsZero(IntegrationTester $I): void
    {
        $result = Helper::image_compression_text(0, 100);

        $I->assertSame('N/A', $result);
    }

    /**
     * 优化后大小为负数，返回 N/A
     */
    public function returnsNAWhenOptimizedSizeIsNegative(IntegrationTester $I): void
    {
        $result = Helper::image_compression_text(1000, -1);

        $I->assertSame('N/A', $result);
    }

    /**
     * 正常压缩（体积变小）
     */
    public function positiveCompressionRate(IntegrationTester $I): void
    {
        // 1000 -> 500，减少 500 字节，50%
        $result = Helper::image_compression_text(1000, 500);

        $I->assertSame('500.00 B (+50%)', $result);
    }

    /**
     * 体积变大（负压缩率）
     */
    public function negativeCompressionRate(IntegrationTester $I): void
    {
        // 500 -> 800，增加 300 字节，-60%
        $result = Helper::image_compression_text(500, 800);

        $I->assertSame('300.00 B (-60%)', $result);
    }

    /**
     * 接近 0% 的情况，应该显示 0%
     */
    public function nearZeroCompressionRate(IntegrationTester $I): void
    {
        // 压缩率约 0.1%，应被视为 0
        $result = Helper::image_compression_text(1000, 999);

        $I->assertSame('1.00 B (+0.1%)', $result);
    }

    /**
     * 自定义精度
     */
    public function customPrecision(IntegrationTester $I): void
    {
        // 1000 -> 333 ≈ 66.7%
        $result = Helper::image_compression_text(1000, 333, 2);

        $I->assertSame('667.00 B (+66.7%)', $result);
    }
}