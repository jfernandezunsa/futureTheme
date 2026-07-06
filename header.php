<?php

/**
 * Header del tema FutureTheme
 *
 * @package FutureTheme
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <header class="site-header">
    <nav class="main-nav" aria-label="<?php esc_attr_e('Menú principal', 'futuretheme'); ?>">

      <a class="nav-brand" href="<?php echo esc_url(home_url('/')); ?>">

        <div class="nav-logo">
          <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
          <?php else : ?>
            <span class="nav-logo-fallback">
              <?php echo esc_html(get_bloginfo('name')); ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="nav-sep"></div>

        <div class="nav-title">
          <strong><?php echo esc_html(get_bloginfo('name')); ?></strong>
          <span><?php echo esc_html(get_bloginfo('description')); ?></span>
        </div>

      </a>

      <button
        class="nav-toggle"
        type="button"
        aria-controls="primary-menu"
        aria-expanded="false"
        aria-label="<?php esc_attr_e('Abrir menú principal', 'futuretheme'); ?>">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <?php
      wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_id'        => 'primary-menu',
        'menu_class'     => 'nav-links',
        'container'      => false,
        'fallback_cb'    => false,
        'depth'          => 2,
      ));
      ?>

      <form class="nav-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">

        <label class="screen-reader-text" for="nav-search-field">
          <?php esc_html_e('Buscar en el sitio', 'futuretheme'); ?>
        </label>

        <input
          type="search"
          id="nav-search-field"
          class="nav-search-input"
          name="s"
          value="<?php echo esc_attr(get_search_query()); ?>"
          placeholder="<?php esc_attr_e('Buscar...', 'futuretheme'); ?>">

        <button class="nav-search" type="submit" aria-label="<?php esc_attr_e('Buscar', 'futuretheme'); ?>">
          <svg width="11" height="11" viewBox="0 0 14 14" fill="none" aria-hidden="true" focusable="false">
            <circle cx="5.5" cy="5.5" r="4.5" stroke="currentColor" stroke-width="1.7" />
            <path d="m9 9 3.5 3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
          </svg>
          <span><?php esc_html_e('Buscar', 'futuretheme'); ?></span>
        </button>

      </form>

    </nav>
  </header>
