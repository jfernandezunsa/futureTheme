<?php
/**
 * Página Contacto.
 *
 * Usa la página WordPress con slug "contacto" como contenedor visual
 * y lista dinámicamente los registros del post type "espacio_cultural"
 * pertenecientes a la categoría "espacios".
 *
 * @package FutureTheme
 */

get_header();

$page_id = get_queried_object_id();

$hero_image = get_the_post_thumbnail_url( $page_id, 'full' );

if ( ! $hero_image ) {
  $hero_image = get_template_directory_uri() . '/assets/img/footer-bg.jpg';
}

$contacto_query = new WP_Query(
  array(
    'post_type'      => 'espacio_cultural',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => array(
      'menu_order' => 'ASC',
      'date'       => 'DESC',
    ),
    'tax_query'      => array(
      array(
        'taxonomy' => 'espacio_categoria',
        'field'    => 'slug',
        'terms'    => array( 'espacios' ),
      ),
    ),
  )
);
?>

<main id="primary" class="site-main page-contacto">

  <section class="pg-hero contacto-hero">

    <img
      src="<?php echo esc_url( $hero_image ); ?>"
      alt="<?php echo esc_attr( get_the_title( $page_id ) ); ?>"
    >

    <div class="pg-hero-content">
      <div class="overline">
        <?php esc_html_e( 'Centro Cultural UNSA', 'futuretheme' ); ?>
      </div>

      <h1>
        <?php echo esc_html( get_the_title( $page_id ) ); ?>
      </h1>
    </div>

  </section>

  <section class="wrap contacto-section">

    <?php
    if ( have_posts() ) :
      while ( have_posts() ) :
        the_post();

        if ( trim( get_the_content() ) ) :
          ?>
          <div class="contacto-intro fade-in">
            <?php the_content(); ?>
          </div>
          <?php
        endif;

      endwhile;
    endif;
    ?>

    <?php if ( $contacto_query->have_posts() ) : ?>

      <div class="contacto-grid fade-in">

        <?php
        while ( $contacto_query->have_posts() ) :
          $contacto_query->the_post();

          set_query_var( 'futuretheme_contacto_card_index', $contacto_query->current_post + 1 );

          get_template_part( 'template-parts/espacios/card', 'contacto' );

        endwhile;
        ?>

      </div>

      <?php wp_reset_postdata(); ?>

    <?php else : ?>

      <div class="contacto-empty">
        <h2><?php esc_html_e( 'No hay espacios registrados', 'futuretheme' ); ?></h2>
        <p>
          <?php esc_html_e( 'Todavía no se han registrado espacios culturales con la categoría Espacios.', 'futuretheme' ); ?>
        </p>
      </div>

    <?php endif; ?>

  </section>

</main>

<?php
get_footer();