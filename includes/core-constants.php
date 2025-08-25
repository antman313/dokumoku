<?php if (!defined('ABSPATH')) exit;

define('DOKUMOKU_PATH', plugin_dir_path(dirname(__FILE__))); // /plugin-root/
define('DOKUMOKU_URL',  plugin_dir_url(dirname(__FILE__)));
define('DOKUMOKU_DOCS_DIR', DOKUMOKU_PATH . 'docs');

function dokumoku_capability() { return apply_filters('dokumoku_capability', 'manage_options'); }

function dokumoku_menu_icon() {
	$svg = @file_get_contents(DOKUMOKU_PATH.'assets/icon.svg');
	return $svg ? 'data:image/svg+xml;base64,'.base64_encode($svg) : 'dashicons-media-document';
}