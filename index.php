<?php

/**
 * Plantilla principal del tema FutureTheme.
 *
 * Este archivo es obligatorio en todo tema clásico de WordPress.
 * Funciona como plantilla de respaldo cuando WordPress no encuentra
 * una plantilla más específica como front-page.php, page.php o single.php.
 *
 * @package FutureTheme
 */

get_header();
?>

<main id="primary" class="site-main" style="padding-top: var(--ft-header-height);">


  <section class="ft-section">
    <div class="wrap">

      <?php if (have_posts()) : ?>

        <?php if (is_home() && ! is_front_page()) : ?>
          <header class="page-header" style="margin-bottom: 40px;">
            <div class="overline">Publicaciones</div>
            <h1><?php single_post_title(); ?></h1>
          </header>
        <?php endif; ?>

        <?php
        while (have_posts()) :
          the_post();
        ?>

          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom: 48px;">

            <header class="entry-header" style="margin-bottom: 18px;">
              <?php if (is_singular()) : ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
              <?php else : ?>
                <h2 class="entry-title">
                  <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                  </a>
                </h2>
              <?php endif; ?>
            </header>

            <?php if (has_post_thumbnail()) : ?>
              <div class="entry-thumbnail" style="margin-bottom: 22px;">
                <a href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail('large'); ?>
                </a>
              </div>
            <?php endif; ?>

            <div class="entry-content">
              <?php
              if (is_singular()) {
                the_content();
              } else {
                the_excerpt();
              }
              ?>
            </div>

            <?php if (! is_singular()) : ?>
              <div style="margin-top: 20px;">
                <a class="btn btn-dark" href="<?php the_permalink(); ?>">
                  Leer más
                </a>
              </div>
            <?php endif; ?>

          </article>

        <?php endwhile; ?>

        <div class="posts-navigation" style="margin-top: 40px;">
          <?php
          the_posts_navigation(
            array(
              'prev_text' => '← Publicaciones anteriores',
              'next_text' => 'Publicaciones siguientes →',
            )
          );
          ?>
        </div>

      <?php else : ?>

        <section class="no-results not-found">
          <div class="overline">Sin contenido</div>
          <h1>No se encontraron publicaciones</h1>
          <p style="max-width: 640px; margin-top: 16px; color: var(--ft-color-muted);">
            Todavía no hay contenido publicado o no se encontró información para mostrar.
          </p>
        </section>

      <?php endif; ?>

    </div>
  </section>

</main>

<?php
get_footer();
