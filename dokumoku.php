<?php
/*
Plugin Name: DokuMoku – Markdown-Dokumentation im WP-Admin
Description: Mehrere Markdown-Dokus (Sets) direkt im WP-Admin lesen & verlinken. Link-Rewrite für .md, helles UI, Header/Footer.
Author: codekeks.de & Andreas Grzybowski
Version: 0.1.0
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
License: GPLv2 or later
Text Domain: dokumoku
*/

if (!defined('ABSPATH')) exit;

/** 1) Konstanten & Core-Utils */
require_once __DIR__ . '/includes/core-constants.php';
require_once __DIR__ . '/includes/core-utils.php';

/** 2) Subsysteme */
require_once __DIR__ . '/includes/fs-storage.php';
require_once __DIR__ . '/includes/md-renderer.php';
require_once __DIR__ . '/includes/admin-assets.php';
require_once __DIR__ . '/includes/admin-actions.php';
require_once __DIR__ . '/includes/admin-page.php';
require_once __DIR__ . '/includes/shortcode.php';

/** 3) Menü registrieren */
add_action('admin_menu', function(){
    add_menu_page(
        __('DokuMoku','dokumoku'),
        __('DokuMoku','dokumoku'),
        dokumoku_capability(),
        'dokumoku',
        'dokumoku_admin_page',
        dokumoku_menu_icon(),
        58
    );
});