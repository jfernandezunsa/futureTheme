<?php

/**
 * Card / bloque de Pieza del Museo.
 *
 * Tiene dos modos:
 *
 * 1. Modo destacada:
 *    - Muestra la pieza principal del programa mensual.
 *    - Muestra imagen, título, descripción breve e infografía.
 *
 * 2. Modo card:
 *    - Muestra otras piezas destacadas en formato tarjeta.
 *
 * Uso desde page-museo.php:
 *
 * set_query_var( 'futuretheme_museo_pieza_id', $pieza_id );
 * set_query_var( 'futuretheme_museo_card_mode', 'destacada' );
 * set_query_var( 'futuretheme_museo_periodo_label', 'Mayo 2026' );
 * get_template_part( 'template-parts/museo/card', 'pieza' );
 *
 * @package FutureTheme
 */

$pieza_id = get_query_var('futuretheme_museo_pieza_id');
$mode     = get_query_var('futuretheme_museo_card_mode');
$periodo  = get_query_var('futuretheme_museo_periodo_label');

if (empty($pieza_id)) {
  $pieza_id = get_the_ID();
}

if (empty($mode)) {
  $mode = 'card';
}

$pieza = get_post($pieza_id);

if (! $pieza || 'pieza_museo' !== $pieza->post_type || 'publish' !== $pieza->post_status) {
  return;
}

$image_url = get_the_post_thumbnail_url($pieza_id, 'large');

if (! $image_url) {
  $image_url = get_template_directory_uri() . '/assets/img/pieza-museo-default.jpg';
}

$title = get_the_title($pieza_id);

$excerpt = get_the_excerpt($pieza_id);

if (empty($excerpt)) {
  $excerpt = wp_trim_words(wp_strip_all_tags($pieza->post_content), 34, '...');
}

$tipo        = get_post_meta($pieza_id, '_futuretheme_pieza_tipo', true);
$periodo_cul = get_post_meta($pieza_id, '_futuretheme_pieza_periodo', true);
$procedencia = get_post_meta($pieza_id, '_futuretheme_pieza_procedencia', true);
$coleccion   = get_post_meta($pieza_id, '_futuretheme_pieza_coleccion', true);
$codigo      = get_post_meta($pieza_id, '_futuretheme_pieza_codigo', true);
$infografia_id = get_post_meta($pieza_id, '_futuretheme_pieza_infografia_id', true);
$infografia    = '';
if (! empty($infografia_id)) {
  $infografia = wp_get_attachment_image_url(absint($infografia_id), 'full');
}

$meta_parts = array();

if (! empty($tipo)) {
  $meta_parts[] = $tipo;
}

if (! empty($periodo_cul)) {
  $meta_parts[] = $periodo_cul;
}

if (! empty($coleccion)) {
  $meta_parts[] = $coleccion;
}

$meta_label = implode(' · ', $meta_parts);

if ('destacada' === $mode) :
?>

  <section id="pieza-museo-destacada-<?php echo esc_attr($pieza_id); ?>" class="museo-pieza fade-in visible">

    <img
      src="<?php echo esc_url($image_url); ?>"
      alt="<?php echo esc_attr($title); ?>">

    <div class="museo-pieza-text">

      <div class="tag">
        <?php
        if (! empty($periodo)) {
          printf(
            /* translators: %s: periodo, e.g. Mayo 2026. */
            esc_html__('Destaque del mes — %s', 'futuretheme'),
            esc_html($periodo)
          );
        } else {
          esc_html_e('Destaque del mes', 'futuretheme');
        }
        ?>
      </div>

      <h2>
        <?php echo esc_html($title); ?>
      </h2>

      <?php if (! empty($excerpt)) : ?>
        <p>
          <?php echo esc_html($excerpt); ?>
        </p>
      <?php endif; ?>

      <?php if (! empty($tipo) || ! empty($periodo_cul) || ! empty($procedencia) || ! empty($coleccion) || ! empty($codigo)) : ?>

        <div class="museo-pieza-data">

          <?php if (! empty($tipo)) : ?>
            <div class="museo-pieza-data-row">
              <span><?php esc_html_e('Tipo', 'futuretheme'); ?></span>
              <strong><?php echo esc_html($tipo); ?></strong>
            </div>
          <?php endif; ?>

          <?php if (! empty($periodo_cul)) : ?>
            <div class="museo-pieza-data-row">
              <span><?php esc_html_e('Periodo', 'futuretheme'); ?></span>
              <strong><?php echo esc_html($periodo_cul); ?></strong>
            </div>
          <?php endif; ?>

          <?php if (! empty($procedencia)) : ?>
            <div class="museo-pieza-data-row">
              <span><?php esc_html_e('Procedencia', 'futuretheme'); ?></span>
              <strong><?php echo esc_html($procedencia); ?></strong>
            </div>
          <?php endif; ?>

          <?php if (! empty($coleccion)) : ?>
            <div class="museo-pieza-data-row">
              <span><?php esc_html_e('Colección', 'futuretheme'); ?></span>
              <strong><?php echo esc_html($coleccion); ?></strong>
            </div>
          <?php endif; ?>

          <?php if (! empty($codigo)) : ?>
            <div class="museo-pieza-data-row">
              <span><?php esc_html_e('Código', 'futuretheme'); ?></span>
              <strong><?php echo esc_html($codigo); ?></strong>
            </div>
          <?php endif; ?>

        </div>

      <?php endif; ?>

    </div>

  </section>

  <?php
  $pieza_content_raw = get_post_field('post_content', $pieza_id);
  $pieza_has_content = ! empty(trim(wp_strip_all_tags($pieza_content_raw))) || has_blocks($pieza_content_raw);
  ?>

  <?php if ($pieza_has_content) : ?>

    <section class="museo-pieza-desarrollo wrap">

      <div class="museo-section-label">
        <?php esc_html_e('Información de la pieza', 'futuretheme'); ?>
      </div>

      <div class="museo-pieza-desarrollo-content">
        <?php echo apply_filters('the_content', $pieza_content_raw); ?>
      </div>

    </section>

  <?php endif; ?>

  <?php if (! empty($infografia)) : ?>

    <section class="infografia-section wrap">

      <h3>
        <?php esc_html_e('Sobre nuestras exposiciones', 'futuretheme'); ?>
      </h3>

      <div class="infografia-img scale-in infog-animate visible">
        <img
          src="<?php echo esc_url($infografia); ?>"
          alt="<?php echo esc_attr(sprintf(__('Infografía %s', 'futuretheme'), $title)); ?>"
          loading="lazy">
      </div>

    </section>

  <?php endif; ?>

<?php
else :
?>

  <article id="pieza-museo-<?php echo esc_attr($pieza_id); ?>" class="museo-card fade-in visible">

    <a class="museo-card-link" href="<?php echo esc_url(get_permalink($pieza_id)); ?>">

      <div class="museo-card-img">
        <img
          src="<?php echo esc_url($image_url); ?>"
          alt="<?php echo esc_attr($title); ?>"
          loading="lazy">
      </div>

      <div class="museo-card-body">

        <h3>
          <?php echo esc_html($title); ?>
        </h3>

        <?php if (! empty($meta_label)) : ?>
          <div class="museo-card-meta">
            <?php echo esc_html(mb_strtoupper($meta_label, 'UTF-8')); ?>
          </div>
        <?php endif; ?>

        <?php if (! empty($excerpt)) : ?>
          <p>
            <?php echo esc_html(wp_trim_words($excerpt, 22, '...')); ?>
          </p>
        <?php endif; ?>

      </div>

    </a>

  </article>

<?php
endif;
