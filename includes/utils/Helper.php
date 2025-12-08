<?php
namespace RobertWP\WebPConverterLite\Utils;

class Helper
{

    public static function get_upgrade_url( $source = ''): string {

        $base_url = 'https://robertwp.com/rw-image-optimizer-pro/';
        if ( $source ) {
            return add_query_arg( 'source', $source, $base_url );
        }
        return $base_url;

    }

    public static function terminate(): void {
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            throw new \Exception('terminate called');
        }
        exit;
    }

}


