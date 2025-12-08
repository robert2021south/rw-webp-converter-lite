<?php
namespace RobertWP\WebPConverterLite\Core;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class EnvironmentChecker {
    use Singleton;

    public static function maybe_show_image_library_notice(): void {

        $has_imagick = class_exists('Imagick') && extension_loaded('imagick');
        $has_gd = function_exists('gd_info');

        if (!$has_imagick && !$has_gd) {
            $type = 'error';
            $message = __(
                '<p><strong>rw-webp-converter-lite:</strong> Your server has neither Imagick nor GD installed. Image optimization cannot work. Please install at least one of them.</p>',
                'rw-webp-converter-lite'
            );
            TemplateLoader::load('partials/admin-notice-generic', [
                'message' => $message,
                'notice_type' => $type
            ]);
        }
    }
}
