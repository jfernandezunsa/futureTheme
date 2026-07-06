<?php

/**
 * Vista individual de Pieza del Museo.
 *
 * Se usa para mostrar una pieza cuando no necesariamente está
 * dentro del bloque "Pieza destacada" del mes.
 *
 * @package FutureTheme
 */

get_header();

if (have_posts()) :
  while (have_posts()) :
    the_post();

    $pieza_id = get_the_ID();

    $image_url = get_the_post_thumbnail_url($pieza_id, 'full');

    if (! $image_url) {
      $image_url = get_template_directory_uri() . '/assets/img/pieza-museo-default.jpg';
    }

    $tipo          = get_post_meta($pieza_id, '_futuretheme_pieza_tipo', true);
    $periodo       = get_post_meta($pieza_id, '_futuretheme_pieza_periodo', true);
    $procedencia   = get_post_meta($pieza_id, '_futuretheme_pieza_procedencia', true);
    $coleccion     = get_post_meta($pieza_id, '_futuretheme_pieza_coleccion', true);
    $codigo        = get_post_meta($pieza_id, '_futuretheme_pieza_codigo', true);
    $infografia_id = get_post_meta($pieza_id, '_futuretheme_pieza_infografia_id', true);

    $infografia_url = '';

    if (! empty($infografia_id)) {
      $infografia_url = wp_get_attachment_image_url(absint($infografia_id), 'full');
    }

    $excerpt = get_the_excerpt();

    if (empty($excerpt)) {
      $excerpt = wp_trim_words(wp_strip_all_tags(get_the_content()), 38, '...');
    }

    $meta_parts = array();

    if (! empty($tipo)) {
      $meta_parts[] = $tipo;
    }

    if (! empty($periodo)) {
      $meta_parts[] = $periodo;
    }

    if (! empty($coleccion)) {
      $meta_parts[] = $coleccion;
    }

    /*
     * Otras piezas del museo.
     */
    $otras_piezas_query = new WP_Query(
      array(
        'post_type'      => 'pieza_museo',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'post__not_in'   => array($pieza_id),
        'orderby'        => array(
          'menu_order' => 'ASC',
          'date'       => 'DESC',
        ),
        'meta_query'     => array(
          array(
            'key'     => '_futuretheme_pieza_destacada',
            'value'   => '1',
            'compare' => '=',
          ),
        ),
      )
    );

    /*
     * Fallback si no hay piezas marcadas como destacadas.
     */
    if (! $otras_piezas_query->have_posts()) {
      wp_reset_postdata();

      $otras_piezas_query = new WP_Query(
        array(
          'post_type'      => 'pieza_museo',
          'posts_per_page' => 3,
          'post_status'    => 'publish',
          'post__not_in'   => array($pieza_id),
          'orderby'        => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
          ),
        )
      );
    }
?>

    <main id="primary" class="site-main">

      <div id="single-pieza-museo" class="page single-pieza-museo">

        <section class="pg-hero museo-hero single-pieza-hero">

          <img
            src="<?php echo esc_url($image_url); ?>"
            alt="<?php echo esc_attr(get_the_title()); ?>">

          <div class="pg-hero-content">

            <div class="overline">
              <?php esc_html_e('Museo Arqueológico UNSA', 'futuretheme'); ?>
            </div>

            <h1>
              <?php the_title(); ?>
            </h1>

            <?php if (! empty($meta_parts)) : ?>
              <div class="sub">
                <?php echo esc_html(implode(' · ', $meta_parts)); ?>
              </div>
            <?php endif; ?>

          </div>

        </section>

        <section class="single-pieza-main wrap">

          <div class="single-pieza-layout">

            <article class="single-pieza-content">

              <!--               <div class="museo-section-label">
                <?php esc_html_e('Pieza del museo', 'futuretheme'); ?>
              </div>

              <h2>
                <?php the_title(); ?>
              </h2> -->

              <?php if (! empty($excerpt)) : ?>
                <p class="single-pieza-lead">
                  <?php echo esc_html($excerpt); ?>
                </p>
              <?php endif; ?>

              <?php if (trim(wp_strip_all_tags(get_the_content())) || has_blocks(get_the_content())) : ?>
                <div class="single-pieza-editor">
                  <?php the_content(); ?>
                </div>
              <?php endif; ?>

            </article>

            <?php if (! empty($tipo) || ! empty($periodo) || ! empty($procedencia) || ! empty($coleccion) || ! empty($codigo)) : ?>

              <aside class="single-pieza-data-card">

                <h3>
                  <?php esc_html_e('Datos de la pieza', 'futuretheme'); ?>
                </h3>

                <?php if (! empty($tipo)) : ?>
                  <div class="single-pieza-data-row">
                    <span><?php esc_html_e('Tipo', 'futuretheme'); ?></span>
                    <strong><?php echo esc_html($tipo); ?></strong>
                  </div>
                <?php endif; ?>

                <?php if (! empty($periodo)) : ?>
                  <div class="single-pieza-data-row">
                    <span><?php esc_html_e('Periodo', 'futuretheme'); ?></span>
                    <strong><?php echo esc_html($periodo); ?></strong>
                  </div>
                <?php endif; ?>

                <?php if (! empty($procedencia)) : ?>
                  <div class="single-pieza-data-row">
                    <span><?php esc_html_e('Procedencia', 'futuretheme'); ?></span>
                    <strong><?php echo esc_html($procedencia); ?></strong>
                  </div>
                <?php endif; ?>

                <?php if (! empty($coleccion)) : ?>
                  <div class="single-pieza-data-row">
                    <span><?php esc_html_e('Colección', 'futuretheme'); ?></span>
                    <strong><?php echo esc_html($coleccion); ?></strong>
                  </div>
                <?php endif; ?>

                <?php if (! empty($codigo)) : ?>
                  <div class="single-pieza-data-row">
                    <span><?php esc_html_e('Código', 'futuretheme'); ?></span>
                    <strong><?php echo esc_html($codigo); ?></strong>
                  </div>
                <?php endif; ?>

              </aside>

            <?php endif; ?>

          </div>

        </section>

        <?php if (! empty($infografia_url)) : ?>

          <section class="infografia-section wrap">

            <h3>
              <?php esc_html_e('Infografía de la pieza', 'futuretheme'); ?>
            </h3>

            <div class="infografia-img scale-in infog-animate visible">
              <img
                src="<?php echo esc_url($infografia_url); ?>"
                alt="<?php echo esc_attr(sprintf(__('Infografía %s', 'futuretheme'), get_the_title())); ?>"
                loading="lazy">
            </div>

          </section>

        <?php endif; ?>

        <?php if ($otras_piezas_query->have_posts()) : ?>

          <section class="museo-otras-piezas wrap">

            <div class="museo-otras-header">

              <div class="museo-section-label">
                <?php esc_html_e('Colección del museo', 'futuretheme'); ?>
              </div>

              <h2>
                <?php esc_html_e('Otras piezas destacadas', 'futuretheme'); ?>
              </h2>

            </div>

            <div class="museo-card-grid">

              <?php
              while ($otras_piezas_query->have_posts()) :
                $otras_piezas_query->the_post();

                set_query_var('futuretheme_museo_pieza_id', get_the_ID());
                set_query_var('futuretheme_museo_card_mode', 'card');
                set_query_var('futuretheme_museo_periodo_label', '');

                get_template_part('template-parts/museo/card', 'pieza');

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
