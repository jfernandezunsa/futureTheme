<?php
/**
 * Página Salas de Presentación.
 *
 * Usa la página WordPress con slug "salas" como contenedor visual
 * y lista dinámicamente los registros del post type "espacio_cultural"
 * pertenecientes a la categoría "salas".
 *
 * @package FutureTheme
 */

get_header();

$page_id = get_queried_object_id();

$hero_image = get_the_post_thumbnail_url( $page_id, 'full' );

if ( ! $hero_image ) {
  $hero_image = get_template_directory_uri() . '/assets/img/footer-bg.jpg';
}

$salas_query = new WP_Query(
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
        'terms'    => array( 'salas' ),
      ),
    ),
  )
);
?>

<main id="primary" class="site-main page-salas">

  <section class="pg-hero salas-hero">

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

  <section class="wrap salas-section">

    <?php
    if ( have_posts() ) :
      while ( have_posts() ) :
        the_post();

        if ( trim( get_the_content() ) ) :
          ?>
          <div class="salas-intro fade-in">
            <?php the_content(); ?>
          </div>
          <?php
        endif;

      endwhile;
    endif;
    ?>

    <?php if ( $salas_query->have_posts() ) : ?>

      <div class="salas-grid fade-in">

        <?php
        while ( $salas_query->have_posts() ) :
          $salas_query->the_post();

          set_query_var( 'futuretheme_sala_card_index', $salas_query->current_post + 1 );

          get_template_part( 'template-parts/espacios/card', 'sala' );

        endwhile;
        ?>

      </div>

      <?php wp_reset_postdata(); ?>

    <?php else : ?>

      <div class="salas-empty">
        <h2><?php esc_html_e( 'No hay salas registradas', 'futuretheme' ); ?></h2>
        <p>
          <?php esc_html_e( 'Todavía no se han registrado espacios culturales con la categoría Salas.', 'futuretheme' ); ?>
        </p>
      </div>

    <?php endif; ?>

  </section>

</main>

<?php
get_footer();