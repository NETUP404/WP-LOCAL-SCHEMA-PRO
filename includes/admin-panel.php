<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    add_menu_page(
        'WP Local Schema PRO',
        'Local Schema PRO',
        'manage_options',
        'wp-local-schema-pro',
        'wp_lsp_render_admin_panel',
        'dashicons-store',
        32
    );
});

function wp_lsp_render_admin_panel() {
    $schema_fields = wp_lsp_get_schema_fields();
    $options = get_option('wp_local_schema_pro_options');
    $current_type = $options['schema_type'] ?? 'LocalBusiness';
    $enabled = isset($options['enabled']) ? (bool)$options['enabled'] : true;

    // Prefijos para campos Organization/Person
    $org_fields = array_map(function($f){return 'org_'.$f;}, array_keys($schema_fields['Organization']));
    $person_fields = array_map(function($f){return 'person_'.$f;}, array_keys($schema_fields['Person']));

    // Campos avanzados
    $extra_fields = [
        'Organization'   => ['founders','employees','awards','areaServed','slogan','brand','contactPoint','duns','globalLocationNumber','hasOfferCatalog','isicV4','leiCode','member','numberOfEmployees','owns','parentOrganization','subOrganization','taxID','vatID','department','logo','email','telephone','address','sameAs','url','image','aggregateRating','review','openingHoursSpecification'],
        'LocalBusiness'  => ['priceRange','currenciesAccepted','paymentAccepted','openingHoursSpecification','areaServed','serviceType','hasMerchantReturnPolicy'],
        'Store'          => ['branchOf','currenciesAccepted','paymentAccepted','openingHoursSpecification','brand','areaServed'],
        'Person'         => ['jobTitle','worksFor','affiliation','alumniOf','award','birthDate','deathDate','gender','hasCredential','hasOccupation','knowsAbout','knowsLanguage','nationality','honorificPrefix','honorificSuffix','memberOf','relatedTo','spouse','telephone','email','url','sameAs','image'],
    ];

    $sections = [
        'general'    => ['title'=>'General',   'icon'=>'<span style="color:#183153;">🏢</span>', 'fields'=>['name','description','image','telephone','email']],
        'ubicacion'  => ['title'=>'Ubicación', 'icon'=>'<span style="color:#183153;">📍</span>', 'fields'=>['address','geo','hasMap','areaServed']],
        'horario'    => ['title'=>'Horario',   'icon'=>'<span style="color:#183153;">🕒</span>', 'fields'=>['openingHoursSpecification','specialOpeningHoursSpecification']],
        'urls'       => ['title'=>'URLs',      'icon'=>'<span style="color:#183153;">🔗</span>', 'fields'=>['url','externalUrls']],
        'redes'      => ['title'=>'Redes',     'icon'=>'<span style="color:#183153;">🌐</span>', 'fields'=>['sameAs','googleBusiness']],
        'tienda'     => ['title'=>'Tienda',    'icon'=>'<span style="color:#183153;">🛒</span>', 'fields'=>array_merge(array_keys($schema_fields['Store']), $extra_fields['Store'])],
        'opiniones'  => ['title'=>'Opiniones', 'icon'=>'<span style="color:#183153;">⭐</span>', 'fields'=>['aggregateRating','review']],
        'orgpersona' => ['title'=>'Org/Pers',  'icon'=>'<span style="color:#183153;">🏢👤</span>', 'fields'=>array_merge($org_fields, $person_fields, $extra_fields['Organization'], $extra_fields['Person'])],
    ];
    $section_enabled = $options['section_enabled'] ?? [];

    ?>
    <div class="wrap wp-lsp-app">
        <h1>WP Local Schema PRO</h1>
        <form method="post" action="options.php" id="wp-lsp-form" autocomplete="off">
            <?php settings_fields('wp_local_schema_pro_group'); do_settings_sections('wp-local-schema-pro'); ?>
            <div style="margin-bottom:20px;">
                <label style="font-weight:bold;">Activar Schema Local para este sitio</label>
                <label class="wp-lsp-switch">
                    <input type="checkbox" id="wp-lsp-global-enable" name="wp_local_schema_pro_options[enabled]" value="1" <?php checked($enabled); ?>>
                    <span class="wp-lsp-slider"></span>
                </label>
                <span class="wp-lsp-help" title="Si desactivas, el schema no se insertará en tu web.">ⓘ</span>
            </div>
            <label for="schema_type"><b>Tipo de Schema (negocio):</b></label>
            <select id="schema_type" name="wp_local_schema_pro_options[schema_type]">
                <?php foreach ($schema_fields as $type => $fields): ?>
                    <option value="<?php echo esc_attr($type); ?>" <?php selected($current_type, $type); ?>><?php echo esc_html($type); ?></option>
                <?php endforeach; ?>
            </select>
            <hr>
            <div class="wp-lsp-tabs">
                <?php $i=0; foreach ($sections as $sec_key => $sec): ?>
                    <button type="button" class="wp-lsp-tab<?php if($i==0)echo ' active'; ?>" data-tab="<?php echo esc_attr($sec_key); ?>">
                        <?php echo $sec['icon']; ?>
                        <?php echo esc_html($sec['title']); ?>
                        <span class="wp-lsp-section-switch">
                            <label class="wp-lsp-switch">
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
                            ?>
                            <div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                <b>Horario comercial:</b> Define el horario habitual por días. Solo puede haber uno.<br>
                                <b>Horarios especiales:</b> Añade periodos con fecha inicio/fin y días/horas excepcionales (ej: festivos, campaña navideña, vacaciones).
                            </div>
                            <div>
                                <label><b>Horario habitual</b></label>
                                <table class="wp-lsp-hours-table">
                                    <?php
                                    $days = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                                    $opening = $options['openingHoursSpecification'] ?? [];
                                    foreach($days as $idx => $day) {
                                        $o = $opening[$idx]['opens'] ?? '';
                                        $c = $opening[$idx]['closes'] ?? '';
                                        $closed = empty($o) && empty($c);
                                        ?>
                                        <tr>
                                            <td><?php echo $day; ?></td>
                                            <td>
                                                <input type="time" name="wp_local_schema_pro_options[openingHoursSpecification][<?php echo $idx; ?>][opens]" value="<?php echo esc_attr($o); ?>" <?php if($closed) echo 'disabled'; ?>>
                                                -
                                                <input type="time" name="wp_local_schema_pro_options[openingHoursSpecification][<?php echo $idx; ?>][closes]" value="<?php echo esc_attr($c); ?>" <?php if($closed) echo 'disabled'; ?>>
                                                <label style="margin-left:10px;"><input type="checkbox" class="wp-lsp-day-closed" data-row="<?php echo $idx; ?>" <?php checked($closed); ?>> Cerrado</label>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                <script>
                                document.addEventListener('DOMContentLoaded',function(){
                                    document.querySelectorAll('.wp-lsp-day-closed').forEach(function(cb){
                                        cb.addEventListener('change',function(){
                                            let row=this.getAttribute('data-row');
                                            let opens=document.querySelector('input[name="wp_local_schema_pro_options[openingHoursSpecification]['+row+'][opens]"]');
                                            let closes=document.querySelector('input[name="wp_local_schema_pro_options[openingHoursSpecification]['+row+'][closes]"]');
                                            opens.disabled=closes.disabled=this.checked;
                                            if(this.checked){ opens.value=''; closes.value=''; }
                                        });
                                    });
                                });
                                </script>
                                <label><b>Horarios especiales</b> (periodos excepcionales, puedes añadir varios)</label>
                                <div id="wp-lsp-special-hours-list">
                                    <?php
                                    $specials = $options['specialOpeningHoursSpecification'] ?? [];
                                    if(empty($specials)) $specials = [[]];
                                    foreach($specials as $i=>$sp) { ?>
                                        <div class="wp-lsp-special-hours-block">
                                            <input type="date" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][<?php echo $i; ?>][from]" value="<?php echo esc_attr($sp['from']??''); ?>"> a
                                            <input type="date" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][<?php echo $i; ?>][to]" value="<?php echo esc_attr($sp['to']??''); ?>">
                                            <span>Días:</span>
                                            <?php foreach($days as $j=>$d) { ?>
                                                <label><input type="checkbox" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][<?php echo $i; ?>][days][<?php echo $j; ?>]" <?php checked(!empty($sp['days'][$j])); ?>><?php echo $d[0]; ?></label>
                                            <?php } ?>
                                            <input type="time" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][<?php echo $i; ?>][opens]" value="<?php echo esc_attr($sp['opens']??''); ?>">
                                            -
                                            <input type="time" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][<?php echo $i; ?>][closes]" value="<?php echo esc_attr($sp['closes']??''); ?>">
                                            <button type="button" class="wp-lsp-remove-special-hours">Eliminar</button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <button type="button" id="wp-lsp-add-special-hours">+ Añadir periodo especial</button>
                                <script>
                                document.addEventListener('DOMContentLoaded',function(){
                                    document.getElementById('wp-lsp-add-special-hours').onclick=function(){
                                        let list=document.getElementById('wp-lsp-special-hours-list');
                                        let i=list.children.length;
                                        let html=`<div class="wp-lsp-special-hours-block">
                                            <input type="date" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][${i}][from]" value="">
                                            a <input type="date" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][${i}][to]" value="">
                                            <span>Días:</span>
                                            <?php foreach($days as $j=>$d) { ?>
                                                <label><input type="checkbox" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][${i}][days][<?php echo $j; ?>]"><?php echo $d[0]; ?></label>
                                            <?php } ?>
                                            <input type="time" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][${i}][opens]" value="">
                                            -
                                            <input type="time" name="wp_local_schema_pro_options[specialOpeningHoursSpecification][${i}][closes]" value="">
                                            <button type="button" class="wp-lsp-remove-special-hours">Eliminar</button>
                                        </div>`;
                                        list.insertAdjacentHTML('beforeend',html);
                                        list.querySelectorAll('.wp-lsp-remove-special-hours').forEach(function(btn){
                                            btn.onclick=function(){ this.parentNode.remove(); };
                                        });
                                    };
                                    document.querySelectorAll('.wp-lsp-remove-special-hours').forEach(function(btn){
                                        btn.onclick=function(){ this.parentNode.remove(); };
                                    });
                                });
                                </script>
                            </div>
                            <style>
                            .wp-lsp-hours-table td {padding:3px 8px;}
                            .wp-lsp-special-hours-block {margin-bottom:10px;padding:8px 10px;background:#f5f5f5;border-radius:6px;}
                            </style>
                            <?php
                        }

                        // ----- RATINGS -----
                        if ($sec_key == 'opiniones') {
                            ?>
                            <div class="wp-lsp-alert wp-lsp-alert-warning" style="color:#a77a10;">
                                <b>¡Atención!</b> Si ya tienes ratings generados por otro plugin (WooCommerce, WP Reviews, etc.), <b>no actives los ratings aquí</b> para evitar duplicidad de schema y pérdida de rich snippets.<br>
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

                        // ----- ORG/PERSONA -----
                        if ($sec_key == 'orgpersona') {
                            $selected_orgpersona = $options['orgpersona_type'] ?? 'Organization';
                            ?>
                            <div class="wp-lsp-alert wp-lsp-alert-warning" style="color:#a77a10;">
                                <b>⚠️ Importante:</b> Elige solo <b>Organización</b> o <b>Persona</b>. Si ya tienes este schema en Yoast, Rank Math u otro plugin, <b>no lo rellenes aquí</b>.<br>
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
                        }

                        // ----- RENDER FIELDS -----
                        foreach ($sec['fields'] as $field_id) {
                            $is_org = strpos($field_id, 'org_') === 0;
                            $is_person = strpos($field_id, 'person_') === 0;
                            $real_id = $is_org ? substr($field_id, 4) : ($is_person ? substr($field_id,7): $field_id);
                            $field_def = null;

                            // Arrays dinámicos para empleados, founders, awards...
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
        </style>
        <script>
        // Tabs, arrays dinámicos
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