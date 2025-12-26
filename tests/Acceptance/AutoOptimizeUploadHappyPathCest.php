<?php
namespace tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Happy Path:
 * 管理员上传一张 JPEG 图片
 * → AutoOptimizer 自动生成 WebP
 */
class AutoOptimizeUploadHappyPathCest
{

    public function _before(AcceptanceTester $I): void
    {
        // 登录管理员
        $I->loginAsAdmin();

        // 确保插件设置
        $I->haveOptionInDatabase('rwwcl_settings', [
            'auto_optimize'   => 1,
            'overwrite_webp'  => 1,
            'keep_original'   => 1,
            'skip_small'      => 300,
            'webp_quality'    => 80,
        ]);

        $I->amOnAdminPage('index.php');
        $I->see('Dashboard');

        $attachments = $I->grabColumnFromDatabase('wp_posts', 'ID', ['post_type' => 'attachment']);
        foreach ($attachments as $id) {
            wp_delete_attachment($id, true);
        }

    }

    /**
     * Happy Path:
     * 管理员在「媒体 → 添加」页面上传 JPEG
     * → AutoOptimizer 自动生成 WebP
     */
    public function upload_image_generates_webp(AcceptanceTester $I): void
    {
        // ---------- 1. 打开媒体上传页面 ----------
        $I->amOnAdminPage('media-new.php');
        $I->waitForText('Drop files to upload', 15);

        // ---------- 2. 上传文件 ----------
        // WordPress 媒体上传 input
        $testImage = 'images/Image_2025-08-13_125222_631.jpg';
        $I->attachFile('input[type="file"]', $testImage);

        // 给 JS 一点时间（仅用于调试）
        $I->wait(10);

        //codecept_debug($I->grabPageSource());

        $I->dontSee('has failed to upload');

        // ---------- 3. 等待上传完成 ----------
        // 上传成功后会出现“编辑”链接
        $I->waitForElement('.media-item', 30);
        $I->waitForElement('.edit-attachment', 30);

        // ---------- 4. 获取最新 attachment ID ----------
        $attachmentId = (int) $I->grabFromDatabase('wp_posts', 'ID', ['post_type' => 'attachment',], 'ORDER BY ID DESC');

        // ---------- 5. 断言 WebP 文件存在 ----------
        // 注意：E2E 允许直接调用 WP 函数（wpbrowser 注入）
        //$uploadDir   = wp_upload_dir();
        $original    = get_attached_file($attachmentId);
        $webpPath   = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original);

        $I->assertFileExists($webpPath, 'WebP file should be generated automatically after upload');

        // ---------- 6.验证最近转换 UI ----------
        $I->amOnAdminPage('tools.php?page=rwwcl-main'); // 你的插件主页面 slug
        $I->waitForText('Recent Conversions', 15);

        // 表格存在
        $I->seeElement('.rwwcl-status-table');

        // 至少一条转换记录
        $I->seeElement('.rwwcl-status-table tbody tr');

        // WebP 文件已生成（核心断言）
        $I->seeElement(
            '.rwwcl-status-table tbody tr a[href$=".webp"]'
        );

        // 文件名显示正确（非必须，但可加）
        $I->see('Image_2025-08-13_125222_631', '.rwwcl-status-table tbody tr span');

    }

}