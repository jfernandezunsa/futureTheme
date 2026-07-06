<?php

/**
 * Footer del tema FutureTheme.
 *
 * Este archivo centraliza el pie de página del sitio.
 * Usa widgets HTML para contenido institucional y menús dinámicos
 * para enlaces internos del footer.
 *
 * @package FutureTheme
 */
?>

<footer class="site-footer">

  <?php
  /**
   * Imagen de fondo del footer.
   *
   * Opción actual:
   * - Usa una imagen por defecto desde assets/img/footer-bg.jpg
   *
   * Más adelante puede reemplazarse por una opción del personalizador.
   */
  $futuretheme_footer_bg = get_template_directory_uri() . '/assets/img/footer-bg.jpg';
  ?>

  <div
    class="footer-bg-img"
    style="background-image: url('<?php echo esc_url($futuretheme_footer_bg); ?>');"
    aria-hidden="true"></div>

  <div class="footer-overlay" aria-hidden="true"></div>

  <div class="footer-inner">

    <?php if (is_active_sidebar('footer-institucion')) : ?>

      <?php dynamic_sidebar('footer-institucion'); ?>

    <?php else : ?>

      <div class="fc footer-widget footer-widget-institucion">
        <h4><?php esc_html_e('Institución', 'futuretheme'); ?></h4>
        <p class="fc-brand">
          <?php esc_html_e('Unidad de Promoción y Desarrollo Cultural de la UNSA', 'futuretheme'); ?>
        </p>
        <p>
          <?php esc_html_e('Lun–Vie: 6:30 p.m. – 8:10 p.m.', 'futuretheme'); ?>
        </p>
      </div>

    <?php endif; ?>


    <?php if (is_active_sidebar('footer-ubicacion')) : ?>

      <?php dynamic_sidebar('footer-ubicacion'); ?>

    <?php else : ?>

      <div class="fc footer-widget footer-widget-ubicacion">
        <h4><?php esc_html_e('Ubicación', 'futuretheme'); ?></h4>
        <p>
          <?php esc_html_e('Calle San Agustín 115', 'futuretheme'); ?><br>
          <?php esc_html_e('Segundo Patio', 'futuretheme'); ?><br>
          <?php esc_html_e('Arequipa, Perú', 'futuretheme'); ?>
        </p>
      </div>

    <?php endif; ?>


    <div class="fc footer-widget footer-widget-actividades">
      <h4><?php esc_html_e('Actividades', 'futuretheme'); ?></h4>

      <?php
      if (has_nav_menu('footer_activities')) {
        wp_nav_menu(
          array(
            'theme_location' => 'footer_activities',
            'container'      => false,
            'menu_class'     => 'footer-menu footer-menu-activities',
            'fallback_cb'    => false,
            'depth'          => 1,
          )
        );
      } else {
      ?>
        <ul class="footer-menu footer-menu-activities">
          <li><a href="<?php echo esc_url(home_url('/artes-visuales/')); ?>"><?php esc_html_e('Artes Visuales', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/cineclub/')); ?>"><?php esc_html_e('Cine', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/museo/')); ?>"><?php esc_html_e('Museo', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/actividades-musicales/')); ?>"><?php esc_html_e('Actividades Musicales', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/artes-escenicas/')); ?>"><?php esc_html_e('Artes Escénicas', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/actividades-literarias/')); ?>"><?php esc_html_e('Actividades Literarias', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/conferencias/')); ?>"><?php esc_html_e('Conferencias', 'futuretheme'); ?></a></li>
        </ul>
      <?php
      }
      ?>
    </div>


    <div class="fc footer-widget footer-widget-espacios">
      <h4><?php esc_html_e('Espacios', 'futuretheme'); ?></h4>

      <?php
      if (has_nav_menu('footer_spaces')) {
        wp_nav_menu(
          array(
            'theme_location' => 'footer_spaces',
            'container'      => false,
            'menu_class'     => 'footer-menu footer-menu-spaces',
            'fallback_cb'    => false,
            'depth'          => 1,
          )
        );
      } else {
      ?>
        <ul class="footer-menu footer-menu-spaces">
          <li><a href="<?php echo esc_url(home_url('/galerias/')); ?>"><?php esc_html_e('Casona Irriberry', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/cineclub/')); ?>"><?php esc_html_e('Cineclub UNSA', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/museo/')); ?>"><?php esc_html_e('Museo Arqueológico', 'futuretheme'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/salas/')); ?>"><?php esc_html_e('Salas de Presentación', 'futuretheme'); ?></a></li>
        </ul>
      <?php
      }
      ?>
    </div>


    <?php if (is_active_sidebar('footer-contacto')) : ?>

      <?php dynamic_sidebar('footer-contacto'); ?>

    <?php else : ?>

      <div class="fc footer-widget footer-widget-contacto">
        <h4><?php esc_html_e('Contacto', 'futuretheme'); ?></h4>

        <a href="tel:+542585477">
          <?php esc_html_e('+54 2585477', 'futuretheme'); ?>
        </a>

        <a href="mailto:updc_galeriasdearte@unsa.edu.pe">
          <?php esc_html_e('updc_galeriasdearte@unsa.edu.pe', 'futuretheme'); ?>
        </a>

        <a href="mailto:updc_cineclub@unsa.edu.pe">
          <?php esc_html_e('updc_cineclub@unsa.edu.pe', 'futuretheme'); ?>
        </a>

        <a href="mailto:updc_museo@unsa.edu.pe">
          <?php esc_html_e('updc_museo@unsa.edu.pe', 'futuretheme'); ?>
        </a>

        <a href="#" class="footer-social-link">
          <?php esc_html_e('Instagram', 'futuretheme'); ?>
        </a>

        <a href="#" class="footer-social-link">
          <?php esc_html_e('Facebook', 'futuretheme'); ?>
        </a>

        <a href="#" class="footer-social-link">
          <?php esc_html_e('YouTube', 'futuretheme'); ?>
        </a>
      </div>

    <?php endif; ?>

  </div>

  <div class="footer-copy">
    &copy; <?php echo esc_html(date_i18n('Y')); ?>
    <?php esc_html_e('Centro Cultural UNSA · Universidad Nacional de San Agustín · Arequipa, Perú', 'futuretheme'); ?>
  </div>

</footer>

<?php wp_footer(); ?>

</body>

</html>
