<?php
if (!defined('ABSPATH')) exit;

function wp_lsp_render_field($field_id, $field_def, $value = '') {
    $type = $field_def['type'] ?? 'text';
    $label = $field_def['label'] ?? $field_id;
    $help = $field_def['help'] ?? '';
    $attributes = $field_def['attributes'] ?? '';
    $options = $field_def['options'] ?? [];
    $html = '';

    $help_html = $help ? "<span class='wp-lsp-help' title='" . esc_attr($help) . "'>ⓘ</span>" : '';

    switch ($type) {
        case 'text':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<input type='text' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' $attributes />";
            break;
        case 'textarea':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<textarea id='$field_id' name='wp_local_schema_pro_options[$field_id]' $attributes >" . esc_textarea($value) . "</textarea>";
            break;
        case 'url':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<input type='url' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' $attributes />";
            break;
        case 'number':
            // Para ratings, limitar de 0 a 5 y permitir decimales
            $is_rating = strpos(strtolower($field_id), 'ratingvalue') !== false;
            $attrs = $attributes;
            if ($is_rating) $attrs .= ' min="0" max="5" step="0.1"';
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<input type='number' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' $attrs />";
            break;
        case 'select':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<select id='$field_id' name='wp_local_schema_pro_options[$field_id]' $attributes >";
            foreach ($options as $k => $v) {
                $sel = ($value == $k) ? "selected" : "";
                $html .= "<option value='$k' $sel>$v</option>";
            }
            $html .= "</select>";
            break;
        case 'image':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<div class='wp-lsp-image-wrap' style='display:flex;align-items:center;gap:8px'>";
            $html .= "<input type='text' class='wp-lsp-image-url' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' placeholder='URL imagen' $attributes />";
            $img_url = esc_attr($value);
            $html .= "<img src='" . ($img_url ? $img_url : '') . "' class='wp-lsp-img-preview' style='max-width:42px;max-height:42px;display:" . ($img_url ? "block" : "none") . ";border:1px solid #ccc;border-radius:4px;' alt='Previsualización'>";
            $html .= "</div>";
            break;
        case 'multitext':
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<textarea id='$field_id' name='wp_local_schema_pro_options[$field_id]' placeholder='Uno por línea' $attributes >" . esc_textarea($value) . "</textarea>";
            break;
        case 'group':
            $html .= "<fieldset><legend>$label $help_html</legend>";
            foreach (($field_def['fields'] ?? []) as $sub_id => $subdef) {
                $subval = $value[$sub_id] ?? '';
                $html .= wp_lsp_render_field($field_id . "[$sub_id]", $subdef, $subval);
            }
            $html .= "</fieldset>";
            break;
        case 'repeater':
            // Repeaters con tabla + formulario de edición/añadir
            $fields = $field_def['fields'] ?? [];
            $html .= "<label>$label $help_html</label>";
            $html .= "<div class='wp-lsp-repeater' data-field='$field_id'>";
            // Render tabla de items
            $html .= "<table class='wp-lsp-repeater-table'><thead><tr>";
            foreach ($fields as $sub_id => $subdef) {
                $html .= "<th>" . esc_html($subdef['label'] ?? $sub_id) . "</th>";
            }
            $html .= "<th>Acciones</th></tr></thead><tbody>";
            if (is_array($value) && count($value)) {
                foreach ($value as $idx => $row) {
                    $html .= "<tr class='wp-lsp-repeater-item' data-idx='$idx'>";
                    foreach ($fields as $sub_id => $subdef) {
                        $subval = $row[$sub_id] ?? '';
                        $html .= "<td>" . esc_html(is_array($subval) ? json_encode($subval) : $subval) . "</td>";
                    }
                    $html .= "<td>
                        <button type='button' class='button wp-lsp-edit-item'>Editar</button>
                        <button type='button' class='button wp-lsp-remove-item'>Eliminar</button>
                    </td>";
                    $html .= "</tr>";
                }
            }
            $html .= "</tbody></table>";
            // Formulario añadir/editar
            $html .= "<div class='wp-lsp-repeater-form' style='margin-top:10px'>";
            foreach ($fields as $sub_id => $subdef) {
                $html .= wp_lsp_render_field("_TMP_[$sub_id]", $subdef, '');
            }
            $html .= "<input type='hidden' class='wp-lsp-repeater-edit-idx' value=''>";
            $html .= "<button type='button' class='button wp-lsp-add-item'>Añadir</button>";
            $html .= "<button type='button' class='button wp-lsp-save-item' style='display:none'>Guardar</button>";
            $html .= "<button type='button' class='button wp-lsp-cancel-edit' style='display:none'>Cancelar</button>";
            $html .= "<span class='wp-lsp-repeater-msg'></span>";
            $html .= "</div>";
            $html .= "</div>";
            break;
        case 'time':
            // Selector de hora respetando formato WP
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<input type='time' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' $attributes />";
            break;
        case 'date':
            // Selector de fecha respetando formato WP
            $html .= "<label for='$field_id'>$label $help_html</label>";
            $html .= "<input type='date' id='$field_id' name='wp_local_schema_pro_options[$field_id]' value='" . esc_attr($value) . "' $attributes />";
            break;
    }
    return "<div class='wp-lsp-field wp-lsp-type-$type'>$html</div>";
}