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

    $sections = [
        'general' => [
            'title'=>'Datos generales',
            'icon'=>'<span style="color:#183153;">🏢</span>',
            'fields'=>['name','description','image','telephone','email']
        ],
        'ubicacion' => [
            'title'=>'Ubicación',
            'icon'=>'<span style="color:#183153;">📍</span>',
            'fields'=>['address','geo','hasMap']
        ],
        'horario' => [
            'title'=>'Horario',
            'icon'=>'<span style="color:#183153;">🕒</span>',
            'fields'=>['openingHoursSpecification','specialOpeningHoursSpecification']
        ],
        'urls' => [
            'title'=>'URLs externas',
            'icon'=>'<span style="color:#183153;">🔗</span>',
            'fields'=>['url','externalUrls']
        ],
        'redes' => [
            'title'=>'Redes sociales',
            'icon'=>'<span style="color:#183153;">🌐</span>',
            'fields'=>['sameAs','googleBusiness']
        ],
        'tienda' => [
            'title'=>'Tienda',
            'icon'=>'<span style="color:#183153;">🛒</span>',
            'fields'=>array_keys($schema_fields['Store'])
        ],
        'opiniones' => [
            'title'=>'Opiniones',
            'icon'=>'<span style="color:#183153;">⭐</span>',
            'fields'=>['aggregateRating','review']
        ],
        'dirs' => [
            'title'=>'Directorios locales',
            'icon'=>'<span style="color:#183153;">🌍</span>',
            'fields'=>[]
        ],
        'org' => [
            'title'=>'Organización',
            'icon'=>'<span style="color:#183153;">🏢</span>',
            'fields'=>array_keys($schema_fields['Organization'])
        ],
        'persona' => [
            'title'=>'Persona',
            'icon'=>'<span style="color:#183153;">👤</span>',
            'fields'=>array_keys($schema_fields['Person'])
        ],
        'seoextra' => [
            'title'=>'SEO Local Extra',
            'icon'=>'<span style="color:#183153;">🧭</span>',
            'fields'=>[]
        ],
    ];
    $section_enabled = $options['section_enabled'] ?? [];

    ?>
    <div class="wrap wp-lsp-app">
        <h1>WP Local Schema PRO</h1>
        <div class="wp-lsp-global-switch">
            <label>
                <input type="checkbox" id="wp-lsp-global-enable" name="wp_local_schema_pro_options[enabled]" value="1" <?php checked($enabled); ?>>
                <span class="switch"></span>
                <b>Activar Schema Local para este sitio</b>
            </label>
            <span class="wp-lsp-help" title="Si desactivas, el schema no se insertará en tu web.">ⓘ</span>
        </div>
        <form method="post" action="options.php" id="wp-lsp-form" autocomplete="off">
            <?php
            settings_fields('wp_local_schema_pro_group');
            do_settings_sections('wp-local-schema-pro');
            ?>
            <label for="schema_type"><b>Tipo de Schema (negocio):</b></label>
            <select id="schema_type" name="wp_local_schema_pro_options[schema_type]">
                <?php foreach ($schema_fields as $type => $fields): ?>
                    <option value="<?php echo esc_attr($type); ?>" <?php selected($current_type, $type); ?>><?php echo esc_html($type); ?></option>
                <?php endforeach; ?>
            </select>
            <hr>
            <!-- PESTAÑAS -->
            <div class="wp-lsp-tabs">
                <?php $i=0; foreach ($sections as $sec_key => $sec): ?>
                    <button type="button" class="wp-lsp-tab<?php if($i==0)echo ' active'; ?>" data-tab="<?php echo esc_attr($sec_key); ?>">
                        <?php echo $sec['icon']; ?>
                        <?php echo esc_html($sec['title']); ?>
                        <span class="wp-lsp-section-switch">
                            <input type="checkbox" name="wp_local_schema_pro_options[section_enabled][<?php echo $sec_key; ?>]" value="1" <?php checked($section_enabled[$sec_key] ?? true); ?>>
                        </span>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
            <!-- CONTENIDO DE CADA PESTAÑA -->
            <div class="wp-lsp-tabs-content">
                <?php $i=0; foreach ($sections as $sec_key => $sec): ?>
                    <div class="wp-lsp-tab-content<?php if($i==0)echo ' active'; ?>" data-tab="<?php echo esc_attr($sec_key); ?>">
                        <?php
                        foreach ($sec['fields'] as $field_id) {
                            $field_def = null;
                            if (isset($schema_fields['Store'][$field_id])) $field_def = $schema_fields['Store'][$field_id];
                            elseif (isset($schema_fields['Organization'][$field_id])) $field_def = $schema_fields['Organization'][$field_id];
                            elseif (isset($schema_fields['Person'][$field_id])) $field_def = $schema_fields['Person'][$field_id];
                            elseif (isset($schema_fields[$current_type][$field_id])) $field_def = $schema_fields[$current_type][$field_id];
                            if ($field_def) {
                                $val = $options[$field_id] ?? '';
                                echo wp_lsp_render_field($field_id, $field_def, $val);
                            }
                        }
                        // Sección directorios locales (enlazador)
                        if ($sec_key == 'dirs') {
                            $data = [
                                'name' => $options['name'] ?? '',
                                'address' => $options['address']['streetAddress'] ?? '',
                                'city' => $options['address']['addressLocality'] ?? '',
                                'postal' => $options['address']['postalCode'] ?? '',
                                'phone' => $options['telephone'] ?? '',
                                'web' => $options['url'] ?? '',
                            ];
                            ?>
                            <div class="wp-lsp-directories">
                                <div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">
                                    <b>Enlazador rápido a directorios locales</b><br>
                                    Accede rápidamente a los principales directorios. Registrar tu negocio en todos ellos aumenta tu visibilidad local y autoridad SEO.<br>
                                    <i>Revisa que tus datos sean idénticos en todos los directorios.</i>
                                </div>
                                <ul style="margin-top:1em;">
                                    <li><a target="_blank" href="https://www.google.com/maps/search/<?php echo urlencode($data['name'].' '.$data['address'].' '.$data['city']); ?>">Google Maps</a></li>
                                    <li><a target="_blank" href="https://www.bing.com/maps?q=<?php echo urlencode($data['name'].' '.$data['address'].' '.$data['city']); ?>">Bing Places</a></li>
                                    <li><a target="_blank" href="https://www.yelp.com/search?find_desc=<?php echo urlencode($data['name']); ?>&find_loc=<?php echo urlencode($data['city']); ?>">Yelp</a></li>
                                    <li><a target="_blank" href="https://www.tripadvisor.es/Search?q=<?php echo urlencode($data['name'].' '.$data['city']); ?>">TripAdvisor</a></li>
                                    <li><a target="_blank" href="https://www.paginasamarillas.es/search/?what=<?php echo urlencode($data['name']); ?>&where=<?php echo urlencode($data['city']); ?>">Páginas Amarillas</a></li>
                                </ul>
                                <div style="margin-top:2em;color:#555;font-size:0.98em;">
                                    <b>¿Ya estás registrado?</b> Marca manualmente en cada directorio.<br>
                                    <label><input type="checkbox" name="wp_local_schema_pro_options[dirs_registered][google]" value="1" <?php checked($options['dirs_registered']['google']??false);?>> Google</label>
                                    <label><input type="checkbox" name="wp_local_schema_pro_options[dirs_registered][bing]" value="1" <?php checked($options['dirs_registered']['bing']??false);?>> Bing</label>
                                    <label><input type="checkbox" name="wp_local_schema_pro_options[dirs_registered][yelp]" value="1" <?php checked($options['dirs_registered']['yelp']??false);?>> Yelp</label>
                                    <label><input type="checkbox" name="wp_local_schema_pro_options[dirs_registered][tripadvisor]" value="1" <?php checked($options['dirs_registered']['tripadvisor']??false);?>> TripAdvisor</label>
                                    <label><input type="checkbox" name="wp_local_schema_pro_options[dirs_registered][paginasamarillas]" value="1" <?php checked($options['dirs_registered']['paginasamarillas']??false);?>> Páginas Amarillas</label>
                                </div>
                                <div style="margin-top:2em;color:#d33;">
                                    <b>NAP Checker:</b>
                                    <br>
                                    <?php
                                    $napOK = $data['name'] && $data['address'] && $data['city'] && $data['phone'];
                                    if (!$napOK) {
                                        echo '⚠️ Debes rellenar nombre, dirección, ciudad y teléfono para optimizar el SEO local (NAP consistente).';
                                    } else {
                                        echo '✔️ Tus datos NAP básicos están rellenados.';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        // Avisos según sección
                        if ($sec_key == 'opiniones') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-warning">⚠️ El rating máximo es 5 estrellas. Si pones más, Google puede penalizar tu ficha local.</div>';
                        }
                        if ($sec_key == 'horario') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">ℹ️ Usa los selectores de día y hora para evitar errores. El formato respeta la configuración de WordPress.</div>';
                        }
                        if ($sec_key == 'urls') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">ℹ️ Añade aquí la web principal, tienda online y otras webs asociadas (de la empresa, filiales, etc.).</div>';
                        }
                        if ($sec_key == 'tienda') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">ℹ️ Si tienes una tienda online/física puedes definir su schema aquí. <b>Si es externa, pon solo los datos básicos.</b></div>';
                        }
                        if ($sec_key == 'org') {
                            if (defined('WPSEO_VERSION')) {
                                echo '<div class="wp-lsp-alert wp-lsp-alert-warning" style="color:#a77a10;"><b>¡Atención!</b> Detectado Yoast SEO. Si usas este schema, desactiva el de Yoast para evitar duplicados y penalizaciones.<br>
                                <b>¿Cómo migrar?</b> Copia los datos y elimina el schema de organización en Yoast.<br>
                                <a href="https://yoast.com/help/duplicate-schema-output/" target="_blank">Ayuda oficial</a></div>';
                            } else {
                                echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">Puedes generar el schema de organización aquí si tu tema/plugin no lo hace.</div>';
                            }
                        }
                        if ($sec_key == 'persona') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">Completa solo si eres profesional o autónomo y necesitas schema de persona (no negocio).</div>';
                        }
                        if ($sec_key == 'seoextra') {
                            echo '<div class="wp-lsp-alert wp-lsp-alert-info" style="color:#183153;">Herramientas extra para SEO local:<ul>
                            <li>Chequeo de NAP (Name, Address, Phone) consistente.</li>
                            <li>Checklist de SEO local.</li>
                            <li>Generar QR para ubicación.</li>
                            <li>Validador de rich snippets.</li>
                            </ul></div>';
                        }
                        ?>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
            <?php submit_button(); ?>
        </form>
        <link rel="stylesheet" href="<?php echo WP_LSP_URL; ?>assets/style.css?v=6" />
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