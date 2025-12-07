<?php
namespace RobertWP\ImageOptimizerLite\Core;

use RobertWP\ImageOptimizerLite\Traits\Singleton;
use RobertWP\ImageOptimizerLite\Utils\TemplateLoader;

class EnvironmentChecker {
    use Singleton;

    public static function maybe_show_image_library_notice(): void {

        $has_imagick = class_exists('Imagick') && extension_loaded('imagick');
        $has_gd = function_exists('gd_info');

        if (!$has_imagick && !$has_gd) {
            $type = 'error';
            $message = __(
                '<p><strong>rw-image-optimizer-lite:</strong> Your server has neither Imagick nor GD installed. Image optimization cannot work. Please install at least one of them.</p>',
                'rw-image-optimizer-lite'
            );
            TemplateLoader::load('partials/admin-notice-generic', [
                'message' => $message,
                'notice_type' => $type
            ]);
        }
    }
}
