<?php
if (!defined('ABSPATH')) exit;

// Hook de sanitización antes de guardar opciones
add_filter('pre_update_option_wp_local_schema_pro_options', function($new, $old) {
    // Limpia secundarios duplicados con el principal
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