<?php
if (!defined('ABSPATH')) exit;

add_action('wp_head', function() {
    if (is_admin()) return;
    $options = get_option('wp_local_schema_pro_options');
    if (empty($options['schema_type_principal'])) return;

    $schema_fields = wp_lsp_get_schema_fields();
    $type_principal = $options['schema_type_principal'];

    // Recoger tipos secundarios, quitando el principal si estuviera repetido
    $sec_types = array_filter(
        isset($options['schema_types_secundarios']) && is_array($options['schema_types_secundarios'])
            ? $options['schema_types_secundarios']
            : [],
        fn($t) => $t !== $type_principal
    );

    // Construir el array schema.org solo con los campos rellenados
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => count($sec_types) ? array_merge([$type_principal], array_values($sec_types)) : $type_principal,
    ];

    // Rellenar los campos rellenados del principal
    if (isset($schema_fields[$type_principal])) {
        foreach ($schema_fields[$type_principal] as $field_id => $field_def) {
            if (!empty($options[$field_id])) {
                $schema[$field_id] = $options[$field_id];
            }
        }
    }

    // Añadir Wikipedia principal a sameAs
    if (!empty($options['wikipedia_url_principal'])) {
        if (isset($schema['sameAs'])) {
            if (is_array($schema['sameAs'])) {
                $schema['sameAs'][] = $options['wikipedia_url_principal'];
            } else {
                $schema['sameAs'] = [$schema['sameAs'], $options['wikipedia_url_principal']];
            }
        } else {
            $schema['sameAs'] = [$options['wikipedia_url_principal']];
        }
    }

    // Imprime el JSON-LD
    echo "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>";
});