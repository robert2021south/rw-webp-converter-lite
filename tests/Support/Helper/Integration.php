<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;

class Integration extends Module
{
    /** @var string 上传目录基路径 */
    public string $uploadsTestDir;

    /**
     * 单个测试用例开始前执行
     * 确保上传目录隔离、存在，并注册上传目录过滤器
     */
    public function _before(\Codeception\Module|\Codeception\TestInterface $module = null): void
    {
        $this->uploadsTestDir = WP_CONTENT_DIR . '/uploads-test';

        // 确保基目录存在
        if (!is_dir($this->uploadsTestDir)) {
            mkdir($this->uploadsTestDir, 0777, true);
        }

        // 注册过滤器，将 WordPress 上传目录重定向到 uploads-test
        add_filter('upload_dir', function ($dirs) {
            $base = $this->uploadsTestDir;

            $dirs['basedir'] = $base;
            $dirs['baseurl'] = content_url('/uploads-test');

            $dirs['path'] = $base . $dirs['subdir'];
            $dirs['url']  = $dirs['baseurl'] . $dirs['subdir'];

            // 确保子目录存在
            if (!is_dir($dirs['path'])) {
                mkdir($dirs['path'], 0777, true);
            }

            return $dirs;
        });
    }

    /**
     * 测试套件结束后执行
     * 安全删除整个 uploads-test 目录
     */
    public function _afterSuite(): void
    {
        $dir = WP_CONTENT_DIR . '/uploads-test';

        if (is_dir($dir)) {
            // CI / 本地都安全
            exec('rm -rf ' . escapeshellarg($dir));
        }
    }
}
