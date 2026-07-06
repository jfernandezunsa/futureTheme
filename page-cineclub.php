<?php

/**
 * Página Cineclub.
 *
 * Estructura basada en theme-model.html:
 * - Hero
 * - Filtro por mes/año
 * - Ciclo destacado
 * - Programación mensual
 * - Botón Cómo llegar
 * - Banner inferior widget
 * - Info strip desde Espacio Cultural asociado
 *
 * @package FutureTheme
 */

get_header();

$page_id = get_queried_object_id();

$hero_image = get_the_post_thumbnail_url($page_id, 'full');

if (! $hero_image) {
  $hero_image = get_template_directory_uri() . '/assets/img/cineclub-default.jpg';
}

/*
 * Meses en español.
 */
$meses_por_numero = array(
  '01' => 'enero',
  '02' => 'febrero',
  '03' => 'marzo',
  '04' => 'abril',
  '05' => 'mayo',
  '06' => 'junio',
  '07' => 'julio',
  '08' => 'agosto',
  '09' => 'septiembre',
  '10' => 'octubre',
  '11' => 'noviembre',
  '12' => 'diciembre',
);

$meses = array(
  ''           => __('Todos', 'futuretheme'),
  'enero'      => __('Enero', 'futuretheme'),
  'febrero'    => __('Febrero', 'futuretheme'),
  'marzo'      => __('Marzo', 'futuretheme'),
  'abril'      => __('Abril', 'futuretheme'),
  'mayo'       => __('Mayo', 'futuretheme'),
  'junio'      => __('Junio', 'futuretheme'),
  'julio'      => __('Julio', 'futuretheme'),
  'agosto'     => __('Agosto', 'futuretheme'),
  'septiembre' => __('Septiembre', 'futuretheme'),
  'octubre'    => __('Octubre', 'futuretheme'),
  'noviembre'  => __('Noviembre', 'futuretheme'),
  'diciembre'  => __('Diciembre', 'futuretheme'),
);

$allowed_months = array_keys($meses);

$mes_actual_numero = date_i18n('m');
$anio_actual       = absint(date_i18n('Y'));

$mes_actual = isset($meses_por_numero[$mes_actual_numero])
  ? $meses_por_numero[$mes_actual_numero]
  : '';

/*
 * Filtros GET.
 *
 * /cineclub/?mes=mayo&anio=2026
 *
 * Si no hay filtros, muestra el mes y año actual.
 */
$tiene_filtro_mes  = isset($_GET['mes']);
$tiene_filtro_anio = isset($_GET['anio']);

$current_mes = $tiene_filtro_mes
  ? sanitize_text_field(wp_unslash($_GET['mes']))
  : $mes_actual;

$current_anio = $tiene_filtro_anio
  ? absint($_GET['anio'])
  : $anio_actual;

if (! in_array($current_mes, $allowed_months, true)) {
  $current_mes = $mes_actual;
}

if ($current_anio < 2000 || $current_anio > 2100) {
  $current_anio = $anio_actual;
}

/*
 * Rango de años para selector.
 */
$anios = array();

for ($year = $anio_actual + 1; $year >= $anio_actual - 8; $year--) {
  $anios[] = $year;
}

/*
 * Rango de fechas para buscar proyecciones.
 */
$numero_mes_actual = array_search($current_mes, $meses_por_numero, true);

$fecha_inicio_mes = '';
$fecha_fin_mes    = '';

if (! empty($numero_mes_actual) && ! empty($current_anio)) {
  $fecha_inicio_mes = sprintf(
    '%04d-%02d-01',
    absint($current_anio),
    absint($numero_mes_actual)
  );

  $fecha_fin_mes = date(
    'Y-m-t',
    strtotime($fecha_inicio_mes)
  );
}

/*
 * Query de proyecciones.
 */
$meta_query = array();

if (! empty($fecha_inicio_mes) && ! empty($fecha_fin_mes)) {
  $meta_query[] = array(
    'key'     => '_futuretheme_proyeccion_fecha',
    'value'   => array($fecha_inicio_mes, $fecha_fin_mes),
    'compare' => 'BETWEEN',
    'type'    => 'DATE',
  );
} elseif (! empty($current_anio)) {
  $meta_query[] = array(
    'key'     => '_futuretheme_proyeccion_fecha',
    'value'   => array(
      $current_anio . '-01-01',
      $current_anio . '-12-31',
    ),
    'compare' => 'BETWEEN',
    'type'    => 'DATE',
  );
}

