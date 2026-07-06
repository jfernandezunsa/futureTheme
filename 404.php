<?php

/**
 * Plantilla 404.
 *
 * Se muestra cuando WordPress no encuentra una página,
 * entrada, archivo o recurso solicitado.
 *
 * @package FutureTheme
 */

get_header();

$hero_image = get_template_directory_uri() . '/assets/img/404-default.jpg';
?>

<main id="primary" class="site-main">

  <div id="error404-general" class="error404-general">

    <section class="pg-hero error404-general-hero">

      <img
        src="<?php echo esc_url($hero_image); ?>"
        alt="<?php esc_attr_e('Página no encontrada', 'futuretheme'); ?>">

      <div class="pg-hero-content">

        <div class="overline">
          <?php esc_html_e('Error 404', 'futuretheme'); ?>
        </div>

        <h1>
          <?php esc_html_e('Página no encontrada', 'futuretheme'); ?>
        </h1>

        <div class="sub">
          <?php esc_html_e('El contenido que buscas no existe, fue movido o ya no se encuentra disponible.', 'futuretheme'); ?>
        </div>

      </div>

    </section>

    <section class="error404-general-content-section wrap">

      <div class="error404-box">

        <div class="museo-section-label">
          <?php esc_html_e('Centro Cultural UNSA', 'futuretheme'); ?>
        </div>

        <h2>
          <?php esc_html_e('No encontramos esta página', 'futuretheme'); ?>
        </h2>

        <p>
          <?php esc_html_e('Puedes volver al inicio o realizar una búsqueda dentro del sitio.', 'futuretheme'); ?>
        </p>

        <form role="search" method="get" class="search-page-form error404-search-form" action="<?php echo esc_url(home_url('/')); ?>">

          <label class="screen-reader-text" for="error404-search-field">
            <?php esc_html_e('Buscar en el sitio', 'futuretheme'); ?>
          </label>

          <input
            type="search"
            id="error404-search-field"
            class="search-page-input"
            name="s"
            value=""
            placeholder="<?php esc_attr_e('Buscar...', 'futuretheme'); ?>">

          <button type="submit" class="search-page-button">
            <?php esc_html_e('Buscar', 'futuretheme'); ?>
          </button>

        </form>

        <div class="error404-actions">

          <a class="btn btn-dark" href="<?php echo esc_url(home_url('/')); ?>">
            <?php esc_html_e('Volver al inicio', 'futuretheme'); ?>
          </a>

          <?php
          $museo_page = get_page_by_path('museo');
          $cine_page  = get_page_by_path('cineclub');
          $artes_page = get_page_by_path('artes-visuales');
          ?>

          <?php if ($museo_page) : ?>
            <a class="btn btn-outline" href="<?php echo esc_url(get_permalink($museo_page->ID)); ?>">
              <?php esc_html_e('Museo', 'futuretheme'); ?>
            </a>
          <?php endif; ?>

          <?php if ($cine_page) : ?>
            <a class="btn btn-outline" href="<?php echo esc_url(get_permalink($cine_page->ID)); ?>">
              <?php esc_html_e('Cineclub', 'futuretheme'); ?>
            </a>
          <?php endif; ?>

          <?php if ($artes_page) : ?>
            <a class="btn btn-outline" href="<?php echo esc_url(get_permalink($artes_page->ID)); ?>">
              <?php esc_html_e('Artes Visuales', 'futuretheme'); ?>
            </a>
          <?php endif; ?>

        </div>

      </div>

    </section>

  </div>

</main>

<?php
get_footer();
