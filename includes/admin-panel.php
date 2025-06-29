<?php
add_action('admin_menu', function() {
    add_menu_page(
        'WP Local Schema PRO',
        'WP Local Schema PRO',
        'manage_options',
        'wp-local-schema-pro',
        'wp_lsp_render_admin_panel',
        'dashicons-location',
        35
    );
});
if (!defined('ABSPATH')) exit;

// Cargar todos los types permitidos de LocalBusiness y subtypes
require_once WP_LSP_PATH . 'includes/all-schema-types.php';

// --- YOAST SEO ORGANIZATION AUTO-IMPORT & WARNING BLOCK ---
function wp_lsp_check_yoast_organization_block(&$options) {
    $yoast = get_option('wpseo_titles');
    if (!$yoast || !is_array($yoast)) return;

    $has_org = !empty($yoast['company_name']) || !empty($yoast['company_logo']);
    if (!$has_org) return;

    if (!empty($_POST['wp_lsp_import_yoast_org'])) {
        if (!empty($yoast['company_name']) && empty($options['name']))  $options['name'] = $yoast['company_name'];
        if (!empty($yoast['company_logo']) && empty($options['logo']))  $options['logo'] = $yoast['company_logo'];
        if (!empty($yoast['company_url']) && empty($options['url']))    $options['url']  = $yoast['company_url'];
        update_option('wp_local_schema_pro_options', $options);
        echo '<div class="notice notice-success"><p>Datos de Yoast importados correctamente. Recuerda desactivar el schema de Organización en Yoast SEO para evitar duplicidad.</p></div>';
    }
    ?>
    <div class="notice notice-warning" style="margin:20px 0;">
        <p>
            <b>¡Atención!</b> Se han detectado datos de empresa en <b>Yoast SEO</b>.<br>
            Puedes <b>importar automáticamente</b> nombre, logo y URL aquí.<br>
            <form method="post" style="display:inline;">
                <input type="hidden" name="wp_lsp_import_yoast_org" value="1">
                <button type="submit" class="button-primary">Importar datos de Yoast SEO</button>
            </form>
        </p>
        <p style="margin-top:8px;color:#a77a10;">Tras importar, ve a Yoast &gt; Ajustes &gt; Empresa y elimina o desactiva la Organización en el schema para evitar conflictos.</p>
    </div>
    <?php
}

