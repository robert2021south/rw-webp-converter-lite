<?php
/*
 *
 * */
namespace RobertWP\ImageOptimizerLite\Core;

use RobertWP\ImageOptimizerLite\Modules\Cleaner\Cleaner;
use RobertWP\ImageOptimizerLite\Modules\Export\PostViewsExporter;
use RobertWP\ImageOptimizerLite\Modules\PostColumn\PostViewsColumn;
use RobertWP\ImageOptimizerLite\Modules\RestApi\RestApi;
use RobertWP\ImageOptimizerLite\Modules\Shortcode\ShortcodeHandler;
use RobertWP\ImageOptimizerLite\Modules\Sort\Sort;
use RobertWP\ImageOptimizerLite\Modules\tracker\Tracker;

class Loader {

    public static function load_features(): void
    {
        new Tracker();
        new ShortcodeHandler();
        new PostViewsColumn();

        PostViewsExporter::get_instance();
        Sort::get_instance();
        Cleaner::get_instance();
        RestApi::get_instance();
    }

}
