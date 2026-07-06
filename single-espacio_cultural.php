<?php
/**
 * Plantilla individual para Espacios Culturales.
 *
 * @package FutureTheme
 */

get_header();

if ( have_posts() ) :
  while ( have_posts() ) :
    the_post();

    $post_id = get_the_ID();

    $capacidad = get_post_meta( $post_id, '_futuretheme_espacio_capacidad', true );
    $horario   = get_post_meta( $post_id, '_futuretheme_espacio_horario', true );
    $direccion = get_post_meta( $post_id, '_futuretheme_espacio_direccion', true );
    $maps      = get_post_meta( $post_id, '_futuretheme_espacio_maps', true );
    $correo    = get_post_meta( $post_id, '_futuretheme_espacio_correo', true );
    $telefono  = get_post_meta( $post_id, '_futuretheme_espacio_telefono', true );
    $tarifa    = get_post_meta( $post_id, '_futuretheme_espacio_tarifa', true );
    $menu_id   = get_post_meta( $post_id, '_futuretheme_espacio_menu_id', true );

    $image_url = get_the_post_thumbnail_url( $post_id, 'full' );

    if ( ! $image_url ) {
      $image_url = get_template_directory_uri() . '/assets/img/espacio-cultural-default.jpg';
    }

    $terms = get_the_terms( $post_id, 'espacio_categoria' );
    ?>

    <main id="primary" class="site-main single-espacio-cultural">

      <section class="espacio-hero">

        <div
          class="espacio-hero-bg"
          style="background-image: url('<?php echo esc_url( $image_url ); ?>');"
          aria-hidden="true"
        ></div>

        <div class="espacio-hero-overlay" aria-hidden="true"></div>

        <div class="espacio-hero-content">

          <div class="overline espacio-overline">
            <?php
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
              $term_names = wp_list_pluck( $terms, 'name' );
              echo esc_html( implode( ' · ', $term_names ) );
            } else {
              esc_html_e( 'Centro Cultural UNSA', 'futuretheme' );
            }
            ?>
          </div>

          <h1><?php the_title(); ?></h1>

        </div>

      </section>

      <?php if ( ! empty( $menu_id ) ) : ?>
        <nav class="espacio-subnav" aria-label="<?php esc_attr_e( 'Navegación del espacio cultural', 'futuretheme' ); ?>">
          <?php
          wp_nav_menu(
            array(
              'menu'        => absint( $menu_id ),
              'container'   => false,
              'menu_class'  => 'espacio-subnav-menu',
              'fallback_cb' => false,
              'depth'       => 1,
            )
          );
          ?>
        </nav>
      <?php endif; ?>

      <?php if ( ! empty( $horario ) || ! empty( $tarifa ) ) : ?>
        <section class="info-strip espacio-info-strip">
          <div class="info-strip-inner">

            <?php if ( ! empty( $horario ) ) : ?>
              <div class="info-strip-item">
                <span><?php esc_html_e( 'Horario', 'futuretheme' ); ?></span>
                <strong><?php echo nl2br( esc_html( $horario ) ); ?></strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $tarifa ) ) : ?>
              <div class="info-strip-item">
                <span><?php esc_html_e( 'Costo', 'futuretheme' ); ?></span>
                <strong><?php echo nl2br( esc_html( $tarifa ) ); ?></strong>
              </div>
            <?php endif; ?>

          </div>
        </section>
      <?php endif; ?>

      <section class="espacio-content-section">
        <div class="wrap espacio-content-grid">

          <article class="espacio-main-content fade-in">

            <?php if ( has_excerpt() ) : ?>
              <p class="espacio-lead">
                <?php echo esc_html( get_the_excerpt() ); ?>
              </p>
            <?php endif; ?>

            <div class="entry-content">
              <?php the_content(); ?>
            </div>

          </article>

          <aside class="info-card espacio-info-card fade-in">

            <h2><?php esc_html_e( 'Información', 'futuretheme' ); ?></h2>

            <?php if ( ! empty( $direccion ) ) : ?>
              <div class="info-row">
                <span><?php esc_html_e( 'Dirección', 'futuretheme' ); ?></span>
                <strong><?php echo nl2br( esc_html( $direccion ) ); ?></strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $telefono ) ) : ?>
              <div class="info-row">
                <span><?php esc_html_e( 'Teléfono', 'futuretheme' ); ?></span>
                <strong>
                  <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $telefono ) ); ?>">
                    <?php echo esc_html( $telefono ); ?>
                  </a>
                </strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $correo ) ) : ?>
              <div class="info-row">
                <span><?php esc_html_e( 'Correo', 'futuretheme' ); ?></span>
                <strong>
                  <a href="mailto:<?php echo esc_attr( antispambot( $correo ) ); ?>">
                    <?php echo esc_html( antispambot( $correo ) ); ?>
                  </a>
                </strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $maps ) ) : ?>
              <div class="info-row">
                <span><?php esc_html_e( 'Ubicación', 'futuretheme' ); ?></span>
                <strong>
                  <a href="<?php echo esc_url( $maps ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e( 'Ver en Google Maps', 'futuretheme' ); ?>
                  </a>
                </strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $capacidad ) ) : ?>
              <div class="info-row">
                <span><?php esc_html_e( 'Capacidad', 'futuretheme' ); ?></span>
                <strong><?php echo esc_html( $capacidad ); ?></strong>
              </div>
            <?php endif; ?>

            <?php if ( ! empty( $correo ) ) : ?>
              <a class="btn btn-dark info-card-btn" href="mailto:<?php echo esc_attr( antispambot( $correo ) ); ?>">
                <?php esc_html_e( 'Contactar', 'futuretheme' ); ?>
              </a>
            <?php endif; ?>

          </aside>

        </div>
      </section>

    </main>

    <?php
  endwhile;
endif;

get_footer();