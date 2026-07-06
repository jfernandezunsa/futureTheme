<?php

/**
 * Ciclo destacado del Cineclub.
 *
 * Estructura basada en theme-model.html:
 *
 * <div class="cine-ciclo">
 *   <div class="cine-film-grain"></div>
 *   <img src="..." alt="">
 *   <div class="cine-ciclo-inner">
 *     <div class="cine-ciclo-title">
 *       <div class="pre">De marzo a septiembre · 2026</div>
 *       <div class="scan-line"></div>
 *       <h2>Ciclo Hitchcock Kubrick</h2>
 *       <div class="post">Clásicos para obsesivos compulsivos</div>
 *       <div class="cine-ciclo-tags">
 *         <span>Sala Audiovisuales</span>
 *         <span class="cine-ciclo-free">Ingreso libre</span>
 *       </div>
 *     </div>
 *     <div class="cine-ciclo-info">
 *       <h4>Acerca del Ciclo</h4>
 *       <p>Descripción...</p>
 *       <div class="cine-ciclo-info-line"></div>
 *       <p class="cine-ciclo-schedule">18:30 hrs · Función diaria</p>
 *     </div>
 *   </div>
 * </div>
 *
 * @package FutureTheme
 */

$ciclo_id = get_query_var('futuretheme_cineclub_ciclo_id');

if (empty($ciclo_id)) {
  return;
}

$ciclo = get_post($ciclo_id);

if (! $ciclo || 'ciclo_cine' !== $ciclo->post_type || 'publish' !== $ciclo->post_status) {
  return;
}

$imagen = get_the_post_thumbnail_url($ciclo_id, 'full');

if (! $imagen) {
  $imagen = get_template_directory_uri() . '/assets/img/ciclo-cine-default.jpg';
}

$fecha_inicio = get_post_meta($ciclo_id, '_futuretheme_ciclo_fecha_inicio', true);
$fecha_fin    = get_post_meta($ciclo_id, '_futuretheme_ciclo_fecha_fin', true);
$bajada       = get_post_meta($ciclo_id, '_futuretheme_ciclo_bajada', true);
$horario      = get_post_meta($ciclo_id, '_futuretheme_ciclo_horario', true);
$ingreso      = get_post_meta($ciclo_id, '_futuretheme_ciclo_ingreso', true);
$sala         = get_post_meta($ciclo_id, '_futuretheme_ciclo_sala', true);

$titulo = get_the_title($ciclo_id);

/*
 * Periodo del ciclo.
 * Ejemplo: De marzo a septiembre · 2026
 */
$periodo = '';

if (! empty($fecha_inicio) || ! empty($fecha_fin)) {

  $inicio_ts = ! empty($fecha_inicio) ? strtotime($fecha_inicio) : false;
  $fin_ts    = ! empty($fecha_fin) ? strtotime($fecha_fin) : false;

  if ($inicio_ts && $fin_ts) {

    $mes_inicio = date_i18n('F', $inicio_ts);
    $mes_fin    = date_i18n('F', $fin_ts);
    $anio_fin   = date_i18n('Y', $fin_ts);

    if (strtolower($mes_inicio) === strtolower($mes_fin)) {
      $periodo = sprintf(
        /* translators: 1: month, 2: year. */
        __('%1$s · %2$s', 'futuretheme'),
        ucfirst($mes_inicio),
        $anio_fin
      );
    } else {
      $periodo = sprintf(
        /* translators: 1: start month, 2: end month, 3: year. */
        __('De %1$s a %2$s · %3$s', 'futuretheme'),
        strtolower($mes_inicio),
        strtolower($mes_fin),
        $anio_fin
      );
    }
  } elseif ($inicio_ts) {

    $periodo = sprintf(
      /* translators: 1: month, 2: year. */
      __('%1$s · %2$s', 'futuretheme'),
      ucfirst(date_i18n('F', $inicio_ts)),
      date_i18n('Y', $inicio_ts)
    );
  } elseif ($fin_ts) {

    $periodo = sprintf(
      /* translators: 1: month, 2: year. */
      __('%1$s · %2$s', 'futuretheme'),
      ucfirst(date_i18n('F', $fin_ts)),
      date_i18n('Y', $fin_ts)
    );
  }
}

/*
 * Descripción del ciclo:
 * usa el contenido principal del post.
 */
$descripcion = apply_filters('the_content', $ciclo->post_content);

$has_descripcion = ! empty(trim(wp_strip_all_tags($descripcion)));
?>

<section id="ciclo-cine-<?php echo esc_attr($ciclo_id); ?>" class="cine-ciclo fade-in visible">

  <div class="cine-film-grain" aria-hidden="true"></div>

  <img
    src="<?php echo esc_url($imagen); ?>"
    alt="<?php echo esc_attr($titulo); ?>">

  <div class="cine-ciclo-inner">

    <div class="cine-ciclo-title">

      <?php if (! empty($periodo)) : ?>
        <div class="pre">
          <?php echo esc_html($periodo); ?>
        </div>
      <?php endif; ?>

      <div class="scan-line" aria-hidden="true"></div>

      <h2>
        <?php echo esc_html($titulo); ?>
      </h2>

      <?php if (! empty($bajada)) : ?>
        <div class="post">
          <?php echo esc_html($bajada); ?>
        </div>
      <?php endif; ?>

      <?php if (! empty($sala) || ! empty($ingreso)) : ?>
        <div class="cine-ciclo-tags">

          <?php if (! empty($sala)) : ?>
            <span>
              <?php echo esc_html($sala); ?>
            </span>
          <?php endif; ?>

          <?php if (! empty($ingreso)) : ?>
            <span class="cine-ciclo-free">
              <?php echo esc_html($ingreso); ?>
            </span>
          <?php endif; ?>

        </div>
      <?php endif; ?>

    </div>

    <?php if ($has_descripcion || ! empty($horario)) : ?>

      <div class="cine-ciclo-info">

        <?php if ($has_descripcion) : ?>
          <h4>
            <?php esc_html_e('Acerca del Ciclo', 'futuretheme'); ?>
          </h4>

          <div class="cine-ciclo-description">
            <?php echo wp_kses_post($descripcion); ?>
          </div>
        <?php endif; ?>

        <?php if (! empty($horario)) : ?>
          <div class="cine-ciclo-info-line" aria-hidden="true"></div>

          <p class="cine-ciclo-schedule">
            <?php echo esc_html($horario); ?>
          </p>
        <?php endif; ?>

      </div>

    <?php endif; ?>

  </div>

</section>
