<?php
namespace tests\Acceptance;

use Tests\Support\AcceptanceTester;


class BulkConvertHappyPathCest
{
    /**
     * 在每个测试前准备图片附件
     */
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();

        // 插件配置
        $I->haveOptionInDatabase('rwwcl_settings', [
            'auto_optimize'   => 0,
            'overwrite_webp'  => 1,
            'keep_original'   => 1,
            'skip_small'      => 0,
            'webp_quality'    => 80,
        ]);

        // 1️⃣ 打开媒体上传页
        $I->amOnAdminPage('media-new.php');

        $images = [
            '300x300_20250618_083505.png',
            '715f1c6064b670b7bde8c3324116bead.jpeg',
            'coutu1.png',
            'coutu2.png',
            'Image_2025-08-13_125222_631.jpg',
            'Image_2025-08-13_125236_895.jpg',
            'Image_2025-08-13_125248_055.jpg',
            'Image_2025-08-13_125402_488.jpg',
            'Image_2025-08-13_131604_701.jpg',
        ];

        foreach ($images as $file) {
            $I->attachFile('input[type="file"]', 'images/' . $file);
        }

        // 等媒体库处理完成
        $I->waitForElement('//div[contains(@class,"media-item")]//div[contains(text(),"Image_2025-08-13_131604_701.jpg")]', 30);

    }


    /**
     * Bulk Converter happy path
     */
    public function bulk_convert_generates_webp(AcceptanceTester $I): void
    {
        // 1. 打开 Bulk Converter 页面
        $I->amOnAdminPage('tools.php?page=rwwcl-main&tab=bulk');

        // 2. 页面加载完成
        $I->waitForElementVisible( '#rwwcl-start-bulk',30);

        // 3. 点击“开始转换”
        $I->click('#rwwcl-start-bulk');

        // 4. 等待转换完成（Bulk 通常是 AJAX）
        //$I->waitForText('Completed', 30);
        //$I->waitForElement('#rwwcl-bulk-progress', 10); // 等待进度条出现
        // 等待按钮被禁用（确认流程已启动）
        $I->waitForJs(
            'var btn = document.querySelector("#rwwcl-start-bulk"); return btn && btn.disabled === true;',
            30
        );

        // 等待按钮恢复可点击（确认流程已结束）
        $I->waitForJs(
            'var btn = document.querySelector("#rwwcl-start-bulk"); return btn && btn.disabled === false;',
            300
        );
        //$I->waitForJs('var btn = document.querySelector("#rwwcl-start-bulk"); return btn && !btn.disabled;', 300);

        $I->seeElement('.rwwcl-progress-text');
        $I->see('Completed', '.rwwcl-progress-text');

        // 5. 跳转到状态页 / 结果页
        $I->amOnAdminPage('tools.php?page=rwwcl-main&tab=status');

        // 6. 断言至少有一条图片被转换
        $I->see('300x300_20250618_083505', '.rwwcl-status-table tbody tr span');

        // 7. 断言 WebP 链接存在
        $I->see('WebP', '.rwwcl-status-table');
    }

    /**
     * 简单 MIME 判断（E2E 够用）
     */
    private function guessMimeType(string $filename): string
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }
}
