<?php
if (!defined('ABSPATH')) exit;

// Hook de sanitización antes de guardar opciones
add_filter('pre_update_option_wp_local_schema_pro_options', function($new, $old) {
    // Convert old structure to new structure if needed (migration)
    $migrated = wp_lsp_migrate_to_entity_structure($new);
    
    // Ensure proper structure for secondary entities
    if (!isset($migrated['secondary_entities'])) {
        $migrated['secondary_entities'] = [];
    }
    
    // Ensure externalUrls is in global scope
    if (!isset($migrated['global'])) {
        $migrated['global'] = [];
    }
    if (!isset($migrated['global']['externalUrls'])) {
        $migrated['global']['externalUrls'] = [];
    }
    
    return $migrated;
}, 10, 2);

// Helper function to migrate old data structure to new entity-based structure
function wp_lsp_migrate_to_entity_structure($options) {
    // If already in new structure, return as-is
    if (isset($options['main_entity']) && isset($options['secondary_entities'])) {
        return $options;
    }
    
    $migrated = [
        'enabled' => $options['enabled'] ?? true,
        'main_entity' => [
            'type' => $options['schema_type_principal'] ?? 'LocalBusiness',
            'fields' => []
        ],
        'secondary_entities' => [],
        'global' => [
            'externalUrls' => $options['externalUrls'] ?? []
        ]
    ];
    
    // Extract main entity fields (exclude store-specific fields)
    $schema_fields = wp_lsp_get_schema_fields();
    $store_fields = array_keys($schema_fields['Store'] ?? []);
    
    foreach ($options as $key => $value) {
        // Skip meta fields
        if (in_array($key, ['enabled', 'schema_type_principal', 'schema_types_secundarios', 'externalUrls'])) {
            continue;
        }
        
        // Skip store-specific fields for main entity
        if (strpos($key, 'store') === 0 || in_array($key, $store_fields)) {
            continue;
        }
        
        // Add to main entity if not empty
        if (!empty($value)) {
            $migrated['main_entity']['fields'][$key] = $value;
        }
    }
    
    // Convert old secondary types to new entity structure (if any exist as Store)
    if (!empty($options['schema_types_secundarios']) && is_array($options['schema_types_secundarios'])) {
        foreach ($options['schema_types_secundarios'] as $index => $type) {
            if ($type === 'Store') {
                // Create Store entity from store fields
                $store_entity = [
                    'id' => 'store_' . time() . '_' . $index,
                    'type' => 'Store',
                    'fields' => []
                ];
                
                // Extract store fields
                foreach ($options as $key => $value) {
                    if ((strpos($key, 'store') === 0 || in_array($key, $store_fields)) && !empty($value)) {
                        $clean_key = str_replace('store', '', $key);
                        $clean_key = lcfirst($clean_key);
                        if ($clean_key === '') $clean_key = $key;
                        $store_entity['fields'][$clean_key] = $value;
                    }
                }
                
                $migrated['secondary_entities'][] = $store_entity;
            }
        }
    }
    
    return $migrated;
}