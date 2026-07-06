<?php

/**
 * Vista individual de Exposición.
 *
 * Basada en la estructura visual de "Hilando Historias".
 *
 * @package FutureTheme
 */

get_header();

if (have_posts()) :
  while (have_posts()) :
    the_post();

    $post_id = get_the_ID();

    /*
     * Imagen principal.
     * Si no hay imagen destacada, usa una imagen por defecto.
     */
    $hero_image = get_the_post_thumbnail_url($post_id, 'full');

    if (! $hero_image) {
      $hero_image = get_template_directory_uri() . '/assets/img/exposicion-default.jpg';
    }

    /*
     * Taxonomías.
     */
    $categorias = get_the_terms($post_id, 'categoria_exposicion');
    $artistas   = get_the_terms($post_id, 'artista_exposicion');
    $salas      = get_the_terms($post_id, 'sala_exposicion');
    $tipos      = get_the_terms($post_id, 'tipo_exposicion');

    $categoria_names = array();
    $artista_names   = array();
    $sala_names      = array();
    $tipo_names      = array();

    if (! empty($categorias) && ! is_wp_error($categorias)) {
      $categoria_names = wp_list_pluck($categorias, 'name');
    }

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
     * Campos personalizados.
     */
    $mes            = get_post_meta($post_id, '_futuretheme_expo_mes', true);
    $anio           = get_post_meta($post_id, '_futuretheme_expo_anio', true);
    $fecha_inicio   = get_post_meta($post_id, '_futuretheme_expo_fecha_inicio', true);
    $fecha_fin      = get_post_meta($post_id, '_futuretheme_expo_fecha_fin', true);
    $ingreso        = get_post_meta($post_id, '_futuretheme_expo_ingreso', true);
    $catalogo_url   = get_post_meta($post_id, '_futuretheme_expo_catalogo_url', true);
    $entrevista_url = get_post_meta($post_id, '_futuretheme_expo_entrevista_url', true);

    /*
     * Periodo.
     */
    $mes_label = '';

    if (! empty($mes)) {
      $mes_label = ucfirst($mes);
    }

    $periodo_label = trim($mes_label . ' ' . $anio);

    /*
     * Overline del hero.
     *
     * Formato esperado:
     * Galerías de Arte · Instalación · Sala I · Mayo 2026
     */
    $overline_parts = array();

    if (! empty($categoria_names)) {
      $overline_parts[] = implode(', ', $categoria_names);
    }

    if (! empty($tipo_names)) {
      $overline_parts[] = implode(', ', $tipo_names);
    }

    if (! empty($sala_names)) {
      $overline_parts[] = implode(', ', $sala_names);
    }

    if (! empty($periodo_label)) {
      $overline_parts[] = $periodo_label;
    }

    $overline = implode(' · ', $overline_parts);

    /*
     * Subtítulo del hero.
     *
     * Formato esperado:
     * Felipe Coaquira
     */
    $subtitulo = '';

    if (! empty($artista_names)) {
      $subtitulo = implode(', ', $artista_names);
    }

    /*
     * Video YouTube para modal.
     * No debe romper si la función no existe o si el campo está vacío.
     */
    $youtube_embed = '';

    if (! empty($entrevista_url) && function_exists('futuretheme_get_youtube_embed_url')) {
      $youtube_embed = futuretheme_get_youtube_embed_url($entrevista_url);
    }

    /*
     * Verificar si el contenido del editor tiene algo.
     */
    $content_raw = get_the_content();
    $has_content = ! empty(trim(wp_strip_all_tags($content_raw))) || has_blocks($content_raw);

    /*
     * Buscar otras exposiciones que se estén dando en esas fechas.
     *
     * Prioridad:
     * 1. Si hay fecha inicio y fecha fin, busca exposiciones con fechas cruzadas.
     * 2. Si no hay fechas completas, busca por mes y año.
     * 3. Si no hay fechas ni mes/año, no muestra sección de relacionadas.
     */
    $otras_exposiciones = null;
    $buscar_relacionadas = false;

    $otras_exposiciones_args = array(
      'post_type'      => 'exposicion',
      'posts_per_page' => 3,
      'post_status'    => 'publish',
      'post__not_in'   => array($post_id),
      'orderby'        => array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
      ),
    );

    if (! empty($fecha_inicio) && ! empty($fecha_fin)) {

      $buscar_relacionadas = true;

      $otras_exposiciones_args['meta_query'] = array(
        'relation' => 'AND',
        array(
          'key'     => '_futuretheme_expo_fecha_inicio',
          'value'   => $fecha_fin,
          'compare' => '<=',
          'type'    => 'DATE',
        ),
        array(
          'key'     => '_futuretheme_expo_fecha_fin',
          'value'   => $fecha_inicio,
          'compare' => '>=',
          'type'    => 'DATE',
        ),
      );
    } elseif (! empty($mes) && ! empty($anio)) {

      $buscar_relacionadas = true;

      $otras_exposiciones_args['meta_query'] = array(
        'relation' => 'AND',
        array(
          'key'     => '_futuretheme_expo_mes',
          'value'   => $mes,
          'compare' => '=',
        ),
        array(
          'key'     => '_futuretheme_expo_anio',
          'value'   => $anio,
          'compare' => '=',
        ),
      );
    }

    if ($buscar_relacionadas) {
      $otras_exposiciones = new WP_Query($otras_exposiciones_args);
    }
