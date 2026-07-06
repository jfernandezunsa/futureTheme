<?php

/**
 * Front Page - FutureTheme
 *
 * @package FutureTheme
 */

get_header();
?>

<main id="primary" class="site-main">

  <?php get_template_part('template-parts/home/slider'); ?>

  <?php get_template_part('template-parts/home/destacado'); ?>

  <!--   <section class="ft-section">
    <div class="wrap">
      <div class="overline">Centro Cultural UNSAssss</div>
      <h2>Contenido de inicio</h2>
      <p>Esta es la portada del sitio.</p>
    </div>
  </section> -->

  <?php
  if (shortcode_exists('futuretheme_contadores')) {
    echo do_shortcode('[futuretheme_contadores 
    left_text="El Centro Cultural más grande de todo el Perú" 
    value_1="30000" 
    label_1="Visitantes Anuales" 
    value_2="500" 
    label_2="Actividades Anuales" 
    value_3="200" 
    label_3="Artistas" 
    value_4="" 
    label_4="" 
  ]');
  }
  ?>

</main>

<?php
get_footer();
