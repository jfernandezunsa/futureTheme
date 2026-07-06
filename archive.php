<?php

/**
 * Plantilla general para archivos.
 *
 * Se usa para:
 * - Archivos de entradas
 * - Categorías
 * - Etiquetas
 * - Custom post types sin archive específico
 *
 * @package FutureTheme
 */

get_header();

if (is_category() || is_tag() || is_tax()) {
  $archive_title = single_term_title('', false);
} elseif (is_post_type_archive()) {
  $archive_title = post_type_archive_title('', false);
} elseif (is_author()) {
  $archive_title = get_the_author();
} elseif (is_date()) {
  $archive_title = get_the_date('F Y');
} else {
  $archive_title = get_the_archive_title();
}

$archive_description = get_the_archive_description();

$post_type = get_post_type();

$post_type_obj = $post_type ? get_post_type_object($post_type) : null;

$archive_label = __('Archivo', 'futuretheme');

if (is_category()) {
  $archive_label = __('Categoría', 'futuretheme');
} elseif (is_tag()) {
  $archive_label = __('Etiqueta', 'futuretheme');
} elseif (is_author()) {
  $archive_label = __('Autor', 'futuretheme');
} elseif (is_date()) {
  $archive_label = __('Fecha', 'futuretheme');
} elseif (is_post_type_archive() && $post_type_obj) {
  $archive_label = $post_type_obj->labels->name;
}

/*
 * Imagen por defecto para hero de archivo.
 * Puedes crear esta imagen:
 * assets/img/archive-default.jpg
 */
$hero_image = get_template_directory_uri() . '/assets/img/archive-default.jpg';

/*
 * Si es archivo de categoría o etiqueta, intentamos usar una imagen destacada
 * de la primera publicación encontrada.
 */
if (have_posts()) {
  the_post();

  $first_post_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

  if ($first_post_image) {
    $hero_image = $first_post_image;
  }

  rewind_posts();
}
?>

<main id="primary" class="site-main">

  <div id="archive-general" class="archive-general">

    <section class="pg-hero archive-general-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php echo esc_attr(wp_strip_all_tags($archive_title)); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php echo esc_html($archive_label); ?>
        </div>

        <h1>
          <?php echo wp_kses_post($archive_title); ?>
        </h1>

        <?php if (! empty($archive_description)) : ?>
          <div class="sub">
            <?php echo wp_kses_post(wp_strip_all_tags($archive_description)); ?>
          </div>
        <?php endif; ?>

      </div>

    </section>

    <section class="archive-general-content-section wrap">

      <?php if (have_posts()) : ?>

        <div class="museo-card-grid archive-card-grid">

          <?php
          while (have_posts()) :
            the_post();

            $item_id = get_the_ID();

            $item_image = get_the_post_thumbnail_url($item_id, 'large');

            if (! $item_image) {
              $item_image = get_template_directory_uri() . '/assets/img/page-default.jpg';
            }

            $item_excerpt = has_excerpt($item_id) ? get_the_excerpt($item_id) : '';

            $item_post_type     = get_post_type($item_id);
            $item_post_type_obj = $item_post_type ? get_post_type_object($item_post_type) : null;

            $item_type_label = $item_post_type_obj && ! empty($item_post_type_obj->labels->singular_name)
              ? $item_post_type_obj->labels->singular_name
              : __('Publicación', 'futuretheme');

            $item_date = get_the_date('d/m/Y', $item_id);

            $meta_parts = array();

            if (! empty($item_type_label)) {
              $meta_parts[] = $item_type_label;
            }

            if (! empty($item_date)) {
              $meta_parts[] = $item_date;
            }

            $meta_label = implode(' · ', $meta_parts);
          ?>

            <article id="post-<?php echo esc_attr($item_id); ?>" <?php post_class('museo-card archive-card'); ?>>

              <a class="museo-card-link" href="<?php the_permalink(); ?>">

                <div class="museo-card-img">
                  <img
                    src="<?php echo esc_url($item_image); ?>"
                    alt="<?php echo esc_attr(get_the_title()); ?>"
                    loading="lazy">
                </div>

                <div class="museo-card-body">

                  <?php if (! empty($meta_label)) : ?>
                    <div class="museo-card-meta">
                      <?php echo esc_html($meta_label); ?>
                    </div>
                  <?php endif; ?>

                  <h3>
                    <?php the_title(); ?>
                  </h3>

                  <?php if (! empty($item_excerpt)) : ?>
                    <p>
                      <?php echo esc_html(wp_trim_words($item_excerpt, 22, '...')); ?>
                    </p>
                  <?php endif; ?>

                </div>

              </a>

            </article>

          <?php endwhile; ?>

        </div>

        <?php
        $pagination = paginate_links(
          array(
            'prev_text' => __('Anterior', 'futuretheme'),
            'next_text' => __('Siguiente', 'futuretheme'),
            'type'      => 'list',
          )
        );
        ?>

        <?php if (! empty($pagination)) : ?>
          <nav class="archive-pagination" aria-label="<?php esc_attr_e('Paginación de archivo', 'futuretheme'); ?>">
            <?php echo wp_kses_post($pagination); ?>
          </nav>
        <?php endif; ?>

      <?php else : ?>

        <div class="archive-general-empty">

          <h2>
            <?php esc_html_e('No hay publicaciones disponibles', 'futuretheme'); ?>
          </h2>

          <p>
            <?php esc_html_e('No se encontraron elementos publicados en este archivo.', 'futuretheme'); ?>
          </p>

        </div>

      <?php endif; ?>

    </section>

  </div>

</main>

<?php
get_footer();
