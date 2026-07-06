<?php

/**
 * Card individual de Proyección Cineclub.
 *
 * Estructura basada en theme-model.html:
 *
 * <div class="film-card">
 *   <div class="film-img"><img src="..." alt=""></div>
 *   <div class="film-info">
 *     <h3>Título</h3>
 *     <div class="dir">Dir: Director</div>
 *     <div class="film-date-row">
 *       <div class="film-date">
 *         <div class="film-day">02</div>
 *         <div class="film-day-info">Lun<br>18:30 hrs</div>
 *       </div>
 *       <span class="film-libre">Ingreso Libre</span>
 *     </div>
 *   </div>
 * </div>
 *
 * @package FutureTheme
 */

$proyeccion_id = get_the_ID();

$image_url = get_the_post_thumbnail_url($proyeccion_id, 'large');

if (! $image_url) {
  $image_url = get_template_directory_uri() . '/assets/img/proyeccion-default.jpg';
}

$director = get_post_meta($proyeccion_id, '_futuretheme_proyeccion_director', true);
$fecha    = get_post_meta($proyeccion_id, '_futuretheme_proyeccion_fecha', true);
$hora     = get_post_meta($proyeccion_id, '_futuretheme_proyeccion_hora', true);
$ingreso  = get_post_meta($proyeccion_id, '_futuretheme_proyeccion_ingreso', true);

$dia_numero = '';
$dia_nombre = '';
$hora_label = '';

if (! empty($fecha)) {
  $timestamp = strtotime($fecha);

  if ($timestamp) {
    $dia_numero = date_i18n('d', $timestamp);

    $dias = array(
      'Monday'    => __('Lun', 'futuretheme'),
      'Tuesday'   => __('Mar', 'futuretheme'),
      'Wednesday' => __('Mié', 'futuretheme'),
      'Thursday'  => __('Jue', 'futuretheme'),
      'Friday'    => __('Vie', 'futuretheme'),
      'Saturday'  => __('Sáb', 'futuretheme'),
      'Sunday'    => __('Dom', 'futuretheme'),
    );

    $day_key = date_i18n('l', $timestamp);

    if (isset($dias[$day_key])) {
      $dia_nombre = $dias[$day_key];
    } else {
      $dia_nombre = date_i18n('D', $timestamp);
    }
  }
}

if (! empty($hora)) {
  $hora_timestamp = strtotime($hora);

  if ($hora_timestamp) {
    $hora_label = date_i18n('H:i', $hora_timestamp) . ' hrs';
  } else {
    $hora_label = $hora;
  }
}

if (empty($ingreso)) {
  $ingreso = __('Ingreso libre', 'futuretheme');
}

$resumen = '';

if (has_excerpt()) {
  $resumen = get_the_excerpt();
}
?>

<article id="proyeccion-<?php the_ID(); ?>" <?php post_class('film-card'); ?>>

  <div class="film-img">
    <img
      src="<?php echo esc_url($image_url); ?>"
      alt="<?php echo esc_attr(get_the_title()); ?>"
      loading="lazy">
  </div>

  <div class="film-info">

    <h3><?php the_title(); ?></h3>

    <?php if (! empty($director)) : ?>
      <div class="dir">
        <?php
        printf(
          /* translators: %s: director name. */
          esc_html__('Dir: %s', 'futuretheme'),
          esc_html($director)
        );
        ?>
      </div>
    <?php endif; ?>

    <?php if (! empty($resumen)) : ?>
      <div class="film-summary">
        <?php echo esc_html(wp_trim_words($resumen, 22, '...')); ?>
      </div>
    <?php endif; ?>

    <div class="film-date-row">

      <?php if (! empty($dia_numero) || ! empty($dia_nombre) || ! empty($hora_label)) : ?>

        <div class="film-date">

          <?php if (! empty($dia_numero)) : ?>
            <div class="film-day">
              <?php echo esc_html($dia_numero); ?>
            </div>
          <?php endif; ?>

          <div class="film-day-info">
            <?php if (! empty($dia_nombre)) : ?>
              <?php echo esc_html($dia_nombre); ?>
            <?php endif; ?>

            <?php if (! empty($hora_label)) : ?>
              <br>
              <?php echo esc_html($hora_label); ?>
            <?php endif; ?>
          </div>

        </div>

      <?php endif; ?>

      <?php if (! empty($ingreso)) : ?>
        <span class="film-libre">
          <?php echo esc_html($ingreso); ?>
        </span>
      <?php endif; ?>

    </div>

  </div>

</article>