$proyecciones_args = array(
  'post_type'      => 'proyeccion_cine',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'meta_key'       => '_futuretheme_proyeccion_fecha',
  'orderby'        => array(
    'meta_value' => 'ASC',
    'menu_order' => 'ASC',
    'date'       => 'DESC',
  ),
);

if (! empty($meta_query)) {
  $proyecciones_args['meta_query'] = $meta_query;
}

$proyecciones_query = new WP_Query($proyecciones_args);

/*
 * Ciclo destacado seleccionado en la página.
 */
$ciclo_destacado_id = get_post_meta($page_id, '_futuretheme_cineclub_ciclo_id', true);

/*
 * Si no se eligió ciclo destacado, intentar tomar el ciclo de la primera proyección del mes.
 */
if (empty($ciclo_destacado_id) && $proyecciones_query->have_posts()) {
  $first_proyeccion_id = $proyecciones_query->posts[0]->ID;
  $ciclo_destacado_id  = get_post_meta($first_proyeccion_id, '_futuretheme_proyeccion_ciclo_id', true);
}

/*
 * Espacio Cultural asociado.
 */
$cineclub_espacio_id = get_post_meta($page_id, '_futuretheme_cineclub_espacio_id', true);

$cine_horario   = '';
$cine_ubicacion = '';
$cine_correo    = '';
$cine_maps      = '';

if (! empty($cineclub_espacio_id)) {
  $cine_horario   = get_post_meta($cineclub_espacio_id, '_futuretheme_espacio_horario', true);
  $cine_ubicacion = get_post_meta($cineclub_espacio_id, '_futuretheme_espacio_direccion', true);
  $cine_correo    = get_post_meta($cineclub_espacio_id, '_futuretheme_espacio_correo', true);
  $cine_maps      = get_post_meta($cineclub_espacio_id, '_futuretheme_espacio_maps', true);
}

/*
 * Textos de cabecera.
 */
$mes_label = ! empty($current_mes) && isset($meses[$current_mes])
  ? $meses[$current_mes]
  : '';

$hero_periodo = trim($mes_label . ' ' . $current_anio);

if (! empty($hero_periodo)) {
  $hero_subtitle = sprintf(
    /* translators: %s: month and year. */
    __('Programación %s', 'futuretheme'),
    $hero_periodo
  );
} else {
  $hero_subtitle = __('Programación vigente', 'futuretheme');
}

$viendo_mes_actual = (
  $current_mes === $mes_actual &&
  absint($current_anio) === absint($anio_actual)
);
?>

