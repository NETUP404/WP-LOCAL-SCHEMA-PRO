<?php
if (!defined('ABSPATH')) exit;

add_action('wp_head', function() {
    if (is_admin()) return;
    $options = get_option('wp_local_schema_pro_options');
    if (empty($options['schema_type_principal'])) return;

    $schema_fields = wp_lsp_get_schema_fields();
    $type_principal = $options['schema_type_principal'];
    
    // Obtener URL actual
    $current_url = '';
    if (is_page() || is_single()) {
        $current_url = get_permalink();
    }
    
    // Verificar si hay tipos secundarios asociados a la página actual
    $matching_secondary_types = [];
    if (!empty($options['schema_types_secundarios_v2']) && is_array($options['schema_types_secundarios_v2'])) {
        foreach ($options['schema_types_secundarios_v2'] as $sec_data) {
            if (!empty($sec_data['type']) && !empty($sec_data['page_url']) && $sec_data['page_url'] === $current_url) {
                $matching_secondary_types[] = $sec_data['type'];
            }
        }
    }
    
    // Determinar qué schemas mostrar
    if (!empty($matching_secondary_types)) {
        // Mostrar solo schemas secundarios que coinciden con la página actual
        $types_to_show = array_unique(array_filter($matching_secondary_types, fn($t) => $t !== $type_principal));
    } else {
        // Mostrar solo el schema principal (sin secundarios)
        $types_to_show = [];
    }
    
    // Construir el array schema.org
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => count($types_to_show) > 0 ? 
            (count($types_to_show) === 1 ? $types_to_show[0] : array_values($types_to_show)) : 
            $type_principal,
    ];

    // Si no hay tipos secundarios coincidentes, rellenar campos del principal
    if (empty($matching_secondary_types)) {
        // Rellenar los campos del principal
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
    } else {
        // Para tipos secundarios, rellenar solo los campos específicos de esos tipos
        foreach ($types_to_show as $secondary_type) {
            if (isset($schema_fields[$secondary_type])) {
                foreach ($schema_fields[$secondary_type] as $field_id => $field_def) {
                    if (!empty($options[$field_id])) {
                        $schema[$field_id] = $options[$field_id];
                    }
                }
            }
        }
    }

    // Imprime el JSON-LD
    echo "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>";
});