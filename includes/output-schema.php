<?php
if (!defined('ABSPATH')) exit;

add_action('wp_head', function() {
    if (is_admin()) return;
    $options = get_option('wp_local_schema_pro_options');
    if (empty($options)) return;
    
    // Check if using new entity structure or migrate from old
    $migrated_options = wp_lsp_migrate_to_entity_structure($options);
    
    if (empty($migrated_options['main_entity']['type'])) return;
    
    $schema_fields = wp_lsp_get_schema_fields();
    $schemas = [];
    
    // Generate main entity schema
    $main_entity = $migrated_options['main_entity'];
    $main_schema = wp_lsp_build_entity_schema($main_entity, $schema_fields);
    if (!empty($main_schema)) {
        $schemas[] = $main_schema;
    }
    
    // Generate secondary entity schemas
    if (!empty($migrated_options['secondary_entities'])) {
        foreach ($migrated_options['secondary_entities'] as $entity) {
            $entity_schema = wp_lsp_build_entity_schema($entity, $schema_fields);
            if (!empty($entity_schema)) {
                $schemas[] = $entity_schema;
            }
        }
    }
    
    // Output all schemas
    foreach ($schemas as $schema) {
        echo "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>";
    }
});

function wp_lsp_build_entity_schema($entity, $schema_fields) {
    if (empty($entity['type']) || empty($entity['fields'])) {
        return null;
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $entity['type'],
    ];
    
    $entity_type = $entity['type'];
    $fields = $entity['fields'];
    
    // Add fields that have values
    if (isset($schema_fields[$entity_type])) {
        foreach ($schema_fields[$entity_type] as $field_id => $field_def) {
            if (!empty($fields[$field_id])) {
                $schema[$field_id] = $fields[$field_id];
            }
        }
    }
    
    // For Store entities, also check Store-specific fields
    if ($entity_type === 'Store' && isset($schema_fields['Store'])) {
        foreach ($schema_fields['Store'] as $field_id => $field_def) {
            if (!empty($fields[$field_id])) {
                $schema[$field_id] = $fields[$field_id];
            }
        }
    }
    
    // Add Wikipedia URL to sameAs if present
    if (!empty($fields['wikipedia_url'])) {
        if (isset($schema['sameAs'])) {
            if (is_array($schema['sameAs'])) {
                $schema['sameAs'][] = $fields['wikipedia_url'];
            } else {
                $schema['sameAs'] = [$schema['sameAs'], $fields['wikipedia_url']];
            }
        } else {
            $schema['sameAs'] = [$fields['wikipedia_url']];
        }
    }
    
    return $schema;
}