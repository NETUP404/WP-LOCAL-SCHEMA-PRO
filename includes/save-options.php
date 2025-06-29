<?php
if (!defined('ABSPATH')) exit;

// Hook de sanitización antes de guardar opciones
add_filter('pre_update_option_wp_local_schema_pro_options', function($new, $old) {
    // Procesar tipos secundarios con páginas asociadas
    if (!empty($new['schema_types_secundarios_v2']) && is_array($new['schema_types_secundarios_v2'])) {
        $cleaned_secondary_types = [];
        
        foreach ($new['schema_types_secundarios_v2'] as $sec_data) {
            // Solo incluir si tiene un tipo seleccionado
            if (!empty($sec_data['type'])) {
                $cleaned_secondary_types[] = [
                    'type' => sanitize_text_field($sec_data['type']),
                    'page_url' => esc_url_raw($sec_data['page_url'] ?? '')
                ];
            }
        }
        
        $new['schema_types_secundarios_v2'] = $cleaned_secondary_types;
        
        // Limpiar datos antiguos para mantener compatibilidad
        $principal = $new['schema_type_principal'] ?? '';
        $legacy_types = [];
        foreach ($cleaned_secondary_types as $sec_data) {
            if ($sec_data['type'] && $sec_data['type'] !== $principal) {
                $legacy_types[] = $sec_data['type'];
            }
        }
        $new['schema_types_secundarios'] = array_values(array_unique($legacy_types));
    }
    
    // Limpia secundarios duplicados con el principal (compatibilidad)
    if (!empty($new['schema_type_principal'])) {
        $principal = $new['schema_type_principal'];
        if (!empty($new['schema_types_secundarios']) && is_array($new['schema_types_secundarios'])) {
            $new['schema_types_secundarios'] = array_values(array_unique(
                array_filter($new['schema_types_secundarios'], function($type) use ($principal) {
                    return $type && $type !== $principal;
                })
            ));
        }
    }
    
    return $new;
}, 10, 2);