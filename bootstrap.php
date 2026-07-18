<?php

// Exits if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Module bootstrap.
 */
require_once __DIR__ . '/infrastructure/annual-leave.php';
require_once __DIR__ . '/infrastructure/substitute.php';
require_once __DIR__ . '/application/annual-leave.php';
