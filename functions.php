<?php

/**
 * Funciones principales del tema FutureTheme.
 *
 * @package FutureTheme
 */

if (! defined('ABSPATH')) {
  exit;
}


/* ============================================================
   01. CONFIGURACIÓN GENERAL DEL TEMA
   ============================================================ */

function futuretheme_setup()
{

  add_theme_support('title-tag');

  add_theme_support('post-thumbnails');

  /*
   * Soporte para bloques / Gutenberg.
   * Permite que theme.json y los estilos del tema se reflejen mejor en el editor.
   */
  add_theme_support('wp-block-styles');
  add_theme_support('align-wide');
  add_theme_support('editor-styles');
  add_theme_support('responsive-embeds');

  /*
   * Carga style.css dentro del editor de bloques.
   * Esto ayuda a que el contenido editado se parezca más al front-end.
   */
  add_editor_style('style.css');

  /*
   * Permite usar Extracto en páginas.
   * Esto es necesario para que page.php pueda mostrar el subtítulo del hero
   * solo cuando se redacte manualmente.
   */
  add_post_type_support('page', 'excerpt');

  add_theme_support(
    'custom-logo',
    array(
      'height'      => 80,
      'width'       => 260,
      'flex-height' => true,
      'flex-width'  => true,
    )
  );

  register_nav_menus(
    array(
      'primary'           => __('Menú principal', 'futuretheme'),
      'footer_activities' => __('Menú Footer Actividades', 'futuretheme'),
      'footer_spaces'     => __('Menú Footer Espacios', 'futuretheme'),
    )
  );
}
add_action('after_setup_theme', 'futuretheme_setup');


/* ============================================================
   02. CARGA DE ESTILOS Y SCRIPTS
   ============================================================ */

function futuretheme_assets()
{

  wp_enqueue_style(
    'futuretheme-style',
    get_stylesheet_uri(),
    array(),
    wp_get_theme()->get('Version')
  );

  wp_enqueue_script(
    'futuretheme-main',
    get_template_directory_uri() . '/assets/js/theme.js',
    array(),
    wp_get_theme()->get('Version'),
    true
  );

  if (is_front_page()) {
    wp_enqueue_script(
      'futuretheme-destacado',
      get_template_directory_uri() . '/assets/js/destacado.js',
      array('futuretheme-main'),
      wp_get_theme()->get('Version'),
      true
    );
  }
}
add_action('wp_enqueue_scripts', 'futuretheme_assets');


/* ============================================================
   03. REGISTRO DE WIDGETS DEL FOOTER
   ============================================================ */

function futuretheme_widgets_init()
{

  register_sidebar(
    array(
      'name'          => __('Footer Institución', 'futuretheme'),
      'id'            => 'footer-institucion',
      'description'   => __('Contenido HTML de la columna Institución del footer.', 'futuretheme'),
      'before_widget' => '<div id="%1$s" class="fc footer-widget footer-widget-institucion %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4>',
      'after_title'   => '</h4>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Footer Ubicación', 'futuretheme'),
      'id'            => 'footer-ubicacion',
      'description'   => __('Contenido HTML de la columna Ubicación del footer.', 'futuretheme'),
      'before_widget' => '<div id="%1$s" class="fc footer-widget footer-widget-ubicacion %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4>',
      'after_title'   => '</h4>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Footer Contacto', 'futuretheme'),
      'id'            => 'footer-contacto',
      'description'   => __('Contenido HTML de la columna Contacto del footer.', 'futuretheme'),
      'before_widget' => '<div id="%1$s" class="fc footer-widget footer-widget-contacto %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4>',
      'after_title'   => '</h4>',
    )
  );
}
add_action('widgets_init', 'futuretheme_widgets_init');


/* ============================================================
   04. POST PERSONALIZADO: DESTACADOS
   ============================================================ */

/**
 * Registra el post type Destacados.
 *
 * Este post type reemplaza al antiguo post type Hero.
 * Se usará para:
 * - Slider principal de portada: categoría hero.
 * - Tarjetas de actualidad cultural: categoría destacado.
 */