// === BLOQUE HORARIO UNIVERSAL ===
function wp_lsp_render_hours_block($prefix, $options) {
    $days = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    $opening = $options[$prefix.'openingHoursSpecification'] ?? [];
    ?>
    <div>
        <label><b>Horario habitual</b></label>
        <table class="wp-lsp-hours-table">
            <?php foreach($days as $idx => $day) {
                $o = $opening[$idx]['opens'] ?? '';
                $c = $opening[$idx]['closes'] ?? '';
                $closed = empty($o) && empty($c);
                ?>
                <tr>
                    <td><?php echo $day; ?></td>
                    <td>
                        <input type="time" name="wp_local_schema_pro_options[<?php echo $prefix; ?>openingHoursSpecification][<?php echo $idx; ?>][opens]" value="<?php echo esc_attr($o); ?>" <?php if($closed) echo 'disabled'; ?>>
                        -
                        <input type="time" name="wp_local_schema_pro_options[<?php echo $prefix; ?>openingHoursSpecification][<?php echo $idx; ?>][closes]" value="<?php echo esc_attr($c); ?>" <?php if($closed) echo 'disabled'; ?>>
                        <label style="margin-left:10px;">
                            <span class="wp-lsp-switch" title="Cerrado este día">
                                <input type="checkbox" class="wp-lsp-day-closed" data-row="<?php echo $prefix.$idx; ?>" <?php checked($closed); ?>>
                                <span class="wp-lsp-slider"></span>
                            </span>
                            <span style="margin-left:8px; font-weight:bold; color:<?php echo $closed ? '#c00' : '#2196F3'; ?>">
                                <?php echo $closed ? 'Cerrado' : 'Abierto'; ?>
                            </span>
                        </label>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <label style="margin-top:15px;display:block;"><b>Horarios especiales</b> (periodos excepcionales, puedes añadir varios)</label>
        <div id="wp-lsp-special-hours-list-<?php echo $prefix; ?>">
            <?php
            $specials = $options[$prefix.'specialOpeningHoursSpecification'] ?? [];
            if(empty($specials)) $specials = [[]];
            foreach($specials as $i=>$sp) { ?>
                <div class="wp-lsp-special-hours-block">
                    <input type="date" name="wp_local_schema_pro_options[<?php echo $prefix; ?>specialOpeningHoursSpecification][<?php echo $i; ?>][from]" value="<?php echo esc_attr($sp['from']??''); ?>">
                    a <input type="date" name="wp_local_schema_pro_options[<?php echo $prefix; ?>specialOpeningHoursSpecification][<?php echo $i; ?>][to]" value="<?php echo esc_attr($sp['to']??''); ?>">
                    <span>Días:</span>
                    <?php foreach($days as $j=>$d) { ?>
                        <label><input type="checkbox" name="wp_local_schema_pro_options[<?php echo $prefix; ?>specialOpeningHoursSpecification][<?php echo $i; ?>][days][<?php echo $j; ?>]" <?php checked(!empty($sp['days'][$j])); ?>><?php echo $d[0]; ?></label>
                    <?php } ?>
                    <input type="time" name="wp_local_schema_pro_options[<?php echo $prefix; ?>specialOpeningHoursSpecification][<?php echo $i; ?>][opens]" value="<?php echo esc_attr($sp['opens']??''); ?>">
                    -
                    <input type="time" name="wp_local_schema_pro_options[<?php echo $prefix; ?>specialOpeningHoursSpecification][<?php echo $i; ?>][closes]" value="<?php echo esc_attr($sp['closes']??''); ?>">
                    <button type="button" class="wp-lsp-remove-special-hours">Eliminar</button>
                </div>
            <?php } ?>
        </div>
        <button type="button" id="wp-lsp-add-special-hours-<?php echo $prefix; ?>" style="margin-top:8px;">+ Añadir periodo especial</button>
    </div>
    <style>
    .wp-lsp-switch {position:relative;display:inline-block;width:44px;height:24px;vertical-align:middle;}
    .wp-lsp-switch input {opacity:0;width:0;height:0;}
    .wp-lsp-slider {position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#2196F3;transition:.4s;border-radius:24px;}
    .wp-lsp-switch input:checked + .wp-lsp-slider {background:#2196F3;}
    .wp-lsp-slider:before {position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background:#fff;transition:.4s;border-radius:50%;}
    .wp-lsp-switch input:checked + .wp-lsp-slider:before {transform:translateX(20px);}
    .wp-lsp-hours-table td {padding:3px 8px;}
    .wp-lsp-special-hours-block {margin-bottom:10px;padding:8px 10px;background:#f5f5f5;border-radius:6px;}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        document.querySelectorAll('.wp-lsp-day-closed').forEach(function(cb){
            cb.addEventListener('change',function(){
                let row=this.getAttribute('data-row');
                let prefix = row.replace(/[0-9]/g, '');
                let idx = row.replace(/\D/g,'');
                let opens=document.querySelector('input[name="wp_local_schema_pro_options['+prefix+'openingHoursSpecification]['+idx+'][opens]"]');
                let closes=document.querySelector('input[name="wp_local_schema_pro_options['+prefix+'openingHoursSpecification]['+idx+'][closes]"]');
                let label=this.closest('label').querySelector('span[style*="font-weight:bold"]');
                opens.disabled=closes.disabled=this.checked;
                if(this.checked){
                    opens.value=''; closes.value='';
                    label.textContent='Cerrado';
                    label.style.color='#c00';
                } else {
                    label.textContent='Abierto';
                    label.style.color='#2196F3';
                }
            });
        });
        document.querySelectorAll('[id^=wp-lsp-add-special-hours-]').forEach(function(btn){
            btn.onclick=function(){
                let prefix = btn.id.replace('wp-lsp-add-special-hours-','');
                let list=document.getElementById('wp-lsp-special-hours-list-'+prefix);
                let i=list.children.length;
                let html=`<div class="wp-lsp-special-hours-block">
                    <input type="date" name="wp_local_schema_pro_options[${prefix}specialOpeningHoursSpecification][${i}][from]" value="">
                    a <input type="date" name="wp_local_schema_pro_options[${prefix}specialOpeningHoursSpecification][${i}][to]" value="">
                    <span>Días:</span>
                    <?php foreach($days as $j=>$d) { ?>
                        <label><input type="checkbox" name="wp_local_schema_pro_options[${prefix}specialOpeningHoursSpecification][${i}][days][<?php echo $j; ?>]"><?php echo $d[0]; ?></label>
                    <?php } ?>
                    <input type="time" name="wp_local_schema_pro_options[${prefix}specialOpeningHoursSpecification][${i}][opens]" value="">
                    -
                    <input type="time" name="wp_local_schema_pro_options[${prefix}specialOpeningHoursSpecification][${i}][closes]" value="">
                    <button type="button" class="wp-lsp-remove-special-hours">Eliminar</button>
                </div>`;
                list.insertAdjacentHTML('beforeend',html);
                list.querySelectorAll('.wp-lsp-remove-special-hours').forEach(function(btn){
                    btn.onclick=function(){ this.parentNode.remove(); };
                });
            };
        });
        document.querySelectorAll('.wp-lsp-remove-special-hours').forEach(function(btn){
            btn.onclick=function(){ this.parentNode.remove(); };
        });
    });
    </script>
    <?php
}
// === FIN BLOQUE HORARIO UNIVERSAL ===

function wp_lsp_render_admin_panel() {
    require_once WP_LSP_PATH . 'includes/all-schema-types.php';
    global $all_schema_types; // <-- EL FIX CRÍTICO AQUÍ
    $schema_fields = wp_lsp_get_schema_fields();
    $options = get_option('wp_local_schema_pro_options');
    $current_type = $options['schema_type_principal'] ?? 'LocalBusiness';
    $enabled = isset($options['enabled']) ? (bool)$options['enabled'] : true;
    $sec_types_selected = $options['schema_types_secundarios'] ?? [];

    // Prefijos para campos Organization/Person
    $org_fields = array_map(function($f){return 'org_'.$f;}, array_keys($schema_fields['Organization']));
    $person_fields = array_map(function($f){return 'person_'.$f;}, array_keys($schema_fields['Person']));

    $sections = [
        'general'    => [
            'title'=>'General',
            'icon'=>'<span style="color:#183153;">🏠</span>',
            'fields'=>[
                'name', 'legalName', 'brand', 'alternateName',
                'description', 'image', 'logo', 'url',
                'telephone', 'faxNumber', 'email',
                'taxID', 'vatID', 'numberOfEmployees',
                'founder', 'foundingDate', 'awards',
                'areaServed', 'currenciesAccepted', 'paymentAccepted',
                'slogan', 'parentOrganization', 'hasOfferCatalog',
                'privacyPolicy', 'termsOfService', 'cookiesPolicy'
            ]
        ],
        'ubicacion'  => ['title'=>'Ubicación', 'icon'=>'<span style="color:#183153;">📍</span>', 'fields'=>['address','geo','hasMap','areaServed']],
        'horario'    => ['title'=>'Horario',   'icon'=>'<span style="color:#183153;">🕒</span>', 'fields'=>[]],
        'urls'       => ['title'=>'URLs',      'icon'=>'<span style="color:#183153;">🔗</span>', 'fields'=>['url','externalUrls']],
        'redes'      => ['title'=>'Redes',     'icon'=>'<span style="color:#183153;">🌐</span>', 'fields'=>['sameAs','googleBusiness']],
        'tienda'     => [
            'title'=>'Tienda',
            'icon'=>'<span style="color:#183153;">🛒</span>',
            'fields'=>array_filter(array_keys($schema_fields['Store']), function($f){
                return !in_array($f, ['openingHoursSpecification','specialOpeningHoursSpecification']);
            })
        ],
        'opiniones'  => ['title'=>'Opiniones', 'icon'=>'<span style="color:#183153;">⭐</span>', 'fields'=>['aggregateRating','review']],
        'orgpersona' => [
            'title'=>'Org/Pers',
            'icon'=>'<span style="color:#183153;">🏠👤</span>',
            'fields'=>array_filter(
                array_merge(
                    $org_fields, $person_fields,
                    array_diff(array_keys($schema_fields['Organization']), ['openingHoursSpecification','specialOpeningHoursSpecification']),
                    array_diff(array_keys($schema_fields['Person']), ['openingHoursSpecification','specialOpeningHoursSpecification'])
                ), function($f){
                    return !in_array($f, ['org_openingHoursSpecification','org_specialOpeningHoursSpecification','person_openingHoursSpecification','person_specialOpeningHoursSpecification']);
                }
            )
        ],
    ];

    $section_enabled = $options['section_enabled'] ?? [];
    ?>
    <div class="wrap wp-lsp-app">
        <h1>WP Local Schema PRO</h1>
        <?php
        wp_lsp_check_yoast_organization_block($options);
        ?>
        <form method="post" action="options.php" id="wp-lsp-form" autocomplete="off">
            <?php settings_fields('wp_local_schema_pro_group'); do_settings_sections('wp-local-schema-pro'); ?>
            <div style="margin-bottom:20px;">
                <label style="font-weight:bold;">Activar Schema Local para este sitio</label>
                <label class="wp-lsp-switch" title="Activa o desactiva el schema para toda la web.">
                    <input type="checkbox" id="wp-lsp-global-enable" name="wp_local_schema_pro_options[enabled]" value="1" <?php checked($enabled); ?>>
                    <span class="wp-lsp-slider"></span>
                </label>
                <span class="wp-lsp-help" title="Si desactivas, el schema no se insertará en tu web.">ℹ️</span>
            </div>

            <!-- Selector principal de type -->
            <label for="schema_type_principal"><b>Tipo principal de Schema (negocio):</b></label>
            <select id="schema_type_principal" name="wp_local_schema_pro_options[schema_type_principal]" title="Tipo de entidad principal para la web. Lo normal es LocalBusiness.">
                <?php foreach ($all_schema_types as $type): ?>
                    <option value="<?php echo esc_attr($type); ?>" <?php selected($current_type, $type); ?>><?php echo esc_html($type); ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <!-- Configuración de tipos secundarios con páginas asociadas -->
            <label><b>Tipos/categorías secundarias:</b></label>
            <div class="wp-lsp-secondary-types-container" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">
                <div id="wp-lsp-secondary-types-list">
                    <?php
                    // Convertir datos antiguos si es necesario
                    $secondary_types_data = $options['schema_types_secundarios_v2'] ?? [];
                    
                    // Migración de datos antiguos
                    if (empty($secondary_types_data) && !empty($sec_types_selected) && is_array($sec_types_selected)) {
                        foreach ($sec_types_selected as $type) {
                            $secondary_types_data[] = [
                                'type' => $type,
                                'page_url' => ''
                            ];
                        }
                    }
                    
                    $published_pages = wp_lsp_get_published_pages();
                    
                    if (empty($secondary_types_data)) {
                        $secondary_types_data = [['type' => '', 'page_url' => '']];
                    }
                    
                    foreach ($secondary_types_data as $index => $sec_data): ?>
                        <div class="wp-lsp-secondary-type-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #e1e1e1; border-radius: 4px;">
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px;">
                                    <label><strong>Tipo de Schema:</strong></label>
                                    <select name="wp_local_schema_pro_options[schema_types_secundarios_v2][<?php echo $index; ?>][type]" style="width: 100%;">
                                        <option value="">-- Seleccionar tipo --</option>
                                        <?php foreach ($all_schema_types as $type): ?>
                                            <option value="<?php echo esc_attr($type); ?>" <?php selected($sec_data['type'] ?? '', $type); ?>>
                                                <?php echo esc_html($type); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="flex: 2; min-width: 300px;">
                                    <label><strong>Página asociada:</strong></label>
                                    <select name="wp_local_schema_pro_options[schema_types_secundarios_v2][<?php echo $index; ?>][page_url]" style="width: 100%;">
                                        <?php foreach ($published_pages as $url => $title): ?>
                                            <option value="<?php echo esc_attr($url); ?>" <?php selected($sec_data['page_url'] ?? '', $url); ?>>
                                                <?php echo esc_html($title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="flex: 0;">
                                    <button type="button" class="button wp-lsp-remove-secondary-type" style="color: #dc3232;">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="wp-lsp-add-secondary-type" class="button button-secondary" style="margin-top: 10px;">+ Añadir tipo secundario</button>
                <p class="description" style="margin-top: 10px;">
                    <strong>Nota:</strong> Los tipos secundarios solo se mostrarán en las páginas específicas asociadas. 
                    Si no asocias una página, el tipo secundario no se mostrará en ninguna parte del sitio.
                </p>
            </div>

            <!-- Campo para enlace Wikipedia/keyword principal -->
            <label>Enlace a Wikipedia (keyword objetivo principal):</label>
            <input type="url" name="wp_local_schema_pro_options[wikipedia_url_principal]" value="<?php echo esc_attr($options['wikipedia_url_principal'] ?? ''); ?>" style="width:100%">
            <br><br>

            <hr>
            <div class="wp-lsp-tabs">
                <?php $i=0; foreach ($sections as $sec_key => $sec): ?>
                    <button type="button" class="wp-lsp-tab<?php if($i==0)echo ' active'; ?>" data-tab="<?php echo esc_attr($sec_key); ?>" title="Ver sección <?php echo esc_attr($sec['title']); ?>">
                        <?php echo $sec['icon']; ?>
                        <?php echo esc_html($sec['title']); ?>
                        <span class="wp-lsp-section-switch" title="Activa/desactiva la sección entera">
                            <label class="wp-lsp-switch" title="Activa o desactiva esta sección">
                                <input type="checkbox" name="wp_local_schema_pro_options[section_enabled][<?php echo $sec_key; ?>]" value="1" <?php checked($section_enabled[$sec_key] ?? true); ?>>
                                <span class="wp-lsp-slider"></span>
                            </label>
                        </span>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
            <div class="wp-lsp-tabs-content">
                <?php $i=0; foreach ($sections as $sec_key => $sec): ?>
                    <div class="wp-lsp-tab-content<?php if($i==0)echo ' active'; ?>" data-tab="<?php echo esc_attr($sec_key); ?>">
                        <?php
                        // ----- HORARIO -----
                        if ($sec_key == 'horario') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                <b>Horario comercial:</b> Define el horario habitual por días.<br>
                                <b>Horarios especiales:</b> Añade periodos con fechas y días/horas excepcionales (ej: festivos, vacaciones).
                            </div>';
                            wp_lsp_render_hours_block('', $options);
                        }

                        // ----- TIENDA -----
                        if ($sec_key == 'tienda') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                <b>Horario de la tienda:</b> (si aplica, independiente del negocio principal)
                            </div>';
                            wp_lsp_render_hours_block('store_', $options);
                        }

                        // ----- ORG/PERSONA -----
                        if ($sec_key == 'orgpersona') {
                            $selected_orgpersona = $options['orgpersona_type'] ?? 'Organization';
                            ?>
                            <div class="wp-lsp-alert wp-lsp-alert-warning" style="color:#a77a10;">
                                <b>⚠️ Importante:</b> Elige solo <b>Organización</b> o <b>Persona</b>. Si ya tienes este schema en Yoast, Rank Math u otro plugin, <b>no lo rellenes aquí</b><br>
                                <b>SEO Pro Tip:</b> Solo una opción debe estar activa y nunca duplicada.<br>
                                <a href="https://schema.org/Organization" target="_blank">Organization</a> | <a href="https://schema.org/Person" target="_blank">Person</a>
                            </div>
                            <label for="orgpersona_type"><b>¿Qué representa tu sitio?</b></label>
                            <select name="wp_local_schema_pro_options[orgpersona_type]" id="orgpersona_type" style="margin-bottom:1em;">
                                <option value="Organization" <?php selected($selected_orgpersona, 'Organization'); ?>>Organización</option>
                                <option value="Person" <?php selected($selected_orgpersona, 'Person'); ?>>Persona</option>
                            </select>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                function toggleOrgPersonaFields() {
                                    const orgFields = document.querySelectorAll('.org-fields');
                                    const personFields = document.querySelectorAll('.person-fields');
                                    const val = document.getElementById('orgpersona_type').value;
                                    orgFields.forEach(f => f.style.display = (val==='Organization')?'':'none');
                                    personFields.forEach(f => f.style.display = (val==='Person')?'':'none');
                                }
                                document.getElementById('orgpersona_type').addEventListener('change', toggleOrgPersonaFields);
                                toggleOrgPersonaFields();
                            });
                            </script>
                            <style>
                            .org-fields, .person-fields {margin-bottom:1em;}
                            </style>
                            <?php
                            // Horario de organización (Organization)
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                <b>Horario de la organización:</b> (si aplica, solo para datos Organization avanzados)
                            </div>';
                            wp_lsp_render_hours_block('org_', $options);
                        }

                        // ----- RATINGS -----
                        if ($sec_key == 'opiniones') {
                            ?>
                            <div class="wp-lsp-alert wp-lsp-alert-warning" style="color:#a77a10;">
                                <b>¡Atención!</b> Si ya tienes ratings generados por otro plugin (WooCommerce, WP Reviews, etc.), <b>no actives los ratings aquí</b> para evitar duplicidad de schema.<br>
                                Marca opiniones solo si no tienes otro sistema de ratings.<br>
                                <label>
                                    <input type="checkbox" name="wp_local_schema_pro_options[force_ratings]" value="1" <?php checked($options['force_ratings'] ?? false); ?>>
                                    <b>Forzar ratings desde este plugin</b> (solo si no hay otro plugin de opiniones activo)
                                </label>
                            </div>
                            <div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                <b>SEO Pro Tip:</b> Solo valores reales y visibles en la web. Si no tienes opiniones auténticas, <b>deja este campo vacío</b>.<br>
                                Si arrancas con opiniones ficticias, usa muy pocas (máx. 3-5), variadas y creíbles. No abuses.<br>
                                Actualiza y amplía las opiniones con datos reales ASAP.
                            </div>
                            <?php
                        }

                        // ----- RENDER FIELDS -----
                        foreach ($sec['fields'] as $field_id) {
                            $is_org = strpos($field_id, 'org_') === 0;
                            $is_person = strpos($field_id, 'person_') === 0;
                            $real_id = $is_org ? substr($field_id, 4) : ($is_person ? substr($field_id,7): $field_id);
                            $field_def = null;

                            if (in_array($real_id, ['employees','founders','awards','award','member','alumniOf','knowsLanguage','knowsAbout','relatedTo','spouse','brand','contactPoint'])) {
                                $arr = $options[$field_id] ?? [];
                                echo '<div class="'.($is_org?'org-fields':($is_person?'person-fields':'')).'">';
                                echo '<label>'.ucfirst($real_id).'</label>';
                                echo '<div class="wp-lsp-array-list" data-field="'.$field_id.'">';
                                foreach($arr as $k=>$v){
                                    echo '<div><input type="text" name="wp_local_schema_pro_options['.$field_id.']['.$k.']" value="'.esc_attr($v).'"> <button type="button" class="wp-lsp-array-remove">-</button></div>';
                                }
                                echo '</div><button type="button" class="wp-lsp-array-add" data-field="'.$field_id.'">+ Añadir</button></div>';
                                continue;
                            }

                            if ($is_org && isset($schema_fields['Organization'][$real_id]))
                                $field_def = $schema_fields['Organization'][$real_id];
                            elseif ($is_person && isset($schema_fields['Person'][$real_id]))
                                $field_def = $schema_fields['Person'][$real_id];
                            elseif (isset($schema_fields['Store'][$field_id]))
                                $field_def = $schema_fields['Store'][$field_id];
                            elseif (isset($schema_fields[$current_type][$field_id]))
                                $field_def = $schema_fields[$current_type][$field_id];
                            $val = $options[$field_id] ?? '';
                            $class = '';
                            if ($sec_key == 'orgpersona') {
                                $class = $is_org ? 'org-fields' : ($is_person ? 'person-fields' : '');
                            }
                            if ($field_def) {
                                echo '<div class="'.$class.'">'.wp_lsp_render_field($field_id, $field_def, $val).'</div>';
                            }
                        }
                        ?>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
            <?php submit_button(); ?>
        </form>
        <link rel="stylesheet" href="<?php echo WP_LSP_URL; ?>assets/style.css?v=6" />
        <style>
        .wp-lsp-switch {position:relative;display:inline-block;width:44px;height:24px;vertical-align:middle;}
        .wp-lsp-switch input {opacity:0;width:0;height:0;}
        .wp-lsp-slider {position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#cfd8dc;transition:.4s;border-radius:24px;}
        .wp-lsp-switch input:checked + .wp-lsp-slider {background:#2196F3;}
        .wp-lsp-slider:before {position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background:#fff;transition:.4s;border-radius:50%;}
        .wp-lsp-switch input:checked + .wp-lsp-slider:before {transform:translateX(20px);}
        .wp-lsp-section-switch {margin-left:10px;}
        .wp-lsp-alert {margin:12px 0;padding:10px 14px;border-radius:6px;font-size:0.97em;}
        .wp-lsp-alert-info {background:#e3f2fd;}
        .wp-lsp-alert-warning {background:#fff3cd;}
        .wp-lsp-array-list div {margin-bottom:5px;}
        .wp-lsp-help {margin-left:3px;color:#0073aa;cursor:help;}
        </style>
        <script>
        document.addEventListener('DOMContentLoaded',function(){
            let tabs=document.querySelectorAll('.wp-lsp-tab');
            let contents=document.querySelectorAll('.wp-lsp-tab-content');
            tabs.forEach((tab,i)=>{
                tab.addEventListener('click',()=>{
                    tabs.forEach(t=>t.classList.remove('active'));
                    contents.forEach(c=>c.classList.remove('active'));
                    tab.classList.add('active');
                    contents[i].classList.add('active');
                });
            });
            document.querySelectorAll('.wp-lsp-array-add').forEach(function(btn){
                btn.onclick=function(){
                    let field=btn.getAttribute('data-field');
                    let list=btn.parentNode.querySelector('.wp-lsp-array-list');
                    let i=list.children.length;
                    list.insertAdjacentHTML('beforeend','<div><input type="text" name="wp_local_schema_pro_options['+field+']['+i+']" value=""> <button type="button" class="wp-lsp-array-remove">-</button></div>');
                    list.querySelectorAll('.wp-lsp-array-remove').forEach(function(b){
                        b.onclick=function(){ this.parentNode.remove(); };
                    });
                };
            });
            document.querySelectorAll('.wp-lsp-array-remove').forEach(function(btn){
                btn.onclick=function(){ this.parentNode.remove(); };
            });
        });
        </script>
        <script>
            // Gestión de tipos secundarios dinámicos
            document.addEventListener('DOMContentLoaded', function() {
                let secondaryTypeIndex = <?php echo count($secondary_types_data); ?>;
                
                // Añadir nuevo tipo secundario
                document.getElementById('wp-lsp-add-secondary-type').addEventListener('click', function() {
                    const container = document.getElementById('wp-lsp-secondary-types-list');
                    const publishedPages = <?php echo json_encode($published_pages); ?>;
                    const allSchemaTypes = <?php echo json_encode($all_schema_types); ?>;
                    
                    let html = `
                        <div class="wp-lsp-secondary-type-item" style="margin-bottom: 15px; padding: 10px; border: 1px solid #e1e1e1; border-radius: 4px;">
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px;">
                                    <label><strong>Tipo de Schema:</strong></label>
                                    <select name="wp_local_schema_pro_options[schema_types_secundarios_v2][${secondaryTypeIndex}][type]" style="width: 100%;">
                                        <option value="">-- Seleccionar tipo --</option>`;
                    
                    allSchemaTypes.forEach(function(type) {
                        html += `<option value="${type}">${type}</option>`;
                    });
                    
                    html += `
                                    </select>
                                </div>
                                <div style="flex: 2; min-width: 300px;">
                                    <label><strong>Página asociada:</strong></label>
                                    <select name="wp_local_schema_pro_options[schema_types_secundarios_v2][${secondaryTypeIndex}][page_url]" style="width: 100%;">`;
                    
                    Object.keys(publishedPages).forEach(function(url) {
                        html += `<option value="${url}">${publishedPages[url]}</option>`;
                    });
                    
                    html += `
                                    </select>
                                </div>
                                <div style="flex: 0;">
                                    <button type="button" class="button wp-lsp-remove-secondary-type" style="color: #dc3232;">Eliminar</button>
                                </div>
                            </div>
                        </div>`;
                    
                    container.insertAdjacentHTML('beforeend', html);
                    secondaryTypeIndex++;
                    
                    // Activar el evento de eliminar en el nuevo elemento
                    bindRemoveEvents();
                });
                
                // Función para activar eventos de eliminar
                function bindRemoveEvents() {
                    document.querySelectorAll('.wp-lsp-remove-secondary-type').forEach(function(button) {
                        button.removeEventListener('click', removeSecondaryType); // Evitar duplicados
                        button.addEventListener('click', removeSecondaryType);
                    });
                }
                
                // Función para eliminar tipo secundario
                function removeSecondaryType(e) {
                    const items = document.querySelectorAll('.wp-lsp-secondary-type-item');
                    if (items.length > 1) {
                        e.target.closest('.wp-lsp-secondary-type-item').remove();
                    } else {
                        alert('Debe mantener al menos un tipo secundario. Si no desea ninguno, deje el tipo vacío.');
                    }
                }
                
                // Activar eventos iniciales
                bindRemoveEvents();
            });
        </script>
        <script>
            window.wpLspSchemaFields = <?php echo json_encode($schema_fields); ?>;
            window.wpLspCurrentOptions = <?php echo json_encode($options); ?>;
            window.wpLspDateFormat = "<?php echo esc_js(get_option('date_format', 'Y-m-d')); ?>";
            window.wpLspTimeFormat = "<?php echo esc_js(get_option('time_format', 'H:i')); ?>";
        </script>
        <script src="<?php echo WP_LSP_URL; ?>assets/admin.js?v=6"></script>
    </div>
    <?php
}

add_action('admin_init', function() {
    register_setting('wp_local_schema_pro_group', 'wp_local_schema_pro_options');
});
