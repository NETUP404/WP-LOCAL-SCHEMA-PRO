<?php
// Detectar si Yoast está activo
$yoast_active = defined('WPSEO_VERSION');

// Sidebar de secciones
$sections = [
    'general'      => ['General', 'dashicons-admin-generic'],
    'tienda'       => ['Tienda', 'dashicons-cart'],
    'directorios'  => ['Directorios', 'dashicons-location'],
    'organizacion' => ['Organización', 'dashicons-building'],
    'persona'      => ['Persona', 'dashicons-admin-users'],
    'extra'        => ['Extra', 'dashicons-star-filled'],
];

// Detectar sección activa
$active_section = isset($_GET['section']) ? sanitize_key($_GET['section']) : 'general';

// Mensajes/avisos por sección
$advices = [
    'general' => [
        'type' => 'info',
        'text' => '<strong>Consejo:</strong> Completa todos los campos NAP (nombre, dirección, teléfono) para maximizar el SEO local. <br>Los cambios se guardan sección a sección.',
    ],
    'tienda' => [
        'type' => 'info',
        'text' => '<strong>Guía:</strong> Añade los datos de tu tienda física o externa. Configura productos y métodos de pago solo si tienes tienda propia.',
    ],
    'directorios' => [
        'type' => 'info',
        'text' => '<strong>Tip:</strong> Añade enlaces a tus perfiles en directorios locales para mejorar la autoridad y coherencia NAP.',
    ],
    'organizacion' => [
        'type' => $yoast_active ? 'warning' : 'info',
        'text' => $yoast_active
            ? '<strong>¡Atención!</strong> Detectamos que tienes <b>Yoast SEO</b> activo. Para evitar duplicados de schema <b>de Organización</b>, usa solo uno de los plugins para este dato: <ul style="margin:8px 0 0 20px;"><li><b>O bien</b> dejas el schema en Yoast (desactívalo aquí),</li><li><b>O bien</b> desactívalo en Yoast y mantenlo aquí.</li></ul><i>Yoast Local SEO Premium puede añadir schemas adicionales.</i>'
            : '<strong>Consejo:</strong> El schema de Organización es clave para Google. Rellena solo si eres empresa, no persona física.',
    ],
    'persona' => [
        'type' => $yoast_active ? 'warning' : 'info',
        'text' => $yoast_active
            ? '<strong>¡Atención!</strong> Detectamos que tienes <b>Yoast SEO</b> activo. Para evitar duplicados de schema <b>de Persona</b>, usa solo uno de los plugins para este dato: <ul style="margin:8px 0 0 20px;"><li><b>O bien</b> dejas el schema en Yoast (desactívalo aquí),</li><li><b>O bien</b> desactívalo en Yoast y mantenlo aquí.</li></ul>'
            : '<strong>Consejo:</strong> Rellena solo si tu web representa a una persona física (autónomo, profesional, etc).',
    ],
    'extra' => [
        'type' => 'info',
        'text' => '<strong>Útil:</strong> Herramientas adicionales y checklist para SEO local avanzado.',
    ],
];

// Render
?>
<div class="wp-lsp-wrapper">
  <aside class="wp-lsp-sidebar">
    <ul>
      <?php foreach ($sections as $slug => [$label, $icon]) : ?>
        <li class="<?php if ($active_section === $slug) echo 'active'; ?>">
          <a href="?page=wp-local-schema-pro&section=<?php echo esc_attr($slug); ?>">
            <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            <?php echo esc_html($label); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>
  <main class="wp-lsp-content">
    <?php if (!empty($advices[$active_section])) : 
      $advice = $advices[$active_section];
      $notice_class = $advice['type'] === 'warning' ? 'notice-warning' : 'notice-info';
    ?>
      <div class="wp-lsp-advice notice <?php echo $notice_class; ?>">
        <?php echo $advice['text']; ?>
      </div>
    <?php endif; ?>
    <form method="post" class="wp-lsp-form">
      <?php
        // Aquí debes incluir dinámicamente los campos de cada sección
        // Ejemplo:
        // include __DIR__ . '/fields-' . $active_section . '.php';
      ?>
      <div class="wp-lsp-buttons">
        <button type="submit" class="button button-primary"><span class="dashicons dashicons-yes"></span> Guardar cambios</button>
        <button type="reset" class="button"><span class="dashicons dashicons-update"></span> Restablecer</button>
        <a href="https://tudocumentacion.com" target="_blank" class="button button-secondary"><span class="dashicons dashicons-editor-help"></span> Ayuda</a>
      </div>
    </form>
  </main>
</div>