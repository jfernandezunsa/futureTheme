<?php

/**
 * Plantilla general para páginas.
 *
 * Esta plantilla se usa como fallback para páginas que no tienen
 * un archivo específico como page-museo.php, page-cineclub.php,
 * page-artes-visuales.php, etc.
 *
 * @package FutureTheme
 */

get_header();

if (have_posts()) :
  while (have_posts()) :
    the_post();

    $page_id = get_the_ID();

    $hero_image = get_the_post_thumbnail_url($page_id, 'full');

    if (! $hero_image) {
      $hero_image = get_template_directory_uri() . '/assets/img/page-default.jpg';
    }

    $excerpt = has_excerpt($page_id) ? get_the_excerpt($page_id) : '';
?>

    <main id="primary" class="site-main">

      <div id="page-<?php echo esc_attr($post->post_name); ?>" <?php post_class('page page-general'); ?>>

        <section class="pg-hero page-general-hero">

          <img
            src="<?php echo esc_url($hero_image); ?>"
            alt="<?php echo esc_attr(get_the_title()); ?>">

          <div class="pg-hero-content">

            <div class="overline">
              <?php esc_html_e('Centro Cultural UNSA', 'futuretheme'); ?>
            </div>

            <h1>
              <?php the_title(); ?>
            </h1>

            <?php if (! empty($excerpt)) : ?>
              <div class="sub">
                <?php echo esc_html($excerpt); ?>
              </div>
            <?php endif; ?>

          </div>

        </section>

        <section class="page-general-content-section wrap">

          <article class="page-general-content">

            <?php
            $content_raw = get_post_field('post_content', $page_id);

            if (! empty(trim(wp_strip_all_tags($content_raw))) || has_blocks($content_raw)) :
            ?>

              <div class="entry-content">
                <?php the_content(); ?>
              </div>

              <?php
              wp_link_pages(
                array(
                  'before' => '<div class="page-links">' . esc_html__('Páginas:', 'futuretheme'),
                  'after'  => '</div>',
                )
              );
              ?>

            <?php else : ?>

              <div class="page-general-empty">

                <h2>
                  <?php esc_html_e('Contenido en preparación', 'futuretheme'); ?>
                </h2>

                <p>
                  <?php esc_html_e('Esta página aún no tiene contenido publicado.', 'futuretheme'); ?>
                </p>

              </div>

            <?php endif; ?>

          </article>

        </section>

      </div>

    </main>

<?php
  endwhile;
endif;

get_footer();