<main id="primary" class="site-main">

  <div id="page-cineclub" class="page page-cineclub">

    <div class="pg-hero cineclub-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php echo esc_attr(get_the_title($page_id)); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php esc_html_e('Cine Club UNSA', 'futuretheme'); ?>
        </div>

        <h1>
          <?php echo esc_html(get_the_title($page_id)); ?>
        </h1>

        <?php if (! empty($hero_subtitle)) : ?>
          <div class="sub">
            <?php echo esc_html($hero_subtitle); ?>
          </div>
        <?php endif; ?>

      </div>

    </div>

    <form
      class="cine-filter-bar"
      method="get"
      action="<?php echo esc_url(get_permalink($page_id)); ?>">

      <span class="cine-filter-label">
        <?php esc_html_e('Filtrar por mes:', 'futuretheme'); ?>
      </span>

      <label class="screen-reader-text" for="futuretheme_cine_filter_mes">
        <?php esc_html_e('Mes', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_cine_filter_mes"
        class="cine-select"
        name="mes"
        onchange="this.form.submit()">
        <?php foreach ($meses as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($current_mes, $value); ?>>
            <?php echo esc_html($label); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label class="screen-reader-text" for="futuretheme_cine_filter_anio">
        <?php esc_html_e('Año', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_cine_filter_anio"
        class="cine-select"
        name="anio"
        onchange="this.form.submit()">
        <option value="">
          <?php esc_html_e('Todos', 'futuretheme'); ?>
        </option>

        <?php foreach ($anios as $anio) : ?>
          <option value="<?php echo esc_attr($anio); ?>" <?php selected($current_anio, $anio); ?>>
            <?php echo esc_html($anio); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if (! $viendo_mes_actual) : ?>
        <a class="cine-filter-reset" href="<?php echo esc_url(get_permalink($page_id)); ?>">
          <?php esc_html_e('Mes actual', 'futuretheme'); ?>
        </a>
      <?php endif; ?>

      <noscript>
        <button type="submit">
          <?php esc_html_e('Filtrar', 'futuretheme'); ?>
        </button>
      </noscript>

    </form>

    <?php
    /*
     * Ciclo destacado.
     */
    if (! empty($ciclo_destacado_id)) :
      set_query_var('futuretheme_cineclub_ciclo_id', absint($ciclo_destacado_id));
      get_template_part('template-parts/cineclub/ciclo', 'destacado');
    endif;
    ?>

    <div class="wrap cineclub-programacion">

      <?php
      /*
       * Contenido opcional de la página WordPress.
       */
      if (have_posts()) :
        while (have_posts()) :
          the_post();

          if (trim(wp_strip_all_tags(get_the_content())) || has_blocks(get_the_content())) :
      ?>
            <div class="cineclub-intro fade-in visible">
              <?php the_content(); ?>
            </div>
      <?php
          endif;

        endwhile;
      endif;
      ?>

      <div class="prog-header fade-in visible">

        <h2>
          <?php
          if (! empty($mes_label)) {
            printf(
              /* translators: %s: month name. */
              esc_html__('Programación %s', 'futuretheme'),
              esc_html($mes_label)
            );
          } else {
            esc_html_e('Programación', 'futuretheme');
          }
          ?>
        </h2>

        <?php if (! empty($current_anio)) : ?>
          <span class="month-badge">
            <?php echo esc_html($current_anio); ?>
          </span>
        <?php endif; ?>

      </div>

      <?php if ($proyecciones_query->have_posts()) : ?>

        <div class="film-grid fade-in visible">

          <?php
          while ($proyecciones_query->have_posts()) :
            $proyecciones_query->the_post();

            get_template_part('template-parts/cineclub/card', 'proyeccion');

          endwhile;
          ?>

        </div>

        <?php wp_reset_postdata(); ?>

      <?php else : ?>

        <div class="film-empty">
          <h2>
            <?php esc_html_e('No hay proyecciones registradas', 'futuretheme'); ?>
          </h2>

          <p>
            <?php
            printf(
              /* translators: %s: month and year. */
              esc_html__('No se encontraron proyecciones para %s.', 'futuretheme'),
              esc_html($hero_periodo)
            );
            ?>
          </p>
        </div>

      <?php endif; ?>

      <?php if (! empty($cine_maps) || ! empty($cine_ubicacion)) : ?>

        <div class="cine-como-llegar">

          <?php if (! empty($cine_maps)) : ?>

            <a
              class="btn btn-dark"
              href="<?php echo esc_url($cine_maps); ?>"
              target="_blank"
              rel="noopener noreferrer">
              <?php esc_html_e('📍 Cómo llegar', 'futuretheme'); ?>
            </a>

          <?php else : ?>

            <button
              type="button"
              class="btn btn-dark"
              onclick="alert('<?php echo esc_js($cine_ubicacion); ?>')">
              <?php esc_html_e('📍 Cómo llegar', 'futuretheme'); ?>
            </button>

          <?php endif; ?>

        </div>

      <?php endif; ?>

    </div>

    <?php
    /*
     * Banner inferior widget.
     */
    if (is_active_sidebar('cineclub-banner-inferior')) :
      dynamic_sidebar('cineclub-banner-inferior');
    endif;
    ?>

    <?php if (! empty($cine_horario) || ! empty($cine_ubicacion) || ! empty($cine_correo)) : ?>

      <div class="info-strip cineclub-info-strip">
        <div class="info-strip-inner">

          <?php if (! empty($cine_horario)) : ?>
            <div class="info-item">
              <span class="lbl"><?php esc_html_e('Horario', 'futuretheme'); ?></span>
              <span class="val"><?php echo nl2br(esc_html($cine_horario)); ?></span>
            </div>
          <?php endif; ?>

          <?php if (! empty($cine_ubicacion)) : ?>
            <div class="info-item">
              <span class="lbl"><?php esc_html_e('Ubicación', 'futuretheme'); ?></span>
              <span class="val"><?php echo nl2br(esc_html($cine_ubicacion)); ?></span>
            </div>
          <?php endif; ?>

          <?php if (! empty($cine_correo)) : ?>
            <div class="info-item">
              <span class="lbl"><?php esc_html_e('Correo', 'futuretheme'); ?></span>
              <span class="val"><?php echo esc_html(antispambot($cine_correo)); ?></span>
            </div>
          <?php endif; ?>

        </div>
      </div>

    <?php endif; ?>

  </div>

</main>

<?php
get_footer();
