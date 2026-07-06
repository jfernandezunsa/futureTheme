<?php

/**
 * Slider principal de portada.
 *
 * Muestra elementos del post type "destacado"
 * pertenecientes a la categoría "hero".
 *
 * @package FutureTheme
 */

$slider_query = new WP_Query(
  array(
    'post_type'      => 'destacado',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'orderby'        => array(
      'menu_order' => 'ASC',
      'date'       => 'DESC',
    ),
    'tax_query'      => array(
      array(
        'taxonomy' => 'destacado_categoria',
        'field'    => 'slug',
        'terms'    => array('hero'),
      ),
    ),
  )
);

if (! $slider_query->have_posts()) {
  return;
}

$slides = array();

while ($slider_query->have_posts()) {
  $slider_query->the_post();

  $post_id     = get_the_ID();
  $image_url   = get_the_post_thumbnail_url($post_id, 'full');
  $tag         = get_post_meta($post_id, '_futuretheme_destacado_tag', true);
  $button_text = get_post_meta($post_id, '_futuretheme_destacado_button_text', true);
  $button_url  = get_post_meta($post_id, '_futuretheme_destacado_button_url', true);
  $new_tab     = get_post_meta($post_id, '_futuretheme_destacado_new_tab', true);

  if (! $image_url) {
    $image_url = get_template_directory_uri() . '/assets/img/hero-default.jpg';
  }

  $slides[] = array(
    'id'          => $post_id,
    'title'       => get_the_title(),
    'excerpt'     => get_the_excerpt(),
    'image'       => $image_url,
    'tag'         => $tag ? $tag : 'Centro Cultural UNSA — Arequipa',
    'button_text' => $button_text ? $button_text : 'Más información',
    'button_url'  => $button_url ? $button_url : home_url('/'),
    'new_tab'     => '1' === $new_tab ? true : false,
    'image_alt'   => get_the_title(),
  );
}

wp_reset_postdata();

if (empty($slides)) {
  return;
}
?>

<section class="hero futuretheme-slider" aria-label="<?php esc_attr_e('Contenido destacado', 'futuretheme'); ?>">

  <div class="hero-track" id="futurethemeSliderTrack">

    <?php foreach ($slides as $index => $slide) : ?>

      <article class="hero-slide <?php echo 0 === $index ? 'active' : ''; ?>">
        <img
          src="<?php echo esc_url($slide['image']); ?>"
          alt="<?php echo esc_attr($slide['image_alt']); ?>">
      </article>

    <?php endforeach; ?>

  </div>

  <div class="hero-content">

    <div class="hero-tag" id="futurethemeSliderTag">
      <?php echo esc_html($slides[0]['tag']); ?>
    </div>

    <h1 id="futurethemeSliderTitle">
      <?php echo esc_html($slides[0]['title']); ?>
    </h1>

    <p id="futurethemeSliderSub">
      <?php echo esc_html($slides[0]['excerpt']); ?>
    </p>

    <a
      id="futurethemeSliderButton"
      class="btn btn-white"
      href="<?php echo esc_url($slides[0]['button_url']); ?>"
      <?php echo ! empty($slides[0]['new_tab']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
      <?php echo esc_html($slides[0]['button_text']); ?>
    </a>

  </div>

  <?php if (count($slides) > 1) : ?>

    <button
      class="hero-arrow prev"
      type="button"
      data-slider-prev
      aria-label="<?php esc_attr_e('Slide anterior', 'futuretheme'); ?>">
      &#8249;
    </button>

    <button
      class="hero-arrow next"
      type="button"
      data-slider-next
      aria-label="<?php esc_attr_e('Siguiente slide', 'futuretheme'); ?>">
      &#8250;
    </button>

    <div
      class="hero-nav"
      id="futurethemeSliderDots"
      aria-label="<?php esc_attr_e('Navegación del slider', 'futuretheme'); ?>"></div>

  <?php endif; ?>

  <script type="application/json" id="futurethemeSliderData">
    <?php echo wp_json_encode($slides); ?>
  </script>

</section>
