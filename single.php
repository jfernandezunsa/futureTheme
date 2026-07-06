<?php

/**
 * Plantilla general para entradas individuales.
 *
 * Esta plantilla funciona como fallback para posts normales.
 *
 * No reemplaza plantillas específicas como:
 * - single-exposicion.php
 * - single-pieza_museo.php
 * - single-espacio_cultural.php
 *
 * @package FutureTheme
 */

get_header();

if (have_posts()) :
  while (have_posts()) :
    the_post();

    $post_id = get_the_ID();

    $hero_image = get_the_post_thumbnail_url($post_id, 'full');

    if (! $hero_image) {
      $hero_image = get_template_directory_uri() . '/assets/img/page-default.jpg';
    }

    $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : '';

    $post_type_obj = get_post_type_object(get_post_type());
    $post_type_name = $post_type_obj && ! empty($post_type_obj->labels->singular_name)
      ? $post_type_obj->labels->singular_name
      : __('Publicación', 'futuretheme');

    $published_date = get_the_date('d/m/Y');
?>

    <main id="primary" class="site-main">

      <div id="single-<?php echo esc_attr($post->post_name); ?>" <?php post_class('single-general'); ?>>

        <section class="pg-hero single-general-hero">

          <img
            src="<?php echo esc_url($hero_image); ?>"
            alt="<?php echo esc_attr(get_the_title()); ?>">

          <div class="pg-hero-content">

            <div class="overline">
              <?php echo esc_html($post_type_name); ?>
              <?php if (! empty($published_date)) : ?>
                · <?php echo esc_html($published_date); ?>
              <?php endif; ?>
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

        <section class="single-general-content-section wrap">

          <div class="single-general-layout">

            <article class="single-general-content">

              <?php
              $content_raw = get_post_field('post_content', $post_id);

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

                <div class="single-general-empty">

                  <h2>
                    <?php esc_html_e('Contenido en preparación', 'futuretheme'); ?>
                  </h2>

                  <p>
                    <?php esc_html_e('Esta publicación aún no tiene contenido desarrollado.', 'futuretheme'); ?>
                  </p>

                </div>

              <?php endif; ?>

            </article>

            <!-- <aside class="single-general-sidebar">

              <div class="single-general-card">

                <h2>
                  <?php esc_html_e('Información', 'futuretheme'); ?>
                </h2>

                <div class="single-general-info-row">
                  <span><?php esc_html_e('Tipo', 'futuretheme'); ?></span>
                  <strong><?php echo esc_html($post_type_name); ?></strong>
                </div>

                <div class="single-general-info-row">
                  <span><?php esc_html_e('Publicado', 'futuretheme'); ?></span>
                  <strong><?php echo esc_html($published_date); ?></strong>
                </div>

                <?php if (has_category()) : ?>
                  <div class="single-general-info-row">
                    <span><?php esc_html_e('Categoría', 'futuretheme'); ?></span>
                    <strong><?php the_category(', '); ?></strong>
                  </div>
                <?php endif; ?>

                <?php if (has_tag()) : ?>
                  <div class="single-general-info-row">
                    <span><?php esc_html_e('Etiquetas', 'futuretheme'); ?></span>
                    <strong><?php the_tags('', ', ', ''); ?></strong>
                  </div>
                <?php endif; ?>

              </div>

            </aside> -->

          </div>

        </section>

        <?php
        $related_query = new WP_Query(
          array(
            'post_type'           => get_post_type(),
            'posts_per_page'      => 3,
            'post_status'         => 'publish',
            'post__not_in'        => array($post_id),
            'ignore_sticky_posts' => true,
            'orderby'             => 'date',
            'order'               => 'DESC',
          )
        );
        ?>

        <?php if ($related_query->have_posts()) : ?>

          <section class="single-general-related museo-otras-piezas wrap">

            <div class="museo-otras-header">

              <div class="museo-section-label">
                <?php esc_html_e('También te puede interesar', 'futuretheme'); ?>
              </div>

              <!--               <h2>
                <?php esc_html_e('Publicaciones relacionadas', 'futuretheme'); ?>
              </h2> -->

            </div>

            <div class="museo-card-grid">

              <?php
              while ($related_query->have_posts()) :
                $related_query->the_post();

                $related_id    = get_the_ID();
                $related_image = get_the_post_thumbnail_url($related_id, 'large');

                if (! $related_image) {
                  $related_image = get_template_directory_uri() . '/assets/img/page-default.jpg';
                }

                $related_excerpt = has_excerpt($related_id) ? get_the_excerpt($related_id) : '';
              ?>

                <article class="museo-card single-general-related-card">

                  <a href="<?php the_permalink(); ?>" class="museo-card-link">

                    <div class="museo-card-img">
                      <img
                        src="<?php echo esc_url($related_image); ?>"
                        alt="<?php echo esc_attr(get_the_title()); ?>"
                        loading="lazy">
                    </div>

                    <div class="museo-card-body">

                      <div class="museo-card-meta">
                        <?php echo esc_html(get_the_date('d/m/Y')); ?>
                      </div>

                      <h3>
                        <?php the_title(); ?>
                      </h3>

                      <?php if (! empty($related_excerpt)) : ?>
                        <p>
                          <?php echo esc_html(wp_trim_words($related_excerpt, 18, '...')); ?>
                        </p>
                      <?php endif; ?>

                    </div>

                  </a>

                </article>

              <?php
              endwhile;
              ?>

            </div>

          </section>

          <?php wp_reset_postdata(); ?>

        <?php endif; ?>

      </div>

    </main>

<?php
  endwhile;
endif;

get_footer();
