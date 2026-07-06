<?php
/**
 * Card de contacto para Espacios Culturales.
 *
 * Muestra una tarjeta simple de contacto usando datos del post type
 * "espacio_cultural", respetando el modelo HTML original:
 * - Sin imagen.
 * - Sin botón.
 * - Sin enlace al single.
 * - Solo título y campos personalizados.
 *
 * @package FutureTheme
 */

$espacio_id = get_the_ID();

$horario   = get_post_meta( $espacio_id, '_futuretheme_espacio_horario', true );
$direccion = get_post_meta( $espacio_id, '_futuretheme_espacio_direccion', true );
$maps      = get_post_meta( $espacio_id, '_futuretheme_espacio_maps', true );
$correo    = get_post_meta( $espacio_id, '_futuretheme_espacio_correo', true );
$telefono  = get_post_meta( $espacio_id, '_futuretheme_espacio_telefono', true );
$tarifa    = get_post_meta( $espacio_id, '_futuretheme_espacio_tarifa', true );

$card_index = get_query_var( 'futuretheme_contacto_card_index' );

if ( empty( $card_index ) ) {
  $card_index = 1;
}
?>

<article id="contacto-espacio-<?php the_ID(); ?>" <?php post_class( 'contacto-card contacto-card-' . absint( $card_index ) ); ?>>

  <div class="contacto-card-head" aria-hidden="true"></div>

  <div class="contacto-card-body">

    <h3><?php the_title(); ?></h3>

    <?php if ( ! empty( $horario ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Horario', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo nl2br( esc_html( $horario ) ); ?></span>
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $direccion ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Ubicación', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo nl2br( esc_html( $direccion ) ); ?></span>
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $telefono ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Teléfono', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo esc_html( $telefono ); ?></span>
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $correo ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Correo', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo esc_html( antispambot( $correo ) ); ?></span>
      </div>
    <?php endif; ?>

    <?php if ( ! empty( $tarifa ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Tarifa', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo nl2br( esc_html( $tarifa ) ); ?></span>
      </div>
    <?php endif; ?>

 <!--    <?php if ( ! empty( $maps ) ) : ?>
      <div class="ci">
        <span class="ci-lbl"><?php esc_html_e( 'Mapa', 'futuretheme' ); ?></span>
        <span class="ci-val"><?php echo esc_html( $maps ); ?></span>
      </div>
    <?php endif; ?> -->

  </div>

</article>