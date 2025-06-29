<?php
/*
Plugin Name: WP Local Schema PRO
Description: Todo el Schema Local SEO, tienda, directorios, ratings, reviews y utilidades SEO local PRO.
Version: 2.0.0
Author: NETUP404 + Copilot
License: GPLv2 or later
*/

if (!defined('ABSPATH')) exit;

define('WP_LSP_PATH', plugin_dir_path(__FILE__));
define('WP_LSP_URL', plugin_dir_url(__FILE__));

require_once WP_LSP_PATH . 'includes/field-definitions.php';
require_once WP_LSP_PATH . 'includes/helpers.php';
require_once WP_LSP_PATH . 'includes/admin-panel.php';
require_once WP_LSP_PATH . 'includes/output-schema.php';
require_once WP_LSP_PATH . 'includes/save-options.php';


register_activation_hook(__FILE__, function() {
    if (!get_option('wp_local_schema_pro_options')) {
        add_option('wp_local_schema_pro_options', []);
    }
});
