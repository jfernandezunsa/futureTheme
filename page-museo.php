<?php

/**
 * Página Museo.
 *
 * Estructura:
 * - Hero
 * - Filtro por mes/año
 * - Programa Museo del mes
 * - Pieza destacada del programa
 * - Infografía de la pieza destacada
 * - Texto llamada
 * - Otras piezas destacadas
 * - Widget acordeón informativo
 *
 * @package FutureTheme
 */

get_header();

$page_id = get_queried_object_id();

$hero_image = get_the_post_thumbnail_url($page_id, 'full');

if (! $hero_image) {
  $hero_image = get_template_directory_uri() . '/assets/img/museo-default.jpg';
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
 * Filtros GET:
 *
 * /museo/?mes=mayo&anio=2026
 *
 * Si no hay filtros, se usa mes y año actual.
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
 * Selector de años.
 */
$anios = array();

for ($year = $anio_actual + 1; $year >= $anio_actual - 8; $year--) {
  $anios[] = $year;
}

/*
 * Etiquetas visibles.
 */
$mes_label = ! empty($current_mes) && isset($meses[$current_mes])
  ? $meses[$current_mes]
  : '';

$periodo_label = trim($mes_label . ' ' . $current_anio);

if (empty($periodo_label)) {
  $periodo_label = __('Vigente', 'futuretheme');
}

$viendo_mes_actual = (
  $current_mes === $mes_actual &&
  absint($current_anio) === absint($anio_actual)
);

/*
 * Buscar Programa Museo del mes/año.
 */
$programa_args = array(
  'post_type'      => 'programa_museo',
  'posts_per_page' => 1,
  'post_status'    => 'publish',
  'orderby'        => array(
    'menu_order' => 'ASC',
    'date'       => 'DESC',
  ),
  'meta_query'     => array(
    'relation' => 'AND',
    array(
      'key'     => '_futuretheme_programa_museo_mes',
      'value'   => $current_mes,
      'compare' => '=',
    ),
    array(
      'key'     => '_futuretheme_programa_museo_anio',
      'value'   => absint($current_anio),
      'compare' => '=',
      'type'    => 'NUMERIC',
    ),
  ),
);

$programa_query = new WP_Query($programa_args);

$programa_id     = 0;
$pieza_id        = 0;
$texto_llamada   = '';
$programa_titulo = '';

if ($programa_query->have_posts()) {
  $programa_query->the_post();

  $programa_id     = get_the_ID();
  $programa_titulo = get_the_title($programa_id);
  $pieza_id        = get_post_meta($programa_id, '_futuretheme_programa_museo_pieza_id', true);
  $texto_llamada   = get_post_meta($programa_id, '_futuretheme_programa_museo_texto_llamada', true);

  wp_reset_postdata();
}

if (empty($texto_llamada)) {
  $texto_llamada = __('Más de 10 000 piezas arqueológicas — Ven y visítanos', 'futuretheme');
}

/*
 * Otras piezas destacadas.
 * Se excluye la pieza principal del programa.
 */
$otras_piezas_args = array(
  'post_type'      => 'pieza_museo',
  'posts_per_page' => 3,
  'post_status'    => 'publish',
  'orderby'        => array(
    'menu_order' => 'ASC',
    'date'       => 'DESC',
  ),
  'meta_query'     => array(
    array(
      'key'     => '_futuretheme_pieza_destacada',
      'value'   => '1',
      'compare' => '=',
    ),
  ),
);

if (! empty($pieza_id)) {
  $otras_piezas_args['post__not_in'] = array(absint($pieza_id));
}

$otras_piezas_query = new WP_Query($otras_piezas_args);

/*
 * Fallback:
 * Si no hay piezas marcadas como destacadas, mostrar últimas piezas,
 * excluyendo la pieza principal.
 */
if (! $otras_piezas_query->have_posts()) {
  wp_reset_postdata();

  $otras_piezas_args = array(
    'post_type'      => 'pieza_museo',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => array(
      'menu_order' => 'ASC',
      'date'       => 'DESC',
    ),
  );

  if (! empty($pieza_id)) {
    $otras_piezas_args['post__not_in'] = array(absint($pieza_id));
  }

  $otras_piezas_query = new WP_Query($otras_piezas_args);
}
?>

<main id="primary" class="site-main">

  <div id="page-museo" class="page page-museo">

    <div class="pg-hero museo-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php echo esc_attr(get_the_title($page_id)); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php
          printf(
            /* translators: %s: period label, e.g. Mayo 2026. */
            esc_html__('Museo Arqueológico · %s', 'futuretheme'),
            esc_html($periodo_label)
          );
          ?>
        </div>

        <h1>
          <?php echo esc_html(get_the_title($page_id)); ?>
        </h1>

      </div>

    </div>

    <form
      class="museo-filter-bar"
      method="get"
      action="<?php echo esc_url(get_permalink($page_id)); ?>">

      <span class="museo-filter-label">
        <?php esc_html_e('Filtrar por mes:', 'futuretheme'); ?>
      </span>

      <label class="screen-reader-text" for="futuretheme_museo_filter_mes">
        <?php esc_html_e('Mes', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_museo_filter_mes"
        class="museo-select"
        name="mes"
        onchange="this.form.submit()">
        <?php foreach ($meses as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($current_mes, $value); ?>>
            <?php echo esc_html($label); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label class="screen-reader-text" for="futuretheme_museo_filter_anio">
        <?php esc_html_e('Año', 'futuretheme'); ?>
      </label>

      <select
        id="futuretheme_museo_filter_anio"
        class="museo-select"
        name="anio"
        onchange="this.form.submit()">
        <option value="">
          <?php esc_html_e('Todos', 'futuretheme'); ?>
        </option>

        <?php foreach ($anios as $anio) : ?>
          <option value="<?php echo esc_attr($anio); ?>" <?php selected(absint($current_anio), absint($anio)); ?>>
            <?php echo esc_html($anio); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if (! $viendo_mes_actual) : ?>
        <a class="museo-filter-reset" href="<?php echo esc_url(get_permalink($page_id)); ?>">
          <?php esc_html_e('Mes actual', 'futuretheme'); ?>
        </a>
      <?php endif; ?>

      <noscript>
        <button type="submit">
          <?php esc_html_e('Filtrar', 'futuretheme'); ?>
        </button>
      </noscript>

    </form>

    <section class="museo-heading-section">

      <div class="museo-heading-inner">

        <div class="museo-section-label">
          <?php esc_html_e('Destaque del mes', 'futuretheme'); ?>
        </div>

        <h2>
          <?php
          if (! empty($programa_titulo)) {
            echo esc_html($programa_titulo);
          } else {
            printf(
              /* translators: %s: period label. */
              esc_html__('Pieza del Mes · %s', 'futuretheme'),
              esc_html($periodo_label)
            );
          }
          ?>
        </h2>

      </div>

    </section>

    <?php if (! empty($pieza_id)) : ?>

      <?php
      set_query_var('futuretheme_museo_pieza_id', absint($pieza_id));
      set_query_var('futuretheme_museo_card_mode', 'destacada');
      set_query_var('futuretheme_museo_periodo_label', $periodo_label);

      get_template_part('template-parts/museo/card', 'pieza');
      ?>

    <?php else : ?>

      <section class="museo-empty">

        <h2>
          <?php esc_html_e('No hay pieza destacada registrada', 'futuretheme'); ?>
        </h2>

        <p>
          <?php
          printf(
            /* translators: %s: period label. */
            esc_html__('No se encontró un programa del museo con pieza destacada para %s.', 'futuretheme'),
            esc_html($periodo_label)
          );
          ?>
        </p>

      </section>

    <?php endif; ?>

    <?php if (! empty($texto_llamada)) : ?>

      <section class="museo-callout wrap">

        <p>
          <?php echo esc_html($texto_llamada); ?>
        </p>

      </section>

    <?php endif; ?>

    <?php if ($otras_piezas_query->have_posts()) : ?>

      <section class="museo-otras-piezas wrap">

        <div class="museo-otras-header">

          <div class="museo-section-label">
            <?php esc_html_e('Colección del museo', 'futuretheme'); ?>
          </div>

          <h2>
            <?php esc_html_e('Otras piezas destacadas', 'futuretheme'); ?>
          </h2>

        </div>

        <div class="museo-card-grid">

          <?php
          while ($otras_piezas_query->have_posts()) :
            $otras_piezas_query->the_post();

            set_query_var('futuretheme_museo_pieza_id', get_the_ID());
            set_query_var('futuretheme_museo_card_mode', 'card');
            set_query_var('futuretheme_museo_periodo_label', '');

            get_template_part('template-parts/museo/card', 'pieza');

          endwhile;
          ?>

        </div>

      </section>

      <?php wp_reset_postdata(); ?>

    <?php endif; ?>

    <?php
    /*
     * Contenido opcional de la página Museo.
     * Si agregas texto en el editor de la página, aparecerá aquí.
     */
    if (have_posts()) :
      while (have_posts()) :
        the_post();

        if (trim(wp_strip_all_tags(get_the_content())) || has_blocks(get_the_content())) :
    ?>
          <section class="museo-page-content wrap">
            <?php the_content(); ?>
          </section>
    <?php
        endif;

      endwhile;
    endif;
    ?>

    <?php
    /*
     * Widget de acordeón informativo.
     */
    if (is_active_sidebar('museo-acordeon-informativo')) :
    ?>
      <section class="museo-acordeon-section wrap">
        <?php dynamic_sidebar('museo-acordeon-informativo'); ?>
      </section>
    <?php
    endif;
    ?>

  </div>

</main>

<?php
get_footer();
