<?php

/**
 * Plantilla de resultados de búsqueda.
 *
 * Usa el mismo estilo general del theme:
 * - Hero general
 * - Grid tipo museo-card-grid
 * - Paginación institucional
 *
 * @package FutureTheme
 */

get_header();

$search_query = get_search_query();

$hero_image = get_template_directory_uri() . '/assets/img/search-default.jpg';

/*
 * Si hay resultados, usamos la imagen destacada del primer resultado
 * como imagen del hero.
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

  <div id="search-general" class="search-general">

    <section class="pg-hero search-general-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php esc_attr_e('Resultados de búsqueda', 'futuretheme'); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php esc_html_e('Resultados de búsqueda', 'futuretheme'); ?>
        </div>

        <h1>
          <?php
          if (! empty($search_query)) {
            echo esc_html($search_query);
          } else {
            esc_html_e('Búsqueda', 'futuretheme');
          }
          ?>
        </h1>

        <div class="sub">
          <?php
          printf(
            esc_html__('Resultados encontrados para la búsqueda realizada en el sitio.', 'futuretheme')
          );
          ?>
        </div>

      </div>

    </section>

    <section class="search-general-content-section wrap">

      <div class="search-form-box">

        <form role="search" method="get" class="search-page-form" action="<?php echo esc_url(home_url('/')); ?>">

          <label class="screen-reader-text" for="search-page-field">
            <?php esc_html_e('Buscar en el sitio', 'futuretheme'); ?>
          </label>

          <input
            type="search"
            id="search-page-field"
            class="search-page-input"
            name="s"
            value="<?php echo esc_attr($search_query); ?>"
            placeholder="<?php esc_attr_e('Buscar...', 'futuretheme'); ?>">

          <button type="submit" class="search-page-button">
            <?php esc_html_e('Buscar', 'futuretheme'); ?>
          </button>

        </form>

      </div>

      <?php if (have_posts()) : ?>

        <div class="museo-card-grid search-card-grid">

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

            <article id="post-<?php echo esc_attr($item_id); ?>" <?php post_class('museo-card search-card'); ?>>

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
          <nav class="archive-pagination search-pagination" aria-label="<?php esc_attr_e('Paginación de resultados de búsqueda', 'futuretheme'); ?>">
            <?php echo wp_kses_post($pagination); ?>
          </nav>
        <?php endif; ?>

      <?php else : ?>

        <div class="search-general-empty">

          <h2>
            <?php esc_html_e('No se encontraron resultados', 'futuretheme'); ?>
          </h2>

          <p>
            <?php esc_html_e('Intenta realizar una nueva búsqueda con otros términos.', 'futuretheme'); ?>
          </p>

        </div>

      <?php endif; ?>

    </section>

  </div>

</main>

<?php
get_footer();
