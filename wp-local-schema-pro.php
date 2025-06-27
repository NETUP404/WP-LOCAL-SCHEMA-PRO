<?php
/*
Plugin Name: WP Local Schema PRO
Description: Todo el Schema Local SEO, tienda, directorios, ratings, reviews y utilidades SEO local PRO.
Version: 2.0.0
Author: NETUP404 + Copilot
Text Domain: wp-local-schema-pro
Domain Path: /languages
*/

defined('ABSPATH') || exit;

define( 'WP_LSP_PATH', plugin_dir_path( __FILE__ ) );

add_action('plugins_loaded', function () {
    load_plugin_textdomain('wp-local-schema-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// INCLUDES
require_once WP_LSP_PATH . 'includes/field-definitions.php';
require_once WP_LSP_PATH . 'includes/helpers.php';
require_once WP_LSP_PATH . 'includes/admin-panel.php';
require_once WP_LSP_PATH . 'includes/output-schema.php';