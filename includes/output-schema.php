<?php
if (!defined('ABSPATH')) exit;

add_action('wp_head', function() {
    if (is_admin()) return;
    $options = get_option('wp_local_schema_pro_options');
    if (empty($options['schema_type'])) return;

    $schema_fields = wp_lsp_get_schema_fields();
    $type = $options['schema_type'];
    if (!isset($schema_fields[$type])) return;

    // Construye el array schema.org solo con los campos rellenados
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => $type,
    ];
    foreach ($schema_fields[$type] as $field_id => $field_def) {
        if (!empty($options[$field_id])) {
            $schema[$field_id] = $options[$field_id];
        }
    }
    // Imprime el JSON-LD
    echo "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>";
});