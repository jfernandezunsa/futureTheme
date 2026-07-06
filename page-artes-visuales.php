<?php

/**
 * Página Artes Visuales.
 *
 * Estructura basada en theme-model.html:
 * - Hero
 * - Barra de filtros av2-filter-bar
 * - Grid av2-grid
 *
 * Por defecto muestra las exposiciones del mes y año actual.
 *
 * @package FutureTheme
 */

get_header();

$page_id = get_queried_object_id();

$hero_image = get_the_post_thumbnail_url($page_id, 'full');

if (! $hero_image) {
  $hero_image = get_template_directory_uri() . '/assets/img/artes-visuales-default.jpg';
}

/*
 * Meses en español para guardar/consultar meta:
 * _futuretheme_expo_mes
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

$mes_actual_numero = date_i18n('m');
$anio_actual       = absint(date_i18n('Y'));

$mes_actual = isset($meses_por_numero[$mes_actual_numero])
  ? $meses_por_numero[$mes_actual_numero]
  : '';

/*
 * Filtros GET:
 * /artes-visuales/?mes=mayo&anio=2026
 *
 * Si no hay filtros en la URL, se usa por defecto
 * el mes y año actual.
 */
$tiene_filtro_mes  = isset($_GET['mes']);
$tiene_filtro_anio = isset($_GET['anio']);

$current_mes = $tiene_filtro_mes
  ? sanitize_text_field(wp_unslash($_GET['mes']))
  : $mes_actual;

$current_anio = $tiene_filtro_anio
  ? absint($_GET['anio'])
  : $anio_actual;

$allowed_months = array(
  '',
  'enero',
  'febrero',
  'marzo',
  'abril',
  'mayo',
  'junio',
  'julio',
  'agosto',
  'septiembre',
  'octubre',
  'noviembre',
  'diciembre',
);

if (! in_array($current_mes, $allowed_months, true)) {
  $current_mes = $mes_actual;
}

if ($current_anio < 2000 || $current_anio > 2100) {
  $current_anio = $anio_actual;
}

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

/*
 * Rango de años para el selector.
 */
$anios = array();

for ($year = $anio_actual + 1; $year >= $anio_actual - 8; $year--) {
  $anios[] = $year;
}

/*
 * Query de exposiciones.
 */
$meta_query = array();

if (! empty($current_mes)) {
  $meta_query[] = array(
    'key'     => '_futuretheme_expo_mes',
    'value'   => $current_mes,
    'compare' => '=',
  );
}

if (! empty($current_anio)) {
  $meta_query[] = array(
    'key'     => '_futuretheme_expo_anio',
    'value'   => $current_anio,
    'compare' => '=',
    'type'    => 'NUMERIC',
  );
}

$query_args = array(
  'post_type'      => 'exposicion',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => array(
    'menu_order' => 'ASC',
    'date'       => 'DESC',
  ),
  'tax_query'      => array(
    array(
      'taxonomy' => 'categoria_exposicion',
      'field'    => 'slug',
      'terms'    => array('artes-visuales'),
    ),
  ),
);

if (! empty($meta_query)) {
  $query_args['meta_query'] = $meta_query;
}

$exposiciones_query = new WP_Query($query_args);

/*
 * Subtítulo del hero.
 */
$hero_periodo = trim(ucfirst($current_mes) . ' ' . $current_anio);

if (! empty($hero_periodo)) {
  $hero_subtitle = sprintf(
    /* translators: %s: periodo de exposición. */
    __('Exposiciones %s', 'futuretheme'),
    $hero_periodo
  );
} else {
  $hero_subtitle = __('Exposiciones vigentes', 'futuretheme');
}

/*
 * Saber si se está viendo un periodo distinto al mes actual.
 */
$viendo_mes_actual = (
  $current_mes === $mes_actual &&
  absint($current_anio) === absint($anio_actual)
);
?>

