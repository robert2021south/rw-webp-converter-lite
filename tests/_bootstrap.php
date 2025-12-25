<?php

declare(strict_types=1);

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

const WP_ENV = 'testing';

// Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/autoload.php';
