<?php

/**
 * Card individual de Exposición.
 *
 * Estructura basada en theme-model.html:
 *
 * <div class="av2-card">
 *   <div class="av2-img" style="background-image: url(...)"></div>
 *   <div class="av2-body">
 *     <h3>Título</h3>
 *     <div class="av2-meta">ARTISTA · SALA</div>
 *     <div class="av2-tags">
 *       <span class="av2-tag">Técnica</span>
 *       <span class="av2-tag av2-free">Ingreso libre</span>
 *     </div>
 *   </div>
 * </div>
 *
 * @package FutureTheme
 */

$exposicion_id = get_the_ID();

$image_url = get_the_post_thumbnail_url($exposicion_id, 'large');

if (! $image_url) {
  $image_url = get_template_directory_uri() . '/assets/img/exposicion-default.jpg';
}

$artistas = get_the_terms($exposicion_id, 'artista_exposicion');
$salas    = get_the_terms($exposicion_id, 'sala_exposicion');
$tipos    = get_the_terms($exposicion_id, 'tipo_exposicion');

$ingreso = get_post_meta($exposicion_id, '_futuretheme_expo_ingreso', true);

$artista_names = array();
$sala_names    = array();
$tipo_names    = array();

if (! empty($artistas) && ! is_wp_error($artistas)) {
  $artista_names = wp_list_pluck($artistas, 'name');
}

if (! empty($salas) && ! is_wp_error($salas)) {
  $sala_names = wp_list_pluck($salas, 'name');
}

if (! empty($tipos) && ! is_wp_error($tipos)) {
  $tipo_names = wp_list_pluck($tipos, 'name');
}

/*
 * Metadata principal:
 * FELIPE COAQUIRA · SALA I
 */
$meta_parts = array();

if (1 === count($artista_names)) {
  $meta_parts[] = $artista_names[0];
} elseif (2 === count($artista_names)) {
  $meta_parts[] = implode(', ', $artista_names);
} elseif (count($artista_names) >= 3) {
  $meta_parts[] = __('Exposición colectiva', 'futuretheme');
}

if (! empty($sala_names)) {
  $meta_parts[] = implode(', ', $sala_names);
}

$card_meta = implode(' · ', $meta_parts);

/*
 * Tags:
 * Instalación
 * Ingreso libre
 */
$tags = array();

if (! empty($tipo_names)) {
  foreach ($tipo_names as $tipo_name) {
    $tags[] = array(
      'label' => $tipo_name,
      'free'  => false,
    );
  }
}

if (! empty($ingreso)) {
  $tags[] = array(
    'label' => $ingreso,
    'free'  => true,
  );
}
?>

<a
  id="exposicion-<?php the_ID(); ?>"
  <?php post_class('av2-card exposicion-card'); ?>
  href="<?php the_permalink(); ?>"
  aria-label="<?php echo esc_attr(get_the_title()); ?>">

  <div
    class="av2-img"
    style="background-image: url('<?php echo esc_url($image_url); ?>');"
    role="img"
    aria-label="<?php echo esc_attr(get_the_title()); ?>"></div>

  <div class="av2-body">

    <h3><?php the_title(); ?></h3>

    <?php if (! empty($card_meta)) : ?>
      <div class="av2-meta">
        <?php echo esc_html(mb_strtoupper($card_meta, 'UTF-8')); ?>
      </div>
    <?php endif; ?>

    <?php if (! empty($tags)) : ?>
      <div class="av2-tags">
        <?php foreach ($tags as $tag) : ?>
          <span class="av2-tag<?php echo ! empty($tag['free']) ? ' av2-free' : ''; ?>">
            <?php echo esc_html($tag['label']); ?>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

</a>