?>

    <main id="primary" class="site-main single-exposicion">

      <section class="pg-hero exposicion-hero">

        <img
          src="<?php echo esc_url($hero_image); ?>"
          alt="<?php echo esc_attr(get_the_title()); ?>">

        <div class="pg-hero-content">

          <?php if (! empty($overline)) : ?>
            <div class="overline">
              <?php echo esc_html($overline); ?>
            </div>
          <?php endif; ?>

          <h1><?php the_title(); ?></h1>

          <?php if (! empty($subtitulo)) : ?>
            <div class="sub">
              <?php echo esc_html($subtitulo); ?>
            </div>
          <?php endif; ?>

        </div>

      </section>

      <section class="hilando-wrap exposicion-wrap">

        <div class="hilando-intro exposicion-intro fade-in">

          <div class="exposicion-main-text">

            <?php if ($has_content) : ?>

              <?php
              /*
               * Aquí se imprime el contenido del editor.
               *
               * Puede contener:
               * - texto curatorial
               * - bloque Galería
               * - imágenes
               * - bloques de párrafo
               * - bloques personalizados
               */
              the_content();
              ?>

            <?php else : ?>

              <div class="exposicion-empty-content">
                <p>
                  <?php esc_html_e('La información detallada de esta exposición será publicada próximamente.', 'futuretheme'); ?>
                </p>
              </div>

            <?php endif; ?>

          </div>

          <?php
          /*
           * El aside solo aparece si hay al menos:
           * - catálogo
           * - entrevista válida
           * - artistas
           * - salas
           * - periodo
           * - fechas
           * - ingreso
           */
          $show_aside = (
            ! empty($catalogo_url) ||
            ! empty($youtube_embed) ||
            ! empty($artista_names) ||
            ! empty($sala_names) ||
            ! empty($periodo_label) ||
            ! empty($fecha_inicio) ||
            ! empty($fecha_fin) ||
            ! empty($ingreso)
          );
          ?>

          <?php if ($show_aside) : ?>

            <aside class="hilando-btns exposicion-actions">

              <?php if (! empty($catalogo_url)) : ?>
                <a
                  class="btn btn-dark"
                  href="<?php echo esc_url($catalogo_url); ?>"
                  target="_blank"
                  rel="noopener noreferrer">
                  <?php esc_html_e('Ver catálogo virtual', 'futuretheme'); ?>
                </a>
              <?php endif; ?>

              <?php if (! empty($youtube_embed)) : ?>
                <button
                  type="button"
                  class="btn btn-outline exposicion-video-open"
                  data-video-src="<?php echo esc_url($youtube_embed); ?>"
                  aria-haspopup="dialog"
                  aria-controls="exposicionVideoModal">
                  <?php esc_html_e('▶ Ver entrevista', 'futuretheme'); ?>
                </button>
              <?php endif; ?>

              <?php
              $show_data_card = (
                ! empty($artista_names) ||
                ! empty($sala_names) ||
                ! empty($periodo_label) ||
                ! empty($fecha_inicio) ||
                ! empty($fecha_fin) ||
                ! empty($ingreso)
              );
              ?>

              <?php if ($show_data_card) : ?>

                <div class="exposicion-data-card">

                  <?php if (! empty($artista_names)) : ?>
                    <div class="exposicion-data-row">
                      <span><?php esc_html_e('Artistas', 'futuretheme'); ?></span>
                      <strong><?php echo esc_html(implode(', ', $artista_names)); ?></strong>
                    </div>
                  <?php endif; ?>

                  <?php if (! empty($sala_names)) : ?>
                    <div class="exposicion-data-row">
                      <span><?php esc_html_e('Sala', 'futuretheme'); ?></span>
                      <strong><?php echo esc_html(implode(', ', $sala_names)); ?></strong>
                    </div>
                  <?php endif; ?>

                  <?php if (! empty($periodo_label)) : ?>
                    <div class="exposicion-data-row">
                      <span><?php esc_html_e('Periodo', 'futuretheme'); ?></span>
                      <strong><?php echo esc_html($periodo_label); ?></strong>
                    </div>
                  <?php endif; ?>

                  <?php if (! empty($fecha_inicio) || ! empty($fecha_fin)) : ?>
                    <div class="exposicion-data-row">
                      <span><?php esc_html_e('Fechas', 'futuretheme'); ?></span>
                      <strong>
                        <?php
                        if (! empty($fecha_inicio) && ! empty($fecha_fin)) {
                          echo esc_html(
                            date_i18n('d/m/Y', strtotime($fecha_inicio)) .
                              ' - ' .
                              date_i18n('d/m/Y', strtotime($fecha_fin))
                          );
                        } elseif (! empty($fecha_inicio)) {
                          echo esc_html(date_i18n('d/m/Y', strtotime($fecha_inicio)));
                        } elseif (! empty($fecha_fin)) {
                          echo esc_html(date_i18n('d/m/Y', strtotime($fecha_fin)));
                        }
                        ?>
                      </strong>
                    </div>
                  <?php endif; ?>

                  <?php if (! empty($ingreso)) : ?>
                    <div class="exposicion-data-row">
                      <span><?php esc_html_e('Ingreso', 'futuretheme'); ?></span>
                      <strong><?php echo esc_html($ingreso); ?></strong>
                    </div>
                  <?php endif; ?>

                </div>

              <?php endif; ?>

            </aside>

          <?php endif; ?>

        </div>

      </section>

      <?php if ($otras_exposiciones instanceof WP_Query && $otras_exposiciones->have_posts()) : ?>

        <section class="exposicion-relacionadas">

          <div class="exposicion-relacionadas-inner">

            <div class="exposicion-section-label">
              <?php esc_html_e('Exposiciones en estas fechas', 'futuretheme'); ?>
            </div>

            <h2>
              <?php esc_html_e('También puedes visitar', 'futuretheme'); ?>
            </h2>

            <div class="exposicion-related-grid">

              <?php
              while ($otras_exposiciones->have_posts()) :
                $otras_exposiciones->the_post();

                $related_id    = get_the_ID();
                $related_img   = get_the_post_thumbnail_url($related_id, 'large');
                $related_mes   = get_post_meta($related_id, '_futuretheme_expo_mes', true);
                $related_anio  = get_post_meta($related_id, '_futuretheme_expo_anio', true);
                $related_salas = get_the_terms($related_id, 'sala_exposicion');
                $related_tipos = get_the_terms($related_id, 'tipo_exposicion');

                if (! $related_img) {
                  $related_img = get_template_directory_uri() . '/assets/img/exposicion-default.jpg';
                }

                $related_meta = array();

                if (! empty($related_tipos) && ! is_wp_error($related_tipos)) {
                  $related_meta[] = implode(', ', wp_list_pluck($related_tipos, 'name'));
                }

                if (! empty($related_salas) && ! is_wp_error($related_salas)) {
                  $related_meta[] = implode(', ', wp_list_pluck($related_salas, 'name'));
                }

                $related_periodo = trim(ucfirst($related_mes) . ' ' . $related_anio);

                if (! empty($related_periodo)) {
                  $related_meta[] = $related_periodo;
                }
              ?>

                <article class="entrevista-card exposicion-related-card fade-in">

                  <a href="<?php the_permalink(); ?>" class="exposicion-related-link">

                    <div class="entrevista-thumb">
                      <img
                        src="<?php echo esc_url($related_img); ?>"
                        alt="<?php echo esc_attr(get_the_title()); ?>">
                    </div>

                    <div class="entrevista-body">

                      <div class="entrevista-tag">
                        <?php esc_html_e('Exposición', 'futuretheme'); ?>
                      </div>

                      <h3><?php the_title(); ?></h3>

                      <?php if (! empty($related_meta)) : ?>
                        <p><?php echo esc_html(implode(' · ', $related_meta)); ?></p>
                      <?php endif; ?>

                    </div>

                  </a>

                </article>

              <?php endwhile; ?>

            </div>

          </div>

        </section>

        <?php wp_reset_postdata(); ?>

      <?php endif; ?>

      <?php if (! empty($youtube_embed)) : ?>

        <div
          id="exposicionVideoModal"
          class="exposicion-video-modal"
          role="dialog"
          aria-modal="true"
          aria-label="<?php esc_attr_e('Entrevista de la exposición', 'futuretheme'); ?>"
          hidden>

          <div class="exposicion-video-backdrop" data-video-close></div>

          <div class="exposicion-video-box">

            <button
              type="button"
              class="exposicion-video-close"
              data-video-close
              aria-label="<?php esc_attr_e('Cerrar video', 'futuretheme'); ?>">
              ×
            </button>

            <div class="exposicion-video-frame">
              <iframe
                id="exposicionVideoIframe"
                src=""
                title="<?php esc_attr_e('Entrevista de la exposición', 'futuretheme'); ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen></iframe>
            </div>

          </div>

        </div>

      <?php endif; ?>

    </main>

    <?php if (! empty($youtube_embed)) : ?>

      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const openButton = document.querySelector('.exposicion-video-open');
          const modal = document.getElementById('exposicionVideoModal');
          const iframe = document.getElementById('exposicionVideoIframe');
          const closeButtons = document.querySelectorAll('[data-video-close]');

          if (!openButton || !modal || !iframe) {
            return;
          }

          function openModal() {
            const videoSrc = openButton.getAttribute('data-video-src');

            if (!videoSrc) {
              return;
            }

            iframe.setAttribute('src', videoSrc + '?autoplay=1&rel=0');
            modal.removeAttribute('hidden');
            document.body.classList.add('exposicion-video-is-open');
          }

          function closeModal() {
            iframe.setAttribute('src', '');
            modal.setAttribute('hidden', 'hidden');
            document.body.classList.remove('exposicion-video-is-open');
          }

          openButton.addEventListener('click', openModal);

          closeButtons.forEach(function(button) {
            button.addEventListener('click', closeModal);
          });

          document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
              closeModal();
            }
          });
        });
      </script>

    <?php endif; ?>

<?php
  endwhile;
endif;

get_footer();