<main id="primary" class="site-main">

  <div id="page-artes-visuales" class="page page-artes-visuales">

    <div class="pg-hero artes-visuales-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php echo esc_attr(get_the_title($page_id)); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php esc_html_e('Galerías de Arte', 'futuretheme'); ?>
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
      class="av2-filter-bar"
      method="get"
      action="<?php echo esc_url(get_permalink($page_id)); ?>">

      <span class="av2-filter-label">
        <?php esc_html_e('Filtrar por mes:', 'futuretheme'); ?>
      </span>

      <label class="screen-reader-text" for="futuretheme_expo_filter_mes">
        <?php esc_html_e('Mes', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_expo_filter_mes"
        class="av2-select"
        name="mes"
        onchange="this.form.submit()">
        <?php foreach ($meses as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($current_mes, $value); ?>>
            <?php echo esc_html($label); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label class="screen-reader-text" for="futuretheme_expo_filter_anio">
        <?php esc_html_e('Año', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_expo_filter_anio"
        class="av2-select"
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
        <a class="av2-filter-reset" href="<?php echo esc_url(get_permalink($page_id)); ?>">
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
     * Contenido opcional de la página WordPress.
     * Si no hay contenido, no se muestra.
     */
    if (have_posts()) :
      while (have_posts()) :
        the_post();

        if (trim(wp_strip_all_tags(get_the_content())) || has_blocks(get_the_content())) :
    ?>
          <div class="artes-visuales-intro">
            <?php the_content(); ?>
          </div>
    <?php
        endif;

      endwhile;
    endif;
    ?>

    <?php if ($exposiciones_query->have_posts()) : ?>

      <div class="av2-grid">

        <?php
        while ($exposiciones_query->have_posts()) :
          $exposiciones_query->the_post();

          get_template_part('template-parts/exposiciones/card', 'exposicion');

        endwhile;
        ?>

      </div>

      <?php wp_reset_postdata(); ?>

    <?php else : ?>

      <div class="av2-grid">

        <div class="av2-empty exposiciones-empty">
          <h2>
            <?php esc_html_e('No hay exposiciones registradas', 'futuretheme'); ?>
          </h2>

          <p>
            <?php
            printf(
              /* translators: %s: periodo de exposición. */
              esc_html__('No se encontraron exposiciones para %s.', 'futuretheme'),
              esc_html($hero_periodo)
            );
            ?>
          </p>
        </div>

      </div>

    <?php endif; ?>

    <?php
    /*
 * Zona de widget: Comunidad artística.
 */
    if (is_active_sidebar('artes-visuales-comunidad')) :
      dynamic_sidebar('artes-visuales-comunidad');
    endif;
    ?>

    <?php
    /*
 * Info strip de Artes Visuales.
 * Toma los datos desde el post personalizado Espacio Cultural
 * seleccionado en la edición de la página.
 */
    $artes_visuales_espacio_id = get_post_meta($page_id, '_futuretheme_artes_visuales_espacio_id', true);

    if (! empty($artes_visuales_espacio_id)) :

      $av_horario   = get_post_meta($artes_visuales_espacio_id, '_futuretheme_espacio_horario', true);
      $av_ubicacion = get_post_meta($artes_visuales_espacio_id, '_futuretheme_espacio_direccion', true);
      $av_correo    = get_post_meta($artes_visuales_espacio_id, '_futuretheme_espacio_correo', true);

      if (! empty($av_horario) || ! empty($av_ubicacion) || ! empty($av_correo)) :
    ?>

        <div class="av2-info-strip">
          <div class="av2-info-inner">

            <?php if (! empty($av_horario)) : ?>
              <div class="info-item">
                <span class="lbl"><?php esc_html_e('Horario', 'futuretheme'); ?></span>
                <span class="val"><?php echo nl2br(esc_html($av_horario)); ?></span>
              </div>
            <?php endif; ?>

            <?php if (! empty($av_ubicacion)) : ?>
              <div class="info-item">
                <span class="lbl"><?php esc_html_e('Ubicación', 'futuretheme'); ?></span>
                <span class="val"><?php echo nl2br(esc_html($av_ubicacion)); ?></span>
              </div>
            <?php endif; ?>

            <?php if (! empty($av_correo)) : ?>
              <div class="info-item">
                <span class="lbl"><?php esc_html_e('Correo', 'futuretheme'); ?></span>
                <span class="val"><?php echo esc_html(antispambot($av_correo)); ?></span>
              </div>
            <?php endif; ?>

          </div>
        </div>

    <?php
      endif;
    endif;
    ?>


  </div>

</main>

<?php
get_footer();
