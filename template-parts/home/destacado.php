<?php

/**
 * Tarjetas de actualidad cultural.
 *
 * Muestra elementos del post type "destacado"
 * pertenecientes a la categoría "destacado".
 *
 * @package FutureTheme
 */

$actualidad_query = new WP_Query(
  array(
    'post_type'      => 'destacado',
    'posts_per_page' => 7,
    'post_status'    => 'publish',
    'orderby'        => array(
      'menu_order' => 'ASC',
      'date'       => 'DESC',
    ),
    'tax_query'      => array(
      array(
        'taxonomy' => 'destacado_categoria',
        'field'    => 'slug',
        'terms'    => array('destacado'),
      ),
    ),
  )
);

if (! $actualidad_query->have_posts()) {
  return;
}

$cards = array();

while ($actualidad_query->have_posts()) {
  $actualidad_query->the_post();

  $post_id    = get_the_ID();
  $image_url  = get_the_post_thumbnail_url($post_id, 'large');
  $tag        = get_post_meta($post_id, '_futuretheme_destacado_tag', true);
  $button_url = get_post_meta($post_id, '_futuretheme_destacado_button_url', true);
  $new_tab    = get_post_meta($post_id, '_futuretheme_destacado_new_tab', true);

  if (! $image_url) {
    $image_url = get_template_directory_uri() . '/assets/img/card-default.jpg';
  }

  $cards[] = array(
    'title'     => get_the_title(),
    'tag'       => $tag,
    'url'       => $button_url ? $button_url : '#',
    'new_tab'   => '1' === $new_tab ? true : false,
    'image'     => $image_url,
    'image_alt' => get_the_title(),
  );
}

wp_reset_postdata();

if (empty($cards)) {
  return;
}

$first_row  = array_slice($cards, 0, 3);
$second_row = array_slice($cards, 3, 3);
$center_row = array_slice($cards, 6, 1);
?>

<section class="actualidad futuretheme-actualidad">

  <div class="wrap">

    <div class="sec-title">
      <?php esc_html_e('Actualidad Cultural', 'futuretheme'); ?>
    </div>

    <?php if (! empty($first_row)) : ?>
      <div class="act-grid">
        <?php foreach ($first_row as $card) : ?>
          <?php futuretheme_actualidad_card_markup($card); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (! empty($second_row)) : ?>
      <div class="act-row2">
        <?php foreach ($second_row as $card) : ?>
          <?php futuretheme_actualidad_card_markup($card); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (! empty($center_row)) : ?>
      <div class="act-center">
        <?php foreach ($center_row as $card) : ?>
          <?php futuretheme_actualidad_card_markup($card); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

</section>

<?php
/**
 * Renderiza una tarjeta de actualidad.
 *
 * @param array $card Datos de la tarjeta.
 */
function futuretheme_actualidad_card_markup($card)
{

  $title   = isset($card['title']) ? $card['title'] : '';
  $tag     = isset($card['tag']) ? $card['tag'] : '';
  $url     = isset($card['url']) ? $card['url'] : '#';
  $new_tab = ! empty($card['new_tab']);
  $image   = isset($card['image']) ? $card['image'] : '';
  $alt     = isset($card['image_alt']) ? $card['image_alt'] : $title;

  if (empty($title)) {
    return;
  }
?>

  <a
    class="act-card"
    href="<?php echo esc_url($url); ?>"
    <?php echo $new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>

    <img
      src="<?php echo esc_url($image); ?>"
      alt="<?php echo esc_attr($alt); ?>">

    <div class="act-label">
      <?php if (! empty($tag)) : ?>
        <span class="act-tag"><?php echo esc_html($tag); ?></span>
      <?php endif; ?>

      <h3><?php echo esc_html($title); ?></h3>
    </div>

    <span class="act-icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" focusable="false">
        <path d="M7 17L17 7"></path>
        <path d="M9 7h8v8"></path>
      </svg>
    </span>

  </a>

<?php
}