function futuretheme_register_destacado_post_type()
{

  $labels = array(
    'name'                  => __('Destacados', 'futuretheme'),
    'singular_name'         => __('Destacado', 'futuretheme'),
    'menu_name'             => __('Destacados', 'futuretheme'),
    'name_admin_bar'        => __('Destacado', 'futuretheme'),
    'add_new'               => __('Añadir nuevo', 'futuretheme'),
    'add_new_item'          => __('Añadir nuevo destacado', 'futuretheme'),
    'new_item'              => __('Nuevo destacado', 'futuretheme'),
    'edit_item'             => __('Editar destacado', 'futuretheme'),
    'view_item'             => __('Ver destacado', 'futuretheme'),
    'all_items'             => __('Todos los destacados', 'futuretheme'),
    'search_items'          => __('Buscar destacados', 'futuretheme'),
    'parent_item_colon'     => __('Destacado superior:', 'futuretheme'),
    'not_found'             => __('No se encontraron destacados.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron destacados en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen destacada', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen destacada', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen destacada', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen destacada', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'description'        => __('Elementos destacados reutilizables para slider, actualidad cultural y difusión.', 'futuretheme'),
    'public'             => true,
    'publicly_queryable' => false,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_admin_bar'  => true,
    'show_in_nav_menus'  => false,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'destacado'),
    'capability_type'    => 'post',
    'has_archive'        => false,
    'hierarchical'       => false,
    'menu_position'      => 21,
    'menu_icon'          => 'dashicons-star-filled',
    'supports'           => array(
      'title',
      'excerpt',
      'thumbnail',
      'page-attributes',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('destacado', $args);
}
add_action('init', 'futuretheme_register_destacado_post_type');


/* ============================================================
   05. TAXONOMÍA: CATEGORÍAS DE DESTACADOS
   ============================================================ */

/**
 * Registra la taxonomía para clasificar los destacados.
 *
 * Categorías base:
 * - hero
 * - destacado
 */
function futuretheme_register_destacado_taxonomy()
{

  $labels = array(
    'name'              => __('Categorías de Destacados', 'futuretheme'),
    'singular_name'     => __('Categoría de Destacado', 'futuretheme'),
    'search_items'      => __('Buscar categorías', 'futuretheme'),
    'all_items'         => __('Todas las categorías', 'futuretheme'),
    'parent_item'       => __('Categoría superior', 'futuretheme'),
    'parent_item_colon' => __('Categoría superior:', 'futuretheme'),
    'edit_item'         => __('Editar categoría', 'futuretheme'),
    'update_item'       => __('Actualizar categoría', 'futuretheme'),
    'add_new_item'      => __('Añadir nueva categoría', 'futuretheme'),
    'new_item_name'     => __('Nuevo nombre de categoría', 'futuretheme'),
    'menu_name'         => __('Categorías', 'futuretheme'),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array('slug' => 'destacado-categoria'),
    'show_in_rest'      => true,
  );

  register_taxonomy('destacado_categoria', array('destacado'), $args);
}
add_action('init', 'futuretheme_register_destacado_taxonomy');


/* ============================================================
   06. CREACIÓN DE CATEGORÍAS BASE
   ============================================================ */

/**
 * Crea automáticamente las categorías base:
 * - Hero
 * - Destacado
 *
 * No duplica categorías si ya existen.
 */
function futuretheme_create_default_destacado_terms()
{

  if (! term_exists('hero', 'destacado_categoria')) {
    wp_insert_term(
      'Hero',
      'destacado_categoria',
      array(
        'slug'        => 'hero',
        'description' => 'Elementos usados en el slider principal de la portada.',
      )
    );
  }

  if (! term_exists('destacado', 'destacado_categoria')) {
    wp_insert_term(
      'Destacado',
      'destacado_categoria',
      array(
        'slug'        => 'destacado',
        'description' => 'Elementos usados en las tarjetas de actualidad cultural.',
      )
    );
  }
}
add_action('init', 'futuretheme_create_default_destacado_terms');


/* ============================================================
   07. CAMPOS PERSONALIZADOS DE DESTACADOS
   ============================================================ */

/**
 * Agrega la caja de campos personalizados al post type Destacados.
 */
function futuretheme_add_destacado_meta_box()
{
  add_meta_box(
    'futuretheme_destacado_fields',
    __('Campos del destacado', 'futuretheme'),
    'futuretheme_render_destacado_meta_box',
    'destacado',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_destacado_meta_box');


/**
 * Renderiza los campos personalizados.
 */
function futuretheme_render_destacado_meta_box($post)
{

  wp_nonce_field('futuretheme_save_destacado_fields', 'futuretheme_destacado_nonce');

  $destacado_tag         = get_post_meta($post->ID, '_futuretheme_destacado_tag', true);
  $destacado_button_text = get_post_meta($post->ID, '_futuretheme_destacado_button_text', true);
  $destacado_button_url  = get_post_meta($post->ID, '_futuretheme_destacado_button_url', true);
  $destacado_new_tab     = get_post_meta($post->ID, '_futuretheme_destacado_new_tab', true);
?>

  <p>
    <label for="futuretheme_destacado_tag">
      <strong><?php esc_html_e('Texto superior opcional', 'futuretheme'); ?></strong>
    </label>
  </p>

  <input
    type="text"
    id="futuretheme_destacado_tag"
    name="futuretheme_destacado_tag"
    value="<?php echo esc_attr($destacado_tag); ?>"
    class="widefat"
    placeholder="Ejemplo: Centro Cultural UNSA — Arequipa">

  <p style="margin-top: 18px;">
    <label for="futuretheme_destacado_button_text">
      <strong><?php esc_html_e('Texto del botón opcional', 'futuretheme'); ?></strong>
    </label>
  </p>

  <input
    type="text"
    id="futuretheme_destacado_button_text"
    name="futuretheme_destacado_button_text"
    value="<?php echo esc_attr($destacado_button_text); ?>"
    class="widefat"
    placeholder="Ejemplo: Más información">

  <p style="margin-top: 18px;">
    <label for="futuretheme_destacado_button_url">
      <strong><?php esc_html_e('Enlace del botón o tarjeta', 'futuretheme'); ?></strong>
    </label>
  </p>

  <input
    type="url"
    id="futuretheme_destacado_button_url"
    name="futuretheme_destacado_button_url"
    value="<?php echo esc_url($destacado_button_url); ?>"
    class="widefat"
    placeholder="Ejemplo: https://www.unsa.edu.pe/">

  <p style="margin-top: 18px;">
    <label for="futuretheme_destacado_new_tab">
      <input
        type="checkbox"
        id="futuretheme_destacado_new_tab"
        name="futuretheme_destacado_new_tab"
        value="1"
        <?php checked($destacado_new_tab, '1'); ?>>
      <strong><?php esc_html_e('Abrir enlace en una nueva pestaña', 'futuretheme'); ?></strong>
    </label>
  </p>

  <p style="margin-top: 6px; color: #666;">
    <?php esc_html_e('Use esta opción para enlaces externos, formularios, redes sociales, plataformas de difusión o sitios fuera del portal.', 'futuretheme'); ?>
  </p>

  <hr style="margin: 22px 0;">

  <p style="color: #666;">
    <strong><?php esc_html_e('Indicaciones de uso:', 'futuretheme'); ?></strong>
  </p>

  <ul style="list-style: disc; margin-left: 20px; color: #666;">
    <li><?php esc_html_e('El título será usado como texto principal.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('El extracto será usado como descripción en el slider.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('La imagen destacada será usada como imagen principal o fondo visual.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('La categoría Hero se usará para el slider principal de portada.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('La categoría Destacado se usará para las tarjetas de actualidad cultural.', 'futuretheme'); ?></li>
  </ul>

<?php
}


/**
 * Guarda los campos personalizados del destacado.
 */
function futuretheme_save_destacado_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_destacado_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_destacado_nonce'])),
      'futuretheme_save_destacado_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (isset($_POST['post_type']) && 'destacado' === $_POST['post_type']) {
    if (! current_user_can('edit_post', $post_id)) {
      return;
    }
  }

  if (isset($_POST['futuretheme_destacado_tag'])) {
    update_post_meta(
      $post_id,
      '_futuretheme_destacado_tag',
      sanitize_text_field(wp_unslash($_POST['futuretheme_destacado_tag']))
    );
  } else {
    delete_post_meta($post_id, '_futuretheme_destacado_tag');
  }

  if (isset($_POST['futuretheme_destacado_button_text'])) {
    update_post_meta(
      $post_id,
      '_futuretheme_destacado_button_text',
      sanitize_text_field(wp_unslash($_POST['futuretheme_destacado_button_text']))
    );
  } else {
    delete_post_meta($post_id, '_futuretheme_destacado_button_text');
  }

  if (isset($_POST['futuretheme_destacado_button_url'])) {
    update_post_meta(
      $post_id,
      '_futuretheme_destacado_button_url',
      esc_url_raw(wp_unslash($_POST['futuretheme_destacado_button_url']))
    );
  } else {
    delete_post_meta($post_id, '_futuretheme_destacado_button_url');
  }

  $destacado_new_tab = isset($_POST['futuretheme_destacado_new_tab']) ? '1' : '0';

  update_post_meta(
    $post_id,
    '_futuretheme_destacado_new_tab',
    $destacado_new_tab
  );
}
add_action('save_post_destacado', 'futuretheme_save_destacado_fields');


/* Espacios Culturales */

/* ============================================================
   POST PERSONALIZADO: ESPACIOS CULTURALES
   ============================================================ */

/**
 * Registra el post type Espacios Culturales.
 */
function futuretheme_register_espacios_culturales_post_type()
{

  $labels = array(
    'name'                  => __('Espacios Culturales', 'futuretheme'),
    'singular_name'         => __('Espacio Cultural', 'futuretheme'),
    'menu_name'             => __('Espacios Culturales', 'futuretheme'),
    'name_admin_bar'        => __('Espacio Cultural', 'futuretheme'),
    'add_new'               => __('Añadir nuevo', 'futuretheme'),
    'add_new_item'          => __('Añadir nuevo espacio cultural', 'futuretheme'),
    'new_item'              => __('Nuevo espacio cultural', 'futuretheme'),
    'edit_item'             => __('Editar espacio cultural', 'futuretheme'),
    'view_item'             => __('Ver espacio cultural', 'futuretheme'),
    'all_items'             => __('Todos los espacios culturales', 'futuretheme'),
    'search_items'          => __('Buscar espacios culturales', 'futuretheme'),
    'not_found'             => __('No se encontraron espacios culturales.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron espacios culturales en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen del espacio cultural', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen del espacio cultural', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen del espacio cultural', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'description'        => __('Espacios culturales como galerías, museo, cineclub, salas y auditorios.', 'futuretheme'),
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'show_in_admin_bar'  => true,
    'show_in_nav_menus'  => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'espacios-culturales',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 22,
    'menu_icon'          => 'dashicons-location-alt',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('espacio_cultural', $args);
}
add_action('init', 'futuretheme_register_espacios_culturales_post_type');


/* ============================================================
   TAXONOMÍA: CATEGORÍAS DE ESPACIOS CULTURALES
   ============================================================ */

/**
 * Registra categorías para Espacios Culturales.
 */
function futuretheme_register_espacios_culturales_taxonomy()
{

  $labels = array(
    'name'              => __('Categorías de Espacios', 'futuretheme'),
    'singular_name'     => __('Categoría de Espacio', 'futuretheme'),
    'search_items'      => __('Buscar categorías', 'futuretheme'),
    'all_items'         => __('Todas las categorías', 'futuretheme'),
    'parent_item'       => __('Categoría superior', 'futuretheme'),
    'parent_item_colon' => __('Categoría superior:', 'futuretheme'),
    'edit_item'         => __('Editar categoría', 'futuretheme'),
    'update_item'       => __('Actualizar categoría', 'futuretheme'),
    'add_new_item'      => __('Añadir nueva categoría', 'futuretheme'),
    'new_item_name'     => __('Nuevo nombre de categoría', 'futuretheme'),
    'menu_name'         => __('Categorías', 'futuretheme'),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array(
      'slug'       => 'categoria-espacio',
      'with_front' => false,
    ),
    'show_in_rest'      => true,
  );

  register_taxonomy('espacio_categoria', array('espacio_cultural'), $args);
}
add_action('init', 'futuretheme_register_espacios_culturales_taxonomy');


/* ============================================================
   TAXONOMÍA: ETIQUETAS DE ESPACIOS CULTURALES
   ============================================================ */

/**
 * Registra etiquetas para Espacios Culturales.
 */
function futuretheme_register_espacios_culturales_tags()
{

  $labels = array(
    'name'                       => __('Etiquetas de Espacios', 'futuretheme'),
    'singular_name'              => __('Etiqueta de Espacio', 'futuretheme'),
    'search_items'               => __('Buscar etiquetas', 'futuretheme'),
    'popular_items'              => __('Etiquetas populares', 'futuretheme'),
    'all_items'                  => __('Todas las etiquetas', 'futuretheme'),
    'edit_item'                  => __('Editar etiqueta', 'futuretheme'),
    'update_item'                => __('Actualizar etiqueta', 'futuretheme'),
    'add_new_item'               => __('Añadir nueva etiqueta', 'futuretheme'),
    'new_item_name'              => __('Nuevo nombre de etiqueta', 'futuretheme'),
    'separate_items_with_commas' => __('Separar etiquetas con comas', 'futuretheme'),
    'add_or_remove_items'        => __('Añadir o quitar etiquetas', 'futuretheme'),
    'choose_from_most_used'      => __('Elegir entre las más usadas', 'futuretheme'),
    'not_found'                  => __('No se encontraron etiquetas.', 'futuretheme'),
    'menu_name'                  => __('Etiquetas', 'futuretheme'),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array(
      'slug'       => 'etiqueta-espacio',
      'with_front' => false,
    ),
    'show_in_rest'          => true,
  );

  register_taxonomy('espacio_etiqueta', array('espacio_cultural'), $args);
}
add_action('init', 'futuretheme_register_espacios_culturales_tags');


/* ============================================================
   CAMPOS PERSONALIZADOS: ESPACIOS CULTURALES
   ============================================================ */

/**
 * Agrega el metabox de datos del espacio cultural.
 */
function futuretheme_add_espacio_cultural_meta_box()
{

  add_meta_box(
    'futuretheme_espacio_cultural_fields',
    __('Datos del espacio cultural', 'futuretheme'),
    'futuretheme_render_espacio_cultural_meta_box',
    'espacio_cultural',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_espacio_cultural_meta_box');


/**
 * Renderiza los campos personalizados.
 *
 * @param WP_Post $post Post actual.
 */
function futuretheme_render_espacio_cultural_meta_box($post)
{

  wp_nonce_field('futuretheme_save_espacio_cultural_fields', 'futuretheme_espacio_cultural_nonce');

  $capacidad = get_post_meta($post->ID, '_futuretheme_espacio_capacidad', true);
  $horario   = get_post_meta($post->ID, '_futuretheme_espacio_horario', true);
  $direccion = get_post_meta($post->ID, '_futuretheme_espacio_direccion', true);
  $maps      = get_post_meta($post->ID, '_futuretheme_espacio_maps', true);
  $correo    = get_post_meta($post->ID, '_futuretheme_espacio_correo', true);
  $telefono  = get_post_meta($post->ID, '_futuretheme_espacio_telefono', true);
  $tarifa    = get_post_meta($post->ID, '_futuretheme_espacio_tarifa', true);
  $menu_id   = get_post_meta($post->ID, '_futuretheme_espacio_menu_id', true);

  $menus = wp_get_nav_menus();
?>

  <p>
    <label for="futuretheme_espacio_capacidad">
      <strong><?php esc_html_e('Capacidad', 'futuretheme'); ?></strong>
    </label>
  </p>
  <input
    type="text"
    id="futuretheme_espacio_capacidad"
    name="futuretheme_espacio_capacidad"
    value="<?php echo esc_attr($capacidad); ?>"
    class="widefat"
    placeholder="Ejemplo: 120 personas">

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_horario">
      <strong><?php esc_html_e('Horario', 'futuretheme'); ?></strong>
    </label>
  </p>
  <textarea
    id="futuretheme_espacio_horario"
    name="futuretheme_espacio_horario"
    class="widefat"
    rows="3"
    placeholder="Ejemplo: Lunes a viernes de 08:30 a 15:15 hrs."><?php echo esc_textarea($horario); ?></textarea>

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_direccion">
      <strong><?php esc_html_e('Dirección', 'futuretheme'); ?></strong>
    </label>
  </p>
  <textarea
    id="futuretheme_espacio_direccion"
    name="futuretheme_espacio_direccion"
    class="widefat"
    rows="3"
    placeholder="Ejemplo: Calle Álvarez Thomas N° 200, Cercado, Arequipa"><?php echo esc_textarea($direccion); ?></textarea>

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_maps">
      <strong><?php esc_html_e('Ubicación Google Maps', 'futuretheme'); ?></strong>
    </label>
  </p>
  <input
    type="url"
    id="futuretheme_espacio_maps"
    name="futuretheme_espacio_maps"
    value="<?php echo esc_url($maps); ?>"
    class="widefat"
    placeholder="Ejemplo: https://maps.google.com/...">

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_correo">
      <strong><?php esc_html_e('Correo', 'futuretheme'); ?></strong>
    </label>
  </p>
  <input
    type="email"
    id="futuretheme_espacio_correo"
    name="futuretheme_espacio_correo"
    value="<?php echo esc_attr($correo); ?>"
    class="widefat"
    placeholder="Ejemplo: updc_museo@unsa.edu.pe">

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_telefono">
      <strong><?php esc_html_e('Teléfono', 'futuretheme'); ?></strong>
    </label>
  </p>
  <input
    type="text"
    id="futuretheme_espacio_telefono"
    name="futuretheme_espacio_telefono"
    value="<?php echo esc_attr($telefono); ?>"
    class="widefat"
    placeholder="Ejemplo: 95874662">

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_tarifa">
      <strong><?php esc_html_e('Tarifa', 'futuretheme'); ?></strong>
    </label>
  </p>
  <textarea
    id="futuretheme_espacio_tarifa"
    name="futuretheme_espacio_tarifa"
    class="widefat"
    rows="3"
    placeholder="Ejemplo: Adultos S/. 5.00 · Estudiantes S/. 2.00 · Escolares S/. 1.00"><?php echo esc_textarea($tarifa); ?></textarea>

  <p style="margin-top: 18px;">
    <label for="futuretheme_espacio_menu_id">
      <strong><?php esc_html_e('Menú asociado', 'futuretheme'); ?></strong>
    </label>
  </p>

  <select
    id="futuretheme_espacio_menu_id"
    name="futuretheme_espacio_menu_id"
    class="widefat">
    <option value="">
      <?php esc_html_e('— No seleccionar menú —', 'futuretheme'); ?>
    </option>

    <?php if (! empty($menus)) : ?>
      <?php foreach ($menus as $menu) : ?>
        <option
          value="<?php echo esc_attr($menu->term_id); ?>"
          <?php selected((string) $menu_id, (string) $menu->term_id); ?>>
          <?php echo esc_html($menu->name); ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>

  <p style="margin-top: 6px; color: #666;">
    <?php esc_html_e('Seleccione un menú de WordPress para mostrarlo posteriormente en la página de este espacio cultural.', 'futuretheme'); ?>
  </p>

  <hr style="margin: 22px 0;">

  <p style="color: #666;">
    <strong><?php esc_html_e('Indicaciones:', 'futuretheme'); ?></strong>
  </p>

  <ul style="list-style: disc; margin-left: 20px; color: #666;">
    <li><?php esc_html_e('Todos los campos son opcionales.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('El título de WordPress será el nombre del espacio cultural.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('El editor principal puede usarse para la descripción completa.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('El extracto puede usarse como descripción corta.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('La imagen destacada puede usarse como imagen principal o tarjeta.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('Las categorías sirven para agrupar espacios como Museo, Galerías, Cineclub o Salas.', 'futuretheme'); ?></li>
    <li><?php esc_html_e('El menú asociado permite mostrar una navegación específica para este espacio.', 'futuretheme'); ?></li>
  </ul>

<?php
}


/**
 * Guarda los campos personalizados.
 *
 * @param int $post_id ID del post.
 */
function futuretheme_save_espacio_cultural_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_espacio_cultural_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_espacio_cultural_nonce'])),
      'futuretheme_save_espacio_cultural_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (isset($_POST['post_type']) && 'espacio_cultural' === $_POST['post_type']) {
    if (! current_user_can('edit_post', $post_id)) {
      return;
    }
  }

  $fields = array(
    '_futuretheme_espacio_capacidad' => array(
      'input'    => 'futuretheme_espacio_capacidad',
      'sanitize' => 'text',
    ),
    '_futuretheme_espacio_horario' => array(
      'input'    => 'futuretheme_espacio_horario',
      'sanitize' => 'textarea',
    ),
    '_futuretheme_espacio_direccion' => array(
      'input'    => 'futuretheme_espacio_direccion',
      'sanitize' => 'textarea',
    ),
    '_futuretheme_espacio_maps' => array(
      'input'    => 'futuretheme_espacio_maps',
      'sanitize' => 'url',
    ),
    '_futuretheme_espacio_correo' => array(
      'input'    => 'futuretheme_espacio_correo',
      'sanitize' => 'email',
    ),
    '_futuretheme_espacio_telefono' => array(
      'input'    => 'futuretheme_espacio_telefono',
      'sanitize' => 'text',
    ),
    '_futuretheme_espacio_tarifa' => array(
      'input'    => 'futuretheme_espacio_tarifa',
      'sanitize' => 'textarea',
    ),
    '_futuretheme_espacio_menu_id' => array(
      'input'    => 'futuretheme_espacio_menu_id',
      'sanitize' => 'absint',
    ),
  );

  foreach ($fields as $meta_key => $field) {

    if (! isset($_POST[$field['input']])) {
      delete_post_meta($post_id, $meta_key);
      continue;
    }

    $raw_value = wp_unslash($_POST[$field['input']]);

    if ('url' === $field['sanitize']) {
      $value = esc_url_raw($raw_value);
    } elseif ('email' === $field['sanitize']) {
      $value = sanitize_email($raw_value);
    } elseif ('textarea' === $field['sanitize']) {
      $value = sanitize_textarea_field($raw_value);
    } elseif ('absint' === $field['sanitize']) {
      $value = absint($raw_value);
    } else {
      $value = sanitize_text_field($raw_value);
    }

    if ('' === $value || 0 === $value) {
      delete_post_meta($post_id, $meta_key);
    } else {
      update_post_meta($post_id, $meta_key, $value);
    }
  }
}
add_action('save_post_espacio_cultural', 'futuretheme_save_espacio_cultural_fields');



/* ============================================================
   EXPOSICIONES
   Post type para Artes Visuales y otras muestras culturales
   ============================================================ */

/**
 * Registrar post type Exposiciones.
 */
function futuretheme_register_exposiciones_post_type()
{

  $labels = array(
    'name'                  => __('Exposiciones', 'futuretheme'),
    'singular_name'         => __('Exposición', 'futuretheme'),
    'menu_name'             => __('Exposiciones', 'futuretheme'),
    'name_admin_bar'        => __('Exposición', 'futuretheme'),
    'add_new'               => __('Añadir nueva', 'futuretheme'),
    'add_new_item'          => __('Añadir nueva exposición', 'futuretheme'),
    'new_item'              => __('Nueva exposición', 'futuretheme'),
    'edit_item'             => __('Editar exposición', 'futuretheme'),
    'view_item'             => __('Ver exposición', 'futuretheme'),
    'all_items'             => __('Todas las exposiciones', 'futuretheme'),
    'search_items'          => __('Buscar exposiciones', 'futuretheme'),
    'not_found'             => __('No se encontraron exposiciones.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron exposiciones en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen destacada de la exposición', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen destacada', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen destacada', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen destacada', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'exposicion',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 23,
    'menu_icon'          => 'dashicons-format-gallery',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('exposicion', $args);
}
add_action('init', 'futuretheme_register_exposiciones_post_type');


/* ============================================================
   TAXONOMÍA: CATEGORÍAS DE EXPOSICIÓN
   Ejemplo: Artes Visuales, Fotografía, Pintura, Instalación
   ============================================================ */

function futuretheme_register_categoria_exposicion_taxonomy()
{

  $labels = array(
    'name'              => __('Categorías de Exposición', 'futuretheme'),
    'singular_name'     => __('Categoría de Exposición', 'futuretheme'),
    'search_items'      => __('Buscar categorías', 'futuretheme'),
    'all_items'         => __('Todas las categorías', 'futuretheme'),
    'parent_item'       => __('Categoría superior', 'futuretheme'),
    'parent_item_colon' => __('Categoría superior:', 'futuretheme'),
    'edit_item'         => __('Editar categoría', 'futuretheme'),
    'update_item'       => __('Actualizar categoría', 'futuretheme'),
    'add_new_item'      => __('Añadir nueva categoría', 'futuretheme'),
    'new_item_name'     => __('Nuevo nombre de categoría', 'futuretheme'),
    'menu_name'         => __('Categorías', 'futuretheme'),
  );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array(
      'slug'       => 'categoria-exposicion',
      'with_front' => false,
    ),
    'show_in_rest'      => true,
  );

  register_taxonomy('categoria_exposicion', array('exposicion'), $args);
}
add_action('init', 'futuretheme_register_categoria_exposicion_taxonomy');


/* ============================================================
   TAXONOMÍA: ARTISTAS
   Se comporta como etiquetas.
   Permite varios artistas por exposición.
   ============================================================ */

function futuretheme_register_artista_exposicion_taxonomy()
{

  $labels = array(
    'name'                       => __('Artistas', 'futuretheme'),
    'singular_name'              => __('Artista', 'futuretheme'),
    'search_items'               => __('Buscar artistas', 'futuretheme'),
    'popular_items'              => __('Artistas populares', 'futuretheme'),
    'all_items'                  => __('Todos los artistas', 'futuretheme'),
    'edit_item'                  => __('Editar artista', 'futuretheme'),
    'update_item'                => __('Actualizar artista', 'futuretheme'),
    'add_new_item'               => __('Añadir nuevo artista', 'futuretheme'),
    'new_item_name'              => __('Nuevo nombre de artista', 'futuretheme'),
    'separate_items_with_commas' => __('Separar artistas con comas', 'futuretheme'),
    'add_or_remove_items'        => __('Añadir o quitar artistas', 'futuretheme'),
    'choose_from_most_used'      => __('Elegir entre los más usados', 'futuretheme'),
    'not_found'                  => __('No se encontraron artistas.', 'futuretheme'),
    'menu_name'                  => __('Artistas', 'futuretheme'),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array(
      'slug'       => 'artista',
      'with_front' => false,
    ),
    'show_in_rest'          => true,
  );

  register_taxonomy('artista_exposicion', array('exposicion'), $args);
}
add_action('init', 'futuretheme_register_artista_exposicion_taxonomy');


/* ============================================================
   TAXONOMÍA: SALAS DE EXPOSICIÓN
   Se comporta como etiquetas.
   Permite una o varias salas por exposición.
   ============================================================ */

function futuretheme_register_sala_exposicion_taxonomy()
{

  $labels = array(
    'name'                       => __('Salas de Exposición', 'futuretheme'),
    'singular_name'              => __('Sala de Exposición', 'futuretheme'),
    'search_items'               => __('Buscar salas', 'futuretheme'),
    'popular_items'              => __('Salas populares', 'futuretheme'),
    'all_items'                  => __('Todas las salas', 'futuretheme'),
    'edit_item'                  => __('Editar sala', 'futuretheme'),
    'update_item'                => __('Actualizar sala', 'futuretheme'),
    'add_new_item'               => __('Añadir nueva sala', 'futuretheme'),
    'new_item_name'              => __('Nuevo nombre de sala', 'futuretheme'),
    'separate_items_with_commas' => __('Separar salas con comas', 'futuretheme'),
    'add_or_remove_items'        => __('Añadir o quitar salas', 'futuretheme'),
    'choose_from_most_used'      => __('Elegir entre las más usadas', 'futuretheme'),
    'not_found'                  => __('No se encontraron salas.', 'futuretheme'),
    'menu_name'                  => __('Salas', 'futuretheme'),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array(
      'slug'       => 'sala-exposicion',
      'with_front' => false,
    ),
    'show_in_rest'          => true,
  );

  register_taxonomy('sala_exposicion', array('exposicion'), $args);
}
add_action('init', 'futuretheme_register_sala_exposicion_taxonomy');


/* ============================================================
   TAXONOMÍA: TIPO / TÉCNICA DE EXPOSICIÓN
   Ejemplo: Pintura, Fotografía, Instalación, Escultura
   ============================================================ */

function futuretheme_register_tipo_exposicion_taxonomy()
{

  $labels = array(
    'name'                       => __('Tipos / Técnicas', 'futuretheme'),
    'singular_name'              => __('Tipo / Técnica', 'futuretheme'),
    'search_items'               => __('Buscar tipos o técnicas', 'futuretheme'),
    'popular_items'              => __('Tipos populares', 'futuretheme'),
    'all_items'                  => __('Todos los tipos', 'futuretheme'),
    'edit_item'                  => __('Editar tipo', 'futuretheme'),
    'update_item'                => __('Actualizar tipo', 'futuretheme'),
    'add_new_item'               => __('Añadir nuevo tipo', 'futuretheme'),
    'new_item_name'              => __('Nuevo nombre de tipo', 'futuretheme'),
    'separate_items_with_commas' => __('Separar tipos con comas', 'futuretheme'),
    'add_or_remove_items'        => __('Añadir o quitar tipos', 'futuretheme'),
    'choose_from_most_used'      => __('Elegir entre los más usados', 'futuretheme'),
    'not_found'                  => __('No se encontraron tipos.', 'futuretheme'),
    'menu_name'                  => __('Tipos / Técnicas', 'futuretheme'),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array(
      'slug'       => 'tipo-exposicion',
      'with_front' => false,
    ),
    'show_in_rest'          => true,
  );

  register_taxonomy('tipo_exposicion', array('exposicion'), $args);
}
add_action('init', 'futuretheme_register_tipo_exposicion_taxonomy');


/* ============================================================
   CREAR TÉRMINOS BASE PARA EXPOSICIONES
   ============================================================ */

function futuretheme_create_default_exposicion_terms()
{

  $categorias = array(
    'Artes Visuales' => 'artes-visuales',
    'Fotografía'    => 'fotografia',
    'Pintura'       => 'pintura',
    'Instalación'   => 'instalacion',
    'Escultura'     => 'escultura',
  );

  foreach ($categorias as $name => $slug) {
    if (! term_exists($slug, 'categoria_exposicion')) {
      wp_insert_term(
        $name,
        'categoria_exposicion',
        array(
          'slug' => $slug,
        )
      );
    }
  }

  $tipos = array(
    'Pintura'       => 'pintura',
    'Fotografía'    => 'fotografia',
    'Instalación'   => 'instalacion',
    'Escultura'     => 'escultura',
    'Arte digital'  => 'arte-digital',
    'Ingreso libre' => 'ingreso-libre',
  );

  foreach ($tipos as $name => $slug) {
    if (! term_exists($slug, 'tipo_exposicion')) {
      wp_insert_term(
        $name,
        'tipo_exposicion',
        array(
          'slug' => $slug,
        )
      );
    }
  }

  $salas = array(
    'Sala I'           => 'sala-i',
    'Sala II'          => 'sala-ii',
    'Sala III'         => 'sala-iii',
    'Casona Irriberry' => 'casona-irreiberry',
  );

  foreach ($salas as $name => $slug) {
    if (! term_exists($slug, 'sala_exposicion')) {
      wp_insert_term(
        $name,
        'sala_exposicion',
        array(
          'slug' => $slug,
        )
      );
    }
  }
}
add_action('init', 'futuretheme_create_default_exposicion_terms', 20);


/* ============================================================
   CAMPOS PERSONALIZADOS DE EXPOSICIÓN
   ============================================================ */

function futuretheme_add_exposicion_meta_box()
{

  add_meta_box(
    'futuretheme_exposicion_fields',
    __('Datos de la exposición', 'futuretheme'),
    'futuretheme_render_exposicion_meta_box',
    'exposicion',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_exposicion_meta_box');


/**
 * Renderizar campos personalizados de Exposición.
 *
 * @param WP_Post $post Post actual.
 */
function futuretheme_render_exposicion_meta_box($post)
{

  wp_nonce_field('futuretheme_save_exposicion_fields', 'futuretheme_exposicion_nonce');

  $mes            = get_post_meta($post->ID, '_futuretheme_expo_mes', true);
  $anio           = get_post_meta($post->ID, '_futuretheme_expo_anio', true);
  $fecha_inicio   = get_post_meta($post->ID, '_futuretheme_expo_fecha_inicio', true);
  $fecha_fin      = get_post_meta($post->ID, '_futuretheme_expo_fecha_fin', true);
  $ingreso        = get_post_meta($post->ID, '_futuretheme_expo_ingreso', true);
  $catalogo_url   = get_post_meta($post->ID, '_futuretheme_expo_catalogo_url', true);
  $entrevista_url = get_post_meta($post->ID, '_futuretheme_expo_entrevista_url', true);

  $meses = array(
    ''          => __('Seleccionar mes', 'futuretheme'),
    'enero'     => __('Enero', 'futuretheme'),
    'febrero'   => __('Febrero', 'futuretheme'),
    'marzo'     => __('Marzo', 'futuretheme'),
    'abril'     => __('Abril', 'futuretheme'),
    'mayo'      => __('Mayo', 'futuretheme'),
    'junio'     => __('Junio', 'futuretheme'),
    'julio'     => __('Julio', 'futuretheme'),
    'agosto'    => __('Agosto', 'futuretheme'),
    'septiembre' => __('Septiembre', 'futuretheme'),
    'octubre'   => __('Octubre', 'futuretheme'),
    'noviembre' => __('Noviembre', 'futuretheme'),
    'diciembre' => __('Diciembre', 'futuretheme'),
  );
?>

  <style>
    .futuretheme-meta-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px 22px;
    }

    .futuretheme-meta-field {
      display: block;
    }

    .futuretheme-meta-field.full {
      grid-column: 1 / -1;
    }

    .futuretheme-meta-field label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .futuretheme-meta-field input,
    .futuretheme-meta-field select {
      width: 100%;
      max-width: 100%;
    }

    .futuretheme-meta-help {
      display: block;
      margin-top: 5px;
      color: #646970;
      font-size: 12px;
    }

    @media (max-width: 782px) {
      .futuretheme-meta-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="futuretheme-meta-grid">

    <div class="futuretheme-meta-field">
      <label for="futuretheme_expo_mes">
        <?php esc_html_e('Mes de exposición', 'futuretheme'); ?>
      </label>

      <select id="futuretheme_expo_mes" name="futuretheme_expo_mes">
        <?php foreach ($meses as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($mes, $value); ?>>
            <?php echo esc_html($label); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Este dato se usará para filtrar exposiciones por mes.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_expo_anio">
        <?php esc_html_e('Año de exposición', 'futuretheme'); ?>
      </label>

      <input
        type="number"
        id="futuretheme_expo_anio"
        name="futuretheme_expo_anio"
        value="<?php echo esc_attr($anio); ?>"
        min="2000"
        max="2100"
        step="1"
        placeholder="2026">

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Este dato se usará para filtrar exposiciones por año.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_expo_fecha_inicio">
        <?php esc_html_e('Fecha de inicio', 'futuretheme'); ?>
      </label>

      <input
        type="date"
        id="futuretheme_expo_fecha_inicio"
        name="futuretheme_expo_fecha_inicio"
        value="<?php echo esc_attr($fecha_inicio); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_expo_fecha_fin">
        <?php esc_html_e('Fecha de fin', 'futuretheme'); ?>
      </label>

      <input
        type="date"
        id="futuretheme_expo_fecha_fin"
        name="futuretheme_expo_fecha_fin"
        value="<?php echo esc_attr($fecha_fin); ?>">
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_expo_ingreso">
        <?php esc_html_e('Ingreso / tarifa', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_expo_ingreso"
        name="futuretheme_expo_ingreso"
        value="<?php echo esc_attr($ingreso); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Ingreso libre / Adultos S/.5 / Estudiantes S/.2', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_expo_catalogo_url">
        <?php esc_html_e('Enlace catálogo virtual', 'futuretheme'); ?>
      </label>

      <input
        type="url"
        id="futuretheme_expo_catalogo_url"
        name="futuretheme_expo_catalogo_url"
        value="<?php echo esc_url($catalogo_url); ?>"
        placeholder="https://...">

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Se mostrará como botón: Ver catálogo virtual.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_expo_entrevista_url">
        <?php esc_html_e('Enlace entrevista / video', 'futuretheme'); ?>
      </label>

      <input
        type="url"
        id="futuretheme_expo_entrevista_url"
        name="futuretheme_expo_entrevista_url"
        value="<?php echo esc_url($entrevista_url); ?>"
        placeholder="https://www.youtube.com/...">

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Se mostrará como botón: Ver entrevista. Puede ser enlace a YouTube u otra plataforma.', 'futuretheme'); ?>
      </span>
    </div>

  </div>

<?php
}


/**
 * Guardar campos personalizados de Exposición.
 *
 * @param int $post_id ID del post.
 */
function futuretheme_save_exposicion_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_exposicion_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_exposicion_nonce'])),
      'futuretheme_save_exposicion_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (isset($_POST['post_type']) && 'exposicion' === $_POST['post_type']) {
    if (! current_user_can('edit_post', $post_id)) {
      return;
    }
  }

  $allowed_months = array(
    '',
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre',
  );

  $mes = isset($_POST['futuretheme_expo_mes'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_expo_mes']))
    : '';

  if (! in_array($mes, $allowed_months, true)) {
    $mes = '';
  }

  $anio = isset($_POST['futuretheme_expo_anio'])
    ? absint($_POST['futuretheme_expo_anio'])
    : '';

  if ($anio < 2000 || $anio > 2100) {
    $anio = '';
  }

  $fecha_inicio = isset($_POST['futuretheme_expo_fecha_inicio'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_expo_fecha_inicio']))
    : '';

  $fecha_fin = isset($_POST['futuretheme_expo_fecha_fin'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_expo_fecha_fin']))
    : '';

  $ingreso = isset($_POST['futuretheme_expo_ingreso'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_expo_ingreso']))
    : '';

  $catalogo_url = isset($_POST['futuretheme_expo_catalogo_url'])
    ? esc_url_raw(wp_unslash($_POST['futuretheme_expo_catalogo_url']))
    : '';

  $entrevista_url = isset($_POST['futuretheme_expo_entrevista_url'])
    ? esc_url_raw(wp_unslash($_POST['futuretheme_expo_entrevista_url']))
    : '';

  update_post_meta($post_id, '_futuretheme_expo_mes', $mes);
  update_post_meta($post_id, '_futuretheme_expo_anio', $anio);
  update_post_meta($post_id, '_futuretheme_expo_fecha_inicio', $fecha_inicio);
  update_post_meta($post_id, '_futuretheme_expo_fecha_fin', $fecha_fin);
  update_post_meta($post_id, '_futuretheme_expo_ingreso', $ingreso);
  update_post_meta($post_id, '_futuretheme_expo_catalogo_url', $catalogo_url);
  update_post_meta($post_id, '_futuretheme_expo_entrevista_url', $entrevista_url);
}
add_action('save_post_exposicion', 'futuretheme_save_exposicion_fields');


/* ============================================================
   HELPER: CONVERTIR URL DE YOUTUBE A EMBED
   ============================================================ */

if (! function_exists('futuretheme_get_youtube_embed_url')) {

  function futuretheme_get_youtube_embed_url($url)
  {

    if (empty($url)) {
      return '';
    }

    $url = trim($url);

    $video_id = '';

    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
      $video_id = $matches[1];
    }

    if (empty($video_id) && preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
      $video_id = $matches[1];
    }

    if (empty($video_id) && preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
      $video_id = $matches[1];
    }

    if (empty($video_id) && preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
      $video_id = $matches[1];
    }

    if (empty($video_id)) {
      return '';
    }

    return esc_url_raw('https://www.youtube.com/embed/' . $video_id);
  }
}

/* ============================================================
   ARTES VISUALES: WIDGET COMUNIDAD + ESPACIO ASOCIADO
   ============================================================ */

/**
 * Registrar zona de widgets para la página Artes Visuales.
 */
function futuretheme_register_artes_visuales_widget_area()
{

  register_sidebar(
    array(
      'name'          => __('Artes Visuales - Comunidad', 'futuretheme'),
      'id'            => 'artes-visuales-comunidad',
      'description'   => __('Zona para insertar el bloque HTML de comunidad artística en la página Artes Visuales.', 'futuretheme'),
      'before_widget' => '',
      'after_widget'  => '',
      'before_title'  => '',
      'after_title'   => '',
    )
  );
}
add_action('widgets_init', 'futuretheme_register_artes_visuales_widget_area');


/**
 * Agregar selector de Espacio Cultural en páginas.
 */
function futuretheme_add_artes_visuales_page_meta_box()
{

  add_meta_box(
    'futuretheme_artes_visuales_settings',
    __('Configuración Artes Visuales', 'futuretheme'),
    'futuretheme_render_artes_visuales_page_meta_box',
    'page',
    'side',
    'default'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_artes_visuales_page_meta_box');


/**
 * Renderizar selector de Espacio Cultural.
 *
 * @param WP_Post $post Post actual.
 */
function futuretheme_render_artes_visuales_page_meta_box($post)
{

  wp_nonce_field('futuretheme_save_artes_visuales_page_settings', 'futuretheme_artes_visuales_nonce');

  $selected_espacio_id = get_post_meta($post->ID, '_futuretheme_artes_visuales_espacio_id', true);

  $espacios = get_posts(
    array(
      'post_type'      => 'espacio_cultural',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );
?>

  <p>
    <label for="futuretheme_artes_visuales_espacio_id">
      <strong><?php esc_html_e('Datos de contacto a mostrar', 'futuretheme'); ?></strong>
    </label>
  </p>

  <select
    id="futuretheme_artes_visuales_espacio_id"
    name="futuretheme_artes_visuales_espacio_id"
    style="width:100%;">
    <option value="">
      <?php esc_html_e('Seleccionar espacio cultural', 'futuretheme'); ?>
    </option>

    <?php foreach ($espacios as $espacio) : ?>
      <option
        value="<?php echo esc_attr($espacio->ID); ?>"
        <?php selected(absint($selected_espacio_id), absint($espacio->ID)); ?>>
        <?php echo esc_html(get_the_title($espacio->ID)); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <p style="color:#646970;font-size:12px;">
    <?php esc_html_e('Se usará para mostrar horario, ubicación y correo debajo del bloque de comunidad.', 'futuretheme'); ?>
  </p>

<?php
}


/**
 * Guardar configuración de Artes Visuales.
 *
 * @param int $post_id ID del post.
 */
function futuretheme_save_artes_visuales_page_settings($post_id)
{

  if (
    ! isset($_POST['futuretheme_artes_visuales_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_artes_visuales_nonce'])),
      'futuretheme_save_artes_visuales_page_settings'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $espacio_id = isset($_POST['futuretheme_artes_visuales_espacio_id'])
    ? absint($_POST['futuretheme_artes_visuales_espacio_id'])
    : 0;

  if ($espacio_id > 0) {
    update_post_meta($post_id, '_futuretheme_artes_visuales_espacio_id', $espacio_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_artes_visuales_espacio_id');
  }
}
add_action('save_post_page', 'futuretheme_save_artes_visuales_page_settings');



/* ============================================================
   CINECLUB
   Ciclos de Cine + Proyecciones
   ============================================================ */

/* ------------------------------------------------------------
   POST TYPE: CICLOS DE CINE
   ------------------------------------------------------------ */

function futuretheme_register_ciclo_cine_post_type()
{

  $labels = array(
    'name'                  => __('Ciclos de Cine', 'futuretheme'),
    'singular_name'         => __('Ciclo de Cine', 'futuretheme'),
    'menu_name'             => __('Ciclos de Cine', 'futuretheme'),
    'name_admin_bar'        => __('Ciclo de Cine', 'futuretheme'),
    'add_new'               => __('Añadir nuevo', 'futuretheme'),
    'add_new_item'          => __('Añadir nuevo ciclo', 'futuretheme'),
    'new_item'              => __('Nuevo ciclo', 'futuretheme'),
    'edit_item'             => __('Editar ciclo', 'futuretheme'),
    'view_item'             => __('Ver ciclo', 'futuretheme'),
    'all_items'             => __('Todos los ciclos', 'futuretheme'),
    'search_items'          => __('Buscar ciclos', 'futuretheme'),
    'not_found'             => __('No se encontraron ciclos.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron ciclos en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen del ciclo', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen del ciclo', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen del ciclo', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen del ciclo', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'ciclo-cine',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 24,
    'menu_icon'          => 'dashicons-video-alt2',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('ciclo_cine', $args);
}
add_action('init', 'futuretheme_register_ciclo_cine_post_type');


/* ------------------------------------------------------------
   POST TYPE: PROYECCIONES
   ------------------------------------------------------------ */

function futuretheme_register_proyeccion_cine_post_type()
{

  $labels = array(
    'name'                  => __('Proyecciones', 'futuretheme'),
    'singular_name'         => __('Proyección', 'futuretheme'),
    'menu_name'             => __('Proyecciones', 'futuretheme'),
    'name_admin_bar'        => __('Proyección', 'futuretheme'),
    'add_new'               => __('Añadir nueva', 'futuretheme'),
    'add_new_item'          => __('Añadir nueva proyección', 'futuretheme'),
    'new_item'              => __('Nueva proyección', 'futuretheme'),
    'edit_item'             => __('Editar proyección', 'futuretheme'),
    'view_item'             => __('Ver proyección', 'futuretheme'),
    'all_items'             => __('Todas las proyecciones', 'futuretheme'),
    'search_items'          => __('Buscar proyecciones', 'futuretheme'),
    'not_found'             => __('No se encontraron proyecciones.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron proyecciones en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen de la proyección', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen de la proyección', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen de la proyección', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen de la proyección', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'proyeccion',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 25,
    'menu_icon'          => 'dashicons-format-video',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('proyeccion_cine', $args);
}
add_action('init', 'futuretheme_register_proyeccion_cine_post_type');


/* ============================================================
   CAMPOS PERSONALIZADOS: CICLO DE CINE
   ============================================================ */

function futuretheme_add_ciclo_cine_meta_box()
{

  add_meta_box(
    'futuretheme_ciclo_cine_fields',
    __('Datos del ciclo de cine', 'futuretheme'),
    'futuretheme_render_ciclo_cine_meta_box',
    'ciclo_cine',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_ciclo_cine_meta_box');


function futuretheme_render_ciclo_cine_meta_box($post)
{

  wp_nonce_field('futuretheme_save_ciclo_cine_fields', 'futuretheme_ciclo_cine_nonce');

  $fecha_inicio = get_post_meta($post->ID, '_futuretheme_ciclo_fecha_inicio', true);
  $fecha_fin    = get_post_meta($post->ID, '_futuretheme_ciclo_fecha_fin', true);
  $bajada       = get_post_meta($post->ID, '_futuretheme_ciclo_bajada', true);
  $horario      = get_post_meta($post->ID, '_futuretheme_ciclo_horario', true);
  $ingreso      = get_post_meta($post->ID, '_futuretheme_ciclo_ingreso', true);
  $sala         = get_post_meta($post->ID, '_futuretheme_ciclo_sala', true);
?>

  <style>
    .futuretheme-meta-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px 22px;
    }

    .futuretheme-meta-field.full {
      grid-column: 1 / -1;
    }

    .futuretheme-meta-field label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .futuretheme-meta-field input,
    .futuretheme-meta-field textarea,
    .futuretheme-meta-field select {
      width: 100%;
      max-width: 100%;
    }

    .futuretheme-meta-help {
      display: block;
      margin-top: 5px;
      color: #646970;
      font-size: 12px;
    }

    @media (max-width: 782px) {
      .futuretheme-meta-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="futuretheme-meta-grid">

    <div class="futuretheme-meta-field">
      <label for="futuretheme_ciclo_fecha_inicio">
        <?php esc_html_e('Fecha de inicio del ciclo', 'futuretheme'); ?>
      </label>
      <input
        type="date"
        id="futuretheme_ciclo_fecha_inicio"
        name="futuretheme_ciclo_fecha_inicio"
        value="<?php echo esc_attr($fecha_inicio); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_ciclo_fecha_fin">
        <?php esc_html_e('Fecha de fin del ciclo', 'futuretheme'); ?>
      </label>
      <input
        type="date"
        id="futuretheme_ciclo_fecha_fin"
        name="futuretheme_ciclo_fecha_fin"
        value="<?php echo esc_attr($fecha_fin); ?>">
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_ciclo_bajada">
        <?php esc_html_e('Bajada / subtítulo del ciclo', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_ciclo_bajada"
        name="futuretheme_ciclo_bajada"
        value="<?php echo esc_attr($bajada); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Clásicos para obsesivos compulsivos', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_ciclo_sala">
        <?php esc_html_e('Sala', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_ciclo_sala"
        name="futuretheme_ciclo_sala"
        value="<?php echo esc_attr($sala); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Sala Audiovisuales', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_ciclo_ingreso">
        <?php esc_html_e('Ingreso', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_ciclo_ingreso"
        name="futuretheme_ciclo_ingreso"
        value="<?php echo esc_attr($ingreso); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Ingreso libre', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_ciclo_horario">
        <?php esc_html_e('Horario general', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_ciclo_horario"
        name="futuretheme_ciclo_horario"
        value="<?php echo esc_attr($horario); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: 18:30 hrs · Función diaria', 'futuretheme'); ?>">
      <span class="futuretheme-meta-help">
        <?php esc_html_e('Se mostrará debajo de la descripción del ciclo.', 'futuretheme'); ?>
      </span>
    </div>

  </div>

<?php
}


function futuretheme_save_ciclo_cine_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_ciclo_cine_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_ciclo_cine_nonce'])),
      'futuretheme_save_ciclo_cine_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $fields = array(
    '_futuretheme_ciclo_fecha_inicio' => 'futuretheme_ciclo_fecha_inicio',
    '_futuretheme_ciclo_fecha_fin'    => 'futuretheme_ciclo_fecha_fin',
    '_futuretheme_ciclo_bajada'       => 'futuretheme_ciclo_bajada',
    '_futuretheme_ciclo_horario'      => 'futuretheme_ciclo_horario',
    '_futuretheme_ciclo_ingreso'      => 'futuretheme_ciclo_ingreso',
    '_futuretheme_ciclo_sala'         => 'futuretheme_ciclo_sala',
  );

  foreach ($fields as $meta_key => $field_name) {
    $value = isset($_POST[$field_name])
      ? sanitize_text_field(wp_unslash($_POST[$field_name]))
      : '';

    if ('' !== $value) {
      update_post_meta($post_id, $meta_key, $value);
    } else {
      delete_post_meta($post_id, $meta_key);
    }
  }
}
add_action('save_post_ciclo_cine', 'futuretheme_save_ciclo_cine_fields');


/* ============================================================
   CAMPOS PERSONALIZADOS: PROYECCIÓN
   ============================================================ */

function futuretheme_add_proyeccion_cine_meta_box()
{

  add_meta_box(
    'futuretheme_proyeccion_cine_fields',
    __('Datos de la proyección', 'futuretheme'),
    'futuretheme_render_proyeccion_cine_meta_box',
    'proyeccion_cine',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_proyeccion_cine_meta_box');


function futuretheme_render_proyeccion_cine_meta_box($post)
{

  wp_nonce_field('futuretheme_save_proyeccion_cine_fields', 'futuretheme_proyeccion_cine_nonce');

  $director  = get_post_meta($post->ID, '_futuretheme_proyeccion_director', true);
  $fecha     = get_post_meta($post->ID, '_futuretheme_proyeccion_fecha', true);
  $hora      = get_post_meta($post->ID, '_futuretheme_proyeccion_hora', true);
  $ingreso   = get_post_meta($post->ID, '_futuretheme_proyeccion_ingreso', true);
  $ciclo_id  = get_post_meta($post->ID, '_futuretheme_proyeccion_ciclo_id', true);

  $ciclos = get_posts(
    array(
      'post_type'      => 'ciclo_cine',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );
?>

  <div class="futuretheme-meta-grid">

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_proyeccion_ciclo_id">
        <?php esc_html_e('Ciclo asociado', 'futuretheme'); ?>
      </label>

      <select id="futuretheme_proyeccion_ciclo_id" name="futuretheme_proyeccion_ciclo_id">
        <option value="">
          <?php esc_html_e('Seleccionar ciclo', 'futuretheme'); ?>
        </option>

        <?php foreach ($ciclos as $ciclo) : ?>
          <option
            value="<?php echo esc_attr($ciclo->ID); ?>"
            <?php selected(absint($ciclo_id), absint($ciclo->ID)); ?>>
            <?php echo esc_html(get_the_title($ciclo->ID)); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Permite agrupar esta proyección dentro de un ciclo de cine.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_proyeccion_director">
        <?php esc_html_e('Director', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_proyeccion_director"
        name="futuretheme_proyeccion_director"
        value="<?php echo esc_attr($director); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Alfred Hitchcock', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_proyeccion_ingreso">
        <?php esc_html_e('Ingreso / costo', 'futuretheme'); ?>
      </label>
      <input
        type="text"
        id="futuretheme_proyeccion_ingreso"
        name="futuretheme_proyeccion_ingreso"
        value="<?php echo esc_attr($ingreso); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Ingreso libre', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_proyeccion_fecha">
        <?php esc_html_e('Fecha de proyección', 'futuretheme'); ?>
      </label>
      <input
        type="date"
        id="futuretheme_proyeccion_fecha"
        name="futuretheme_proyeccion_fecha"
        value="<?php echo esc_attr($fecha); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_proyeccion_hora">
        <?php esc_html_e('Hora de proyección', 'futuretheme'); ?>
      </label>
      <input
        type="time"
        id="futuretheme_proyeccion_hora"
        name="futuretheme_proyeccion_hora"
        value="<?php echo esc_attr($hora); ?>">
    </div>

  </div>

  <p class="futuretheme-meta-help" style="margin-top:14px;">
    <?php esc_html_e('El resumen breve puede colocarse en el campo Extracto de la proyección.', 'futuretheme'); ?>
  </p>

<?php
}


function futuretheme_save_proyeccion_cine_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_proyeccion_cine_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_proyeccion_cine_nonce'])),
      'futuretheme_save_proyeccion_cine_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $ciclo_id = isset($_POST['futuretheme_proyeccion_ciclo_id'])
    ? absint($_POST['futuretheme_proyeccion_ciclo_id'])
    : 0;

  if ($ciclo_id > 0) {
    update_post_meta($post_id, '_futuretheme_proyeccion_ciclo_id', $ciclo_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_proyeccion_ciclo_id');
  }

  $fields = array(
    '_futuretheme_proyeccion_director' => 'futuretheme_proyeccion_director',
    '_futuretheme_proyeccion_fecha'    => 'futuretheme_proyeccion_fecha',
    '_futuretheme_proyeccion_hora'     => 'futuretheme_proyeccion_hora',
    '_futuretheme_proyeccion_ingreso'  => 'futuretheme_proyeccion_ingreso',
  );

  foreach ($fields as $meta_key => $field_name) {
    $value = isset($_POST[$field_name])
      ? sanitize_text_field(wp_unslash($_POST[$field_name]))
      : '';

    if ('' !== $value) {
      update_post_meta($post_id, $meta_key, $value);
    } else {
      delete_post_meta($post_id, $meta_key);
    }
  }
}
add_action('save_post_proyeccion_cine', 'futuretheme_save_proyeccion_cine_fields');


/* ============================================================
   CINECLUB: CONFIGURACIÓN EN PÁGINA
   Selector de Ciclo destacado + Espacio Cultural
   ============================================================ */

function futuretheme_add_cineclub_page_meta_box()
{

  add_meta_box(
    'futuretheme_cineclub_settings',
    __('Configuración Cineclub', 'futuretheme'),
    'futuretheme_render_cineclub_page_meta_box',
    'page',
    'side',
    'default'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_cineclub_page_meta_box');


function futuretheme_render_cineclub_page_meta_box($post)
{

  wp_nonce_field('futuretheme_save_cineclub_page_settings', 'futuretheme_cineclub_nonce');

  $selected_ciclo_id   = get_post_meta($post->ID, '_futuretheme_cineclub_ciclo_id', true);
  $selected_espacio_id = get_post_meta($post->ID, '_futuretheme_cineclub_espacio_id', true);

  $ciclos = get_posts(
    array(
      'post_type'      => 'ciclo_cine',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );

  $espacios = get_posts(
    array(
      'post_type'      => 'espacio_cultural',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );
?>

  <p>
    <label for="futuretheme_cineclub_ciclo_id">
      <strong><?php esc_html_e('Ciclo destacado', 'futuretheme'); ?></strong>
    </label>
  </p>

  <select
    id="futuretheme_cineclub_ciclo_id"
    name="futuretheme_cineclub_ciclo_id"
    style="width:100%;">
    <option value="">
      <?php esc_html_e('Seleccionar ciclo', 'futuretheme'); ?>
    </option>

    <?php foreach ($ciclos as $ciclo) : ?>
      <option
        value="<?php echo esc_attr($ciclo->ID); ?>"
        <?php selected(absint($selected_ciclo_id), absint($ciclo->ID)); ?>>
        <?php echo esc_html(get_the_title($ciclo->ID)); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <p style="margin-top:16px;">
    <label for="futuretheme_cineclub_espacio_id">
      <strong><?php esc_html_e('Espacio Cultural asociado', 'futuretheme'); ?></strong>
    </label>
  </p>

  <select
    id="futuretheme_cineclub_espacio_id"
    name="futuretheme_cineclub_espacio_id"
    style="width:100%;">
    <option value="">
      <?php esc_html_e('Seleccionar espacio cultural', 'futuretheme'); ?>
    </option>

    <?php foreach ($espacios as $espacio) : ?>
      <option
        value="<?php echo esc_attr($espacio->ID); ?>"
        <?php selected(absint($selected_espacio_id), absint($espacio->ID)); ?>>
        <?php echo esc_html(get_the_title($espacio->ID)); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <p style="color:#646970;font-size:12px;">
    <?php esc_html_e('El espacio cultural se usará para mostrar horario, ubicación, correo y mapa del Cineclub.', 'futuretheme'); ?>
  </p>

<?php
}


function futuretheme_save_cineclub_page_settings($post_id)
{

  if (
    ! isset($_POST['futuretheme_cineclub_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_cineclub_nonce'])),
      'futuretheme_save_cineclub_page_settings'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $ciclo_id = isset($_POST['futuretheme_cineclub_ciclo_id'])
    ? absint($_POST['futuretheme_cineclub_ciclo_id'])
    : 0;

  $espacio_id = isset($_POST['futuretheme_cineclub_espacio_id'])
    ? absint($_POST['futuretheme_cineclub_espacio_id'])
    : 0;

  if ($ciclo_id > 0) {
    update_post_meta($post_id, '_futuretheme_cineclub_ciclo_id', $ciclo_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_cineclub_ciclo_id');
  }

  if ($espacio_id > 0) {
    update_post_meta($post_id, '_futuretheme_cineclub_espacio_id', $espacio_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_cineclub_espacio_id');
  }
}
add_action('save_post_page', 'futuretheme_save_cineclub_page_settings');


/* ============================================================
   CINECLUB: ZONA DE WIDGET PARA BANNER INFERIOR
   ============================================================ */

function futuretheme_register_cineclub_widget_area()
{

  register_sidebar(
    array(
      'name'          => __('Cineclub - Banner inferior', 'futuretheme'),
      'id'            => 'cineclub-banner-inferior',
      'description'   => __('Zona para insertar el banner inferior de la página Cineclub.', 'futuretheme'),
      'before_widget' => '',
      'after_widget'  => '',
      'before_title'  => '',
      'after_title'   => '',
    )
  );
}
add_action('widgets_init', 'futuretheme_register_cineclub_widget_area');


/* ============================================================
   MUSEO
   Programas del Museo + Piezas del Museo
   ============================================================ */

/* ------------------------------------------------------------
   POST TYPE: PROGRAMAS DEL MUSEO
   Ejemplo: Pieza del Mes · Mayo 2026
   ------------------------------------------------------------ */

function futuretheme_register_programa_museo_post_type()
{

  $labels = array(
    'name'                  => __('Programas del Museo', 'futuretheme'),
    'singular_name'         => __('Programa del Museo', 'futuretheme'),
    'menu_name'             => __('Programas del Museo', 'futuretheme'),
    'name_admin_bar'        => __('Programa del Museo', 'futuretheme'),
    'add_new'               => __('Añadir nuevo', 'futuretheme'),
    'add_new_item'          => __('Añadir nuevo programa', 'futuretheme'),
    'new_item'              => __('Nuevo programa', 'futuretheme'),
    'edit_item'             => __('Editar programa', 'futuretheme'),
    'view_item'             => __('Ver programa', 'futuretheme'),
    'all_items'             => __('Todos los programas', 'futuretheme'),
    'search_items'          => __('Buscar programas', 'futuretheme'),
    'not_found'             => __('No se encontraron programas.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron programas en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen del programa', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen del programa', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen del programa', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen del programa', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'programa-museo',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 26,
    'menu_icon'          => 'dashicons-calendar-alt',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('programa_museo', $args);
}
add_action('init', 'futuretheme_register_programa_museo_post_type');


/* ------------------------------------------------------------
   POST TYPE: PIEZAS DEL MUSEO
   Ejemplo: Huaco inca, Cerámica ceremonial, Textil, etc.
   ------------------------------------------------------------ */

function futuretheme_register_pieza_museo_post_type()
{

  $labels = array(
    'name'                  => __('Piezas del Museo', 'futuretheme'),
    'singular_name'         => __('Pieza del Museo', 'futuretheme'),
    'menu_name'             => __('Piezas del Museo', 'futuretheme'),
    'name_admin_bar'        => __('Pieza del Museo', 'futuretheme'),
    'add_new'               => __('Añadir nueva', 'futuretheme'),
    'add_new_item'          => __('Añadir nueva pieza', 'futuretheme'),
    'new_item'              => __('Nueva pieza', 'futuretheme'),
    'edit_item'             => __('Editar pieza', 'futuretheme'),
    'view_item'             => __('Ver pieza', 'futuretheme'),
    'all_items'             => __('Todas las piezas', 'futuretheme'),
    'search_items'          => __('Buscar piezas', 'futuretheme'),
    'not_found'             => __('No se encontraron piezas.', 'futuretheme'),
    'not_found_in_trash'    => __('No se encontraron piezas en la papelera.', 'futuretheme'),
    'featured_image'        => __('Imagen de la pieza', 'futuretheme'),
    'set_featured_image'    => __('Asignar imagen de la pieza', 'futuretheme'),
    'remove_featured_image' => __('Quitar imagen de la pieza', 'futuretheme'),
    'use_featured_image'    => __('Usar como imagen de la pieza', 'futuretheme'),
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array(
      'slug'       => 'pieza-museo',
      'with_front' => false,
    ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 27,
    'menu_icon'          => 'dashicons-art',
    'supports'           => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'page-attributes',
      'revisions',
    ),
    'show_in_rest'       => true,
  );

  register_post_type('pieza_museo', $args);
}
add_action('init', 'futuretheme_register_pieza_museo_post_type');


/* ============================================================
   CAMPOS PERSONALIZADOS: PROGRAMA MUSEO
   ============================================================ */

function futuretheme_add_programa_museo_meta_box()
{

  add_meta_box(
    'futuretheme_programa_museo_fields',
    __('Datos del programa del museo', 'futuretheme'),
    'futuretheme_render_programa_museo_meta_box',
    'programa_museo',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_programa_museo_meta_box');


function futuretheme_render_programa_museo_meta_box($post)
{

  wp_nonce_field('futuretheme_save_programa_museo_fields', 'futuretheme_programa_museo_nonce');

  $mes            = get_post_meta($post->ID, '_futuretheme_programa_museo_mes', true);
  $anio           = get_post_meta($post->ID, '_futuretheme_programa_museo_anio', true);
  $pieza_id       = get_post_meta($post->ID, '_futuretheme_programa_museo_pieza_id', true);
  $texto_llamada  = get_post_meta($post->ID, '_futuretheme_programa_museo_texto_llamada', true);

  $piezas = get_posts(
    array(
      'post_type'      => 'pieza_museo',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );

  $meses = array(
    ''           => __('Seleccionar mes', 'futuretheme'),
    'enero'      => __('Enero', 'futuretheme'),
    'febrero'    => __('Febrero', 'futuretheme'),
    'marzo'      => __('Marzo', 'futuretheme'),
    'abril'      => __('Abril', 'futuretheme'),
    'mayo'       => __('Mayo', 'futuretheme'),
    'junio'      => __('Junio', 'futuretheme'),
    'julio'      => __('Julio', 'futuretheme'),
    'agosto'     => __('Agosto', 'futuretheme'),
    'septiembre' => __('Septiembre', 'futuretheme'),
    'octubre'    => __('Octubre', 'futuretheme'),
    'noviembre'  => __('Noviembre', 'futuretheme'),
    'diciembre'  => __('Diciembre', 'futuretheme'),
  );
?>

  <style>
    .futuretheme-meta-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px 22px;
    }

    .futuretheme-meta-field.full {
      grid-column: 1 / -1;
    }

    .futuretheme-meta-field label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .futuretheme-meta-field input,
    .futuretheme-meta-field textarea,
    .futuretheme-meta-field select {
      width: 100%;
      max-width: 100%;
    }

    .futuretheme-meta-help {
      display: block;
      margin-top: 5px;
      color: #646970;
      font-size: 12px;
    }

    @media (max-width: 782px) {
      .futuretheme-meta-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="futuretheme-meta-grid">

    <div class="futuretheme-meta-field">
      <label for="futuretheme_programa_museo_mes">
        <?php esc_html_e('Mes', 'futuretheme'); ?>
      </label>

      <select id="futuretheme_programa_museo_mes" name="futuretheme_programa_museo_mes">
        <?php foreach ($meses as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($mes, $value); ?>>
            <?php echo esc_html($label); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_programa_museo_anio">
        <?php esc_html_e('Año', 'futuretheme'); ?>
      </label>

      <input
        type="number"
        id="futuretheme_programa_museo_anio"
        name="futuretheme_programa_museo_anio"
        value="<?php echo esc_attr($anio); ?>"
        min="2000"
        max="2100"
        step="1"
        placeholder="<?php esc_attr_e('Ejemplo: 2026', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_programa_museo_pieza_id">
        <?php esc_html_e('Pieza destacada', 'futuretheme'); ?>
      </label>

      <select id="futuretheme_programa_museo_pieza_id" name="futuretheme_programa_museo_pieza_id">
        <option value="">
          <?php esc_html_e('Seleccionar pieza del museo', 'futuretheme'); ?>
        </option>

        <?php foreach ($piezas as $pieza) : ?>
          <option
            value="<?php echo esc_attr($pieza->ID); ?>"
            <?php selected(absint($pieza_id), absint($pieza->ID)); ?>>
            <?php echo esc_html(get_the_title($pieza->ID)); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Esta pieza será mostrada como destaque principal del mes.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_programa_museo_texto_llamada">
        <?php esc_html_e('Texto llamada inferior', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_programa_museo_texto_llamada"
        name="futuretheme_programa_museo_texto_llamada"
        value="<?php echo esc_attr($texto_llamada); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Más de 10 000 piezas arqueológicas — Ven y visítanos', 'futuretheme'); ?>">

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Se mostrará antes del listado de otras piezas destacadas.', 'futuretheme'); ?>
      </span>
    </div>

  </div>

<?php
}


function futuretheme_save_programa_museo_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_programa_museo_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_programa_museo_nonce'])),
      'futuretheme_save_programa_museo_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $mes = isset($_POST['futuretheme_programa_museo_mes'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_programa_museo_mes']))
    : '';

  $allowed_months = array(
    '',
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre',
  );

  if (in_array($mes, $allowed_months, true) && '' !== $mes) {
    update_post_meta($post_id, '_futuretheme_programa_museo_mes', $mes);
  } else {
    delete_post_meta($post_id, '_futuretheme_programa_museo_mes');
  }

  $anio = isset($_POST['futuretheme_programa_museo_anio'])
    ? absint($_POST['futuretheme_programa_museo_anio'])
    : 0;

  if ($anio >= 2000 && $anio <= 2100) {
    update_post_meta($post_id, '_futuretheme_programa_museo_anio', $anio);
  } else {
    delete_post_meta($post_id, '_futuretheme_programa_museo_anio');
  }

  $pieza_id = isset($_POST['futuretheme_programa_museo_pieza_id'])
    ? absint($_POST['futuretheme_programa_museo_pieza_id'])
    : 0;

  if ($pieza_id > 0) {
    update_post_meta($post_id, '_futuretheme_programa_museo_pieza_id', $pieza_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_programa_museo_pieza_id');
  }

  $texto_llamada = isset($_POST['futuretheme_programa_museo_texto_llamada'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_programa_museo_texto_llamada']))
    : '';

  if ('' !== $texto_llamada) {
    update_post_meta($post_id, '_futuretheme_programa_museo_texto_llamada', $texto_llamada);
  } else {
    delete_post_meta($post_id, '_futuretheme_programa_museo_texto_llamada');
  }
}
add_action('save_post_programa_museo', 'futuretheme_save_programa_museo_fields');


/* ============================================================
   CAMPOS PERSONALIZADOS: PIEZA MUSEO
   ============================================================ */

function futuretheme_add_pieza_museo_meta_box()
{

  add_meta_box(
    'futuretheme_pieza_museo_fields',
    __('Datos de la pieza del museo', 'futuretheme'),
    'futuretheme_render_pieza_museo_meta_box',
    'pieza_museo',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_pieza_museo_meta_box');


function futuretheme_render_pieza_museo_meta_box($post)
{

  wp_nonce_field('futuretheme_save_pieza_museo_fields', 'futuretheme_pieza_museo_nonce');

  $tipo          = get_post_meta($post->ID, '_futuretheme_pieza_tipo', true);
  $periodo       = get_post_meta($post->ID, '_futuretheme_pieza_periodo', true);
  $procedencia   = get_post_meta($post->ID, '_futuretheme_pieza_procedencia', true);
  $coleccion     = get_post_meta($post->ID, '_futuretheme_pieza_coleccion', true);
  $codigo        = get_post_meta($post->ID, '_futuretheme_pieza_codigo', true);
  $infografia_id  = get_post_meta($post->ID, '_futuretheme_pieza_infografia_id', true);
  $infografia_url = '';
  if (! empty($infografia_id)) {
    $infografia_url = wp_get_attachment_image_url(absint($infografia_id), 'medium');
  }
  $destacada     = get_post_meta($post->ID, '_futuretheme_pieza_destacada', true);
?>

  <div class="futuretheme-meta-grid">

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_tipo">
        <?php esc_html_e('Tipo de pieza', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_pieza_tipo"
        name="futuretheme_pieza_tipo"
        value="<?php echo esc_attr($tipo); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Cerámica', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_periodo">
        <?php esc_html_e('Periodo cultural', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_pieza_periodo"
        name="futuretheme_pieza_periodo"
        value="<?php echo esc_attr($periodo); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Inca, Prehispánico', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_procedencia">
        <?php esc_html_e('Procedencia', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_pieza_procedencia"
        name="futuretheme_pieza_procedencia"
        value="<?php echo esc_attr($procedencia); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Región Arequipa', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_coleccion">
        <?php esc_html_e('Colección', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_pieza_coleccion"
        name="futuretheme_pieza_coleccion"
        value="<?php echo esc_attr($coleccion); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: Museo Arqueológico UNSA', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_codigo">
        <?php esc_html_e('Código interno', 'futuretheme'); ?>
      </label>

      <input
        type="text"
        id="futuretheme_pieza_codigo"
        name="futuretheme_pieza_codigo"
        value="<?php echo esc_attr($codigo); ?>"
        placeholder="<?php esc_attr_e('Ejemplo: MA-0001', 'futuretheme'); ?>">
    </div>

    <div class="futuretheme-meta-field">
      <label for="futuretheme_pieza_destacada">
        <?php esc_html_e('Mostrar como pieza destacada', 'futuretheme'); ?>
      </label>

      <select id="futuretheme_pieza_destacada" name="futuretheme_pieza_destacada">
        <option value="" <?php selected($destacada, ''); ?>>
          <?php esc_html_e('No', 'futuretheme'); ?>
        </option>
        <option value="1" <?php selected($destacada, '1'); ?>>
          <?php esc_html_e('Sí', 'futuretheme'); ?>
        </option>
      </select>

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Sirve para mostrar esta pieza dentro del listado de otras piezas destacadas.', 'futuretheme'); ?>
      </span>
    </div>

    <div class="futuretheme-meta-field full">
      <label for="futuretheme_pieza_infografia_id">
        <?php esc_html_e('Infografía JPG de la pieza', 'futuretheme'); ?>
      </label>

      <input
        type="hidden"
        id="futuretheme_pieza_infografia_id"
        name="futuretheme_pieza_infografia_id"
        value="<?php echo esc_attr($infografia_id); ?>">

      <div
        id="futuretheme_pieza_infografia_preview"
        style="margin-bottom:10px;">
        <?php if (! empty($infografia_url)) : ?>
          <img
            src="<?php echo esc_url($infografia_url); ?>"
            alt=""
            style="max-width:220px;height:auto;border:1px solid #ccd0d4;padding:4px;background:#fff;">
        <?php endif; ?>
      </div>

      <button
        type="button"
        class="button"
        id="futuretheme_pieza_infografia_select">
        <?php esc_html_e('Seleccionar infografía', 'futuretheme'); ?>
      </button>

      <button
        type="button"
        class="button"
        id="futuretheme_pieza_infografia_remove"
        <?php echo empty($infografia_id) ? 'style="display:none;"' : ''; ?>>
        <?php esc_html_e('Quitar infografía', 'futuretheme'); ?>
      </button>

      <span class="futuretheme-meta-help">
        <?php esc_html_e('Selecciona la infografía desde la Biblioteca de Medios. Se recomienda usar imagen JPG.', 'futuretheme'); ?>
      </span>
    </div>

  </div>

  <p class="futuretheme-meta-help" style="margin-top:14px;">
    <?php esc_html_e('La descripción breve debe colocarse en el campo Extracto. La descripción ampliada puede colocarse en el editor principal.', 'futuretheme'); ?>
  </p>

<?php
}


function futuretheme_save_pieza_museo_fields($post_id)
{

  if (
    ! isset($_POST['futuretheme_pieza_museo_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_pieza_museo_nonce'])),
      'futuretheme_save_pieza_museo_fields'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $fields = array(
    '_futuretheme_pieza_tipo'           => 'futuretheme_pieza_tipo',
    '_futuretheme_pieza_periodo'        => 'futuretheme_pieza_periodo',
    '_futuretheme_pieza_procedencia'    => 'futuretheme_pieza_procedencia',
    '_futuretheme_pieza_coleccion'      => 'futuretheme_pieza_coleccion',
    '_futuretheme_pieza_codigo'         => 'futuretheme_pieza_codigo',
  );

  foreach ($fields as $meta_key => $field_name) {
    $value = isset($_POST[$field_name])
      ? sanitize_text_field(wp_unslash($_POST[$field_name]))
      : '';

    if ('' !== $value) {
      update_post_meta($post_id, $meta_key, $value);
    } else {
      delete_post_meta($post_id, $meta_key);
    }
  }

  $infografia_id = isset($_POST['futuretheme_pieza_infografia_id'])
    ? absint($_POST['futuretheme_pieza_infografia_id'])
    : 0;

  if ($infografia_id > 0) {
    update_post_meta($post_id, '_futuretheme_pieza_infografia_id', $infografia_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_pieza_infografia_id');
  }

  $destacada = isset($_POST['futuretheme_pieza_destacada'])
    ? sanitize_text_field(wp_unslash($_POST['futuretheme_pieza_destacada']))
    : '';

  if ('1' === $destacada) {
    update_post_meta($post_id, '_futuretheme_pieza_destacada', '1');
  } else {
    delete_post_meta($post_id, '_futuretheme_pieza_destacada');
  }
}
add_action('save_post_pieza_museo', 'futuretheme_save_pieza_museo_fields');


/* ============================================================
   MUSEO: CONFIGURACIÓN EN PÁGINA
   Selector de Espacio Cultural asociado
   ============================================================ */

function futuretheme_add_museo_page_meta_box()
{

  add_meta_box(
    'futuretheme_museo_settings',
    __('Configuración Museo', 'futuretheme'),
    'futuretheme_render_museo_page_meta_box',
    'page',
    'side',
    'default'
  );
}
add_action('add_meta_boxes', 'futuretheme_add_museo_page_meta_box');


function futuretheme_render_museo_page_meta_box($post)
{

  wp_nonce_field('futuretheme_save_museo_page_settings', 'futuretheme_museo_nonce');

  $selected_espacio_id = get_post_meta($post->ID, '_futuretheme_museo_espacio_id', true);

  $espacios = get_posts(
    array(
      'post_type'      => 'espacio_cultural',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );
?>

  <p>
    <label for="futuretheme_museo_espacio_id">
      <strong><?php esc_html_e('Espacio Cultural asociado', 'futuretheme'); ?></strong>
    </label>
  </p>

  <select
    id="futuretheme_museo_espacio_id"
    name="futuretheme_museo_espacio_id"
    style="width:100%;">
    <option value="">
      <?php esc_html_e('Seleccionar espacio cultural', 'futuretheme'); ?>
    </option>

    <?php foreach ($espacios as $espacio) : ?>
      <option
        value="<?php echo esc_attr($espacio->ID); ?>"
        <?php selected(absint($selected_espacio_id), absint($espacio->ID)); ?>>
        <?php echo esc_html(get_the_title($espacio->ID)); ?>
      </option>
    <?php endforeach; ?>
  </select>

  <p style="color:#646970;font-size:12px;">
    <?php esc_html_e('Este campo queda disponible para usar datos del Museo Arqueológico, como horario, ubicación o correo, si se requiere en la página.', 'futuretheme'); ?>
  </p>

<?php
}


function futuretheme_save_museo_page_settings($post_id)
{

  if (
    ! isset($_POST['futuretheme_museo_nonce']) ||
    ! wp_verify_nonce(
      sanitize_text_field(wp_unslash($_POST['futuretheme_museo_nonce'])),
      'futuretheme_save_museo_page_settings'
    )
  ) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $espacio_id = isset($_POST['futuretheme_museo_espacio_id'])
    ? absint($_POST['futuretheme_museo_espacio_id'])
    : 0;

  if ($espacio_id > 0) {
    update_post_meta($post_id, '_futuretheme_museo_espacio_id', $espacio_id);
  } else {
    delete_post_meta($post_id, '_futuretheme_museo_espacio_id');
  }
}
add_action('save_post_page', 'futuretheme_save_museo_page_settings');


/* ============================================================
   MUSEO: ZONA DE WIDGET PARA ACORDEÓN INFORMATIVO
   ============================================================ */

function futuretheme_register_museo_widget_area()
{

  register_sidebar(
    array(
      'name'          => __('Museo - Acordeón informativo', 'futuretheme'),
      'id'            => 'museo-acordeon-informativo',
      'description'   => __('Zona para insertar el acordeón informativo de la página Museo.', 'futuretheme'),
      'before_widget' => '',
      'after_widget'  => '',
      'before_title'  => '',
      'after_title'   => '',
    )
  );
}
add_action('widgets_init', 'futuretheme_register_museo_widget_area');


/* ============================================================
   MUSEO: SELECTOR DE MEDIOS PARA INFOGRAFÍA
   ============================================================ */

function futuretheme_admin_museo_media_scripts($hook)
{

  global $post;

  if (! $post || 'pieza_museo' !== $post->post_type) {
    return;
  }

  wp_enqueue_media();

  wp_add_inline_script(
    'jquery-core',
    "
    jQuery(document).ready(function($) {

      var frame;

      $('#futuretheme_pieza_infografia_select').on('click', function(e) {
        e.preventDefault();

        if (frame) {
          frame.open();
          return;
        }

        frame = wp.media({
          title: 'Seleccionar infografía',
          button: {
            text: 'Usar esta infografía'
          },
          multiple: false,
          library: {
            type: 'image'
          }
        });

        frame.on('select', function() {
          var attachment = frame.state().get('selection').first().toJSON();

          $('#futuretheme_pieza_infografia_id').val(attachment.id);

          var previewUrl = attachment.sizes && attachment.sizes.medium
            ? attachment.sizes.medium.url
            : attachment.url;

          $('#futuretheme_pieza_infografia_preview').html(
            '<img src=\"' + previewUrl + '\" alt=\"\" style=\"max-width:220px;height:auto;border:1px solid #ccd0d4;padding:4px;background:#fff;\">'
          );

          $('#futuretheme_pieza_infografia_remove').show();
        });

        frame.open();
      });

      $('#futuretheme_pieza_infografia_remove').on('click', function(e) {
        e.preventDefault();

        $('#futuretheme_pieza_infografia_id').val('');
        $('#futuretheme_pieza_infografia_preview').html('');
        $(this).hide();
      });

    });
    "
  );
}
add_action('admin_enqueue_scripts', 'futuretheme_admin_museo_media_scripts');
