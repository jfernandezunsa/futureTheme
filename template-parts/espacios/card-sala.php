<?php
/**
 * Card individual de Sala.
 *
 * Se usa dentro de page-salas.php para mostrar cada registro
 * del post type espacio_cultural con categoría salas.
 *
 * @package FutureTheme
 */

$sala_id = get_the_ID();

$capacidad = get_post_meta( $sala_id, '_futuretheme_espacio_capacidad', true );
$tags      = get_the_terms( $sala_id, 'espacio_etiqueta' );

$card_index = get_query_var( 'futuretheme_sala_card_index' );

if ( empty( $card_index ) ) {
  $card_index = 1;
}
?>

<article id="sala-<?php the_ID(); ?>" <?php post_class( 'sala-card sala-card-' . absint( $card_index ) ); ?>>

  <div class="sala-card-color" aria-hidden="true"></div>

  <div class="sala-card-body">

    <h2>
      <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
    </h2>

    <?php if ( has_excerpt() ) : ?>
      <p>
        <?php echo esc_html( get_the_excerpt() ); ?>
      </p>
    <?php endif; ?>

    <?php if ( ! empty( $capacidad ) ) : ?>
      <div class="sala-aforo">
        <div class="sala-aforo-n">
          <?php echo esc_html( $capacidad ); ?>
        </div>

        <div class="sala-aforo-l">
          <?php esc_html_e( 'personas', 'futuretheme' ); ?><br>
          <?php esc_html_e( 'de aforo', 'futuretheme' ); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
      <div class="sala-features">
        <?php foreach ( $tags as $tag ) : ?>
          <span class="sala-tag">
            <?php echo esc_html( $tag->name ); ?>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

</article>