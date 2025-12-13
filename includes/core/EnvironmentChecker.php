<?php
namespace RobertWP\WebPConverterLite\Core;

use Imagick;
use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class EnvironmentChecker {
    use Singleton;

    public static function maybe_show_image_library_notice(): void {

        $imagick_supported = false;
        $gd_supported = false;

        // Check Imagick support
        if (class_exists('Imagick') && extension_loaded('imagick')) {
            $formats = Imagick::queryFormats();
            if (is_array($formats) && in_array('WEBP', $formats, true)) {
                $imagick_supported = true;
            }
        }

        // Check GD support
        if (function_exists('gd_info')) {
            $gd_info = gd_info();
            if (!empty($gd_info['WebP Support'])) {
                $gd_supported = true;
            }
        }

        // If neither supports WebP, show error
        if (!$imagick_supported && !$gd_supported) {
            $type = 'error';
            $message = __(
                '<p><strong>RW WebP Converter Lite:</strong> Your server does not support WebP conversion.</p>
                 <p>You need <code>Imagick</code> (with WebP support) or <code>GD</code> (compiled with WebP support).</p>',
                'rw-webp-converter-lite'
            );

            TemplateLoader::load('partials/admin-notice-generic', [
                'message' => $message,
                'notice_type' => $type
            ]);
        }
    }
}
