<?php 
/* ----------- OCULTO RANGO DE PRECIO EN PRODUCTO VARIABLE Y LO MUESTRO NORMAL  ---------- */

add_filter( 'woocommerce_variable_sale_price_html', 'bbloomer_variation_price_format', 10, 2 );

add_filter( 'woocommerce_variable_price_html', 'bbloomer_variation_price_format', 10, 2 );

function bbloomer_variation_price_format( $price, $product ) {

 if (is_product()) {
    return $product->get_price();
 } else {
        // Main Price
        $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
        $price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

        // Sale Price
        $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
        sort( $prices );
        $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

        if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
        }
        return $price;
         }

}

// show variation price
add_filter('woocommerce_show_variation_price', function() {return true;});

//override woocommerce function
function woocommerce_template_single_price() {
    global $product;
    if ( ! $product->is_type('variable') ) { 
        woocommerce_get_template( 'single-product/price.php' );
    }
}

function shuffle_variable_product_elements(){
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );
        if ( $product->is_type( 'variable' ) ) {
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
            add_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation', 20 );

            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
            add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_title', 10 );

            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_excerpt', 30 );
        }
    }
}
add_action( 'woocommerce_before_single_product', 'shuffle_variable_product_elements' );


//WOOCOMMERCE COUNT IN HEADER


//in header.php
$items_count = WC()->cart->get_cart_contents_count(); 
?>
    <div id="mini-cart-count"><?php echo $items_count ? $items_count : '&nbsp;'; ?></div>
<?php

//in functions.php
add_filter( 'woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments){
    ob_start();
    $items_count = WC()->cart->get_cart_contents_count();
    ?>
    <div id="mini-cart-count"><?php echo $items_count ? $items_count : '&nbsp;'; ?></div>
    <?php
        $fragments['#mini-cart-count'] = ob_get_clean();
    return $fragments;
}








//CODIGO JS EN EL HEAD (Google Tag Conversion, en página Thank you (order received Woocommerce)

add_action( 'wp_head', 'my_google_conversion' );
function my_google_conversion(){
    if( is_wc_endpoint_url( 'order-received' ) ) :
        $order_id = absint( get_query_var('order-received') ); // Get order ID
        if( get_post_type( $order_id ) !== 'shop_order' ) return; // Exit
        $order = wc_get_order( $order_id );
        $order_num = $order->get_order_number();
        $order_total = $order->get_total();
    ?>
    <!-- Event snippet for Compras conversion page --> <script> gtag('event', 'conversion', { 'send_to': 'AW-714870159/FaohCN743akBEI-b8NQC', 'value': <?php echo $order_total;?>, 'currency': 'CLP', 'transaction_id': '<?php echo $order_num;?>' }); </script>
    <?php   
    endif;
}


// //301 Redirects en .htacess

// # BEGIN WordPress
// <IfModule mod_rewrite.c>
// RewriteEngine On
// RewriteBase /

// # BEGIN Permanent URL redirects
// RewriteRule ^about-cs\.html$ /about-christian-simpson/? [L,R=301,NC]
// RewriteRule ^entrepreneurial-success\.html$ /entrepreneurial-success/? [L,R=301,NC] 
// # other 301 rules here

// RewriteRule ^index\.php$ - [L]
// RewriteCond %{REQUEST_FILENAME} !-f
// RewriteCond %{REQUEST_FILENAME} !-d
// RewriteRule . /index.php [L]
// </IfModule>
// # END WordPress


//Cambia texto
add_filter('gettext', 'translate_volv_tienda');
add_filter('ngettext', 'translate_volv_tienda');
function translate_volv_tienda($translated) {
$translated = str_ireplace('Volver a la tienda', 'Comprar entradas', $translated);
return $translated;
}

//Quita link a productos en checkout woocommerce
add_filter('woocommerce_cart_item_permalink','__return_false');

// Cambia el link de Volver a la tienda woocommerce
add_filter( 'woocommerce_return_to_shop_redirect', 'bbloomer_change_return_shop_url' );
 
function bbloomer_change_return_shop_url() {
return 'https://juc.maimonides.edu/inscripcion';
}


//Quitar campos del checkout woocommerce

function custom_override_checkout_fields( $fields ) {
unset($fields['billing']['billing_first_name']);
unset($fields['billing']['billing_last_name']);
unset($fields['billing']['billing_company']);
unset($fields['billing']['billing_address_1']);
unset($fields['billing']['billing_address_2']);
unset($fields['billing']['billing_city']);
unset($fields['billing']['billing_postcode']);
unset($fields['billing']['billing_country']);
unset($fields['billing']['billing_state']);
unset($fields['billing']['billing_phone']);
unset($fields['order']['order_comments']);
unset($fields['billing']['billing_email']);
unset($fields['account']['account_username']);
unset($fields['account']['account_password']);
unset($fields['account']['account_password-2']);
return $fields;
}

/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );



//Remover una categoria de la busqueda de productos

// Excluir categoria packs del buscador
function wpse188669_pre_get_posts( $query ) {
   if ( ! is_admin() && $query->is_main_query() && $query->is_search()) {
       $query->set( 'post_type', array( 'product' ) );
       $tax_query = array(
           array(
               // likely what you are after
               'taxonomy' => 'product_cat',
               'field'   => 'slug',
               'terms'   => 'packs',
               'operator' => 'NOT IN',
           ),
       );
       $query->set( 'tax_query', $tax_query );
	}
}
add_action( 'pre_get_posts', 'wpse188669_pre_get_posts' );



//Custom Query con Post type, y custom field (object type).
$params = array(
	    'post_type' => 'product',
			'meta_query'		=> array(
			'relation'		=> 'AND',
				array(
					'key' => 'artista_relacionado',
					'value' =>  get_the_ID(),
					'compare' => 'LIKE'
				),
				array(
	        'key' => '_stock_status',
	        'value' => 'instock'
	      )
			)

	  );
	  $wc_query = new WP_Query($params);





//Registrar campos en api rest

add_action( 'rest_api_init', 'add_thumbnail_to_JSON' );
function add_thumbnail_to_JSON() {
//Add featured image
register_rest_field( 
    'post', // Where to add the field (Here, blog posts. Could be an array)
    'featured_image_src', // Name of new field (You can call this anything)
    array(
        'get_callback'    => 'get_image_src',
        'update_callback' => null,
        'schema'          => null,
         )
    );
}

function get_image_src( $object, $field_name, $request ) {
  $feat_img_array = wp_get_attachment_image_src(
    $object['featured_media'], // Image attachment ID
    'thumbnail',  // Size.  Ex. "thumbnail", "large", "full", etc..
    true // Whether the image should be treated as an icon.
  );
  return $feat_img_array[0];
}


// Paginas con Ajax

//en single.php

 /*
    $post = get_post($_POST['id']); // this line is used to define the {id:post_id} which you will see in another snippet further down
		if ($post) { // this is necessary and is a replacement of the typical `if (have_posts())`
        setup_postdata($post); // needed to format custom query results for template tags ?>
        <!-- everything below this line is your typical page template coding -->
        <div <?php post_class() ?> id="post-<?php the_ID(); ?>">

            <h2><?php the_title(); ?></h2>
						<h3><?php the_field(titulo);?></h3>
            <div class="entry">
                <?php the_content(); ?>
            </div>

        </div>

<?php }  */?>



En main.js
<script>

$.ajaxSetup({cache:false});
$(".link_nosotros").click(function(e){ // line 5
		//pageurl = $(this).attr('href');
		//if(pageurl!=window.location) {
		//		window.history.pushState({path: pageurl}, '', pageurl);
		//}

		var post_id = $(this).attr("rel");
		$("#contenido").load("https://nomada.com/nosotros",{id:post_id}); // line 12
		return false;
});
$(".link_trabajos").click(function(e){ // line 5
	//	pageurl = $(this).attr('href');
	//	if(pageurl!=window.location) {
	//			window.history.pushState({path: pageurl}, '', pageurl);
	//	}

		var post_id = $(this).attr("rel");
		$("#contenido").load("https://nomada.com/trabajos",{id:post_id}); // line 12
		return false;
});
</script>

<?php

//Los templates php se deben llamar igual que el slug




//Agregar soporte para mensajes en woocommerce (alertas tipo cart empty

do_action(wc_print_notices());


//Registrar sidebar functions.php

function wpb_widgets_init() {
 
    register_sidebar( array(
        'name' => __( 'Main Sidebar', 'wpb' ),
        'id' => 'sidebar-1',
        'description' => __( 'The main sidebar appears on the right on each page except the front page template', 'wpb' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
 
add_action( 'widgets_init', 'wpb_widgets_init' );

//Luego en el theme

?>

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
    <div id="secondary" class="widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
<?php endif; ?>

<?php
//Compatibilidad de themes de woocommerce - toma carpeta child dentro de carpeta del theme

function mytheme_add_woocommerce_support() { // Agrego soporte para WooCommerce
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );


//Remover el boton de add to cart
add_action( 'woocommerce_single_product_summary', 'hide_add_to_cart_button_variable_product', 1, 0 );
function hide_add_to_cart_button_variable_product() {

    // Removing add to cart button and quantities only
    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
}


//Escapar caracteres especiales 
esc_attr()



//Woocommerce theme support con tamaños de thumbs

function mytheme_add_woocommerce_support() { // Agrego soporte para WooCommerce
	add_theme_support( 'woocommerce', array(
'thumbnail_image_width' => 800,
'thumbnail_image_height' => 500,
'gallery_thumbnail_image_width' => 800,
'gallery_thumbnail_image_height' => 500,
'single_image_width' => 800,
'single_image_height' => 500,
) );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
?>

//Breadcrumbs function
<li><?php echo get_category_parents( $idCat, true, ' / ' ); ?></li>

//Custom Query de Post type usando Custom Field

<?php
$tipoTraining = get_field('training_level');
$query = new WP_Query( array(
	'post_type' => 'training',
	'posts_per_page' => 5,
	'meta_key' => 'training_level',
	'meta_value' => $tipoTraining
) );
?>




//Custom WP_Query con paginación


<?php
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $query = new WP_Query( array(
        'cat' => get_query_var('cat'),
        'posts_per_page' => 2,
        'paged' => $paged
    ) );
?>

<?php if ( $query->have_posts() ) : ?>

<!-- begin loop -->
<?php while ( $query->have_posts() ) : $query->the_post(); ?>

    <h2><a href="<?php the_permalink(); ?>" title="Read"><?php the_title(); ?></a></h2>
    <?php the_excerpt(); ?>
    <?php echo get_the_date(); ?>
    <?php echo $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>

<?php endwhile; ?>
<!-- end loop -->

<div class="pagination">
    <?php
				/*
        echo paginate_links( array(
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'total'        => $query->max_num_pages,
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
            'end_size'     => 2,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
				*/
				wp_pagenavi(array( 'query' => $query )); //si se usa el plugin PageNavi
    ?>
</div>


<?php wp_reset_postdata(); ?>

<?php else : ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>



//Contenido flexible sin loop, trayendo arrays por id de posicion
<!-- Las 4 imagenes -->
<?php $imagenesActividades = get_field('imagenes_ac'); ?>

<?php echo $imagenesActividades[0]["item"];  //URL Imagen 1 vertical left ?>
<?php echo $imagenesActividades[1]["item"]; //URL Imagen 2 top ?>
<?php echo $imagenesActividades[2]["item"]; //URL Imagen 3 bottom ?>
<?php echo $imagenesActividades[3]["item"]; //URL Imagen 4 vertical right ?>

//Loop para archive con paginación

$args = array(
	'post_type' => 'rmcc_blurb',
);
$query = new WP_query ( $args );
//usar $query->have_posts()


<?php if ( have_posts() ) : $postCount = 1;?>
	<?php	while ( have_posts() ) : $postCount++; the_post(); //Loop de cada post?>
		<?php if($postCount == 2) { //El primer post de cada página del arcchive?>
			<article id="post-<?php the_ID(); ?>" class="destacado">
			<?php }else{ //post no destacados?>
				<article id="post-<?php the_ID(); ?>" class="post-normal">
			<?php } //postCount?>
			<h3><a href="<?php the_permalink(); ?>"><?php echo tituloNota($post->ID,4); //corta a 4 palabras ?></a></h3>
			<?php if ( has_post_thumbnail() ) { ?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'medium', array('class' => 'left','alt'	=> get_the_title()) );?>
				</a>
			<?php }?>
			<?php echo excerptNota($post->ID,15); //Excerpt nota - corta a 15 palabras, ver si esta en el diseño. ?>
		</article>
	<?php	endwhile; //loop?>

	<?php	//Paginación, tiene que ir dentro del if have_posts
	the_posts_pagination( array(
	'prev_text' => 'Anteriores',
	'next_text' => 'Siguientes',
	'screen_reader_text' => 'Más notas',
	//'before_page_number' => '<span class="meta-nav screen-reader-text">pag</span>',
	) );
	?>
<?php endif; //have_posts?>

<?php
//Woocommerce - ver si un cliente compró un producto - web alok

$current_user = wp_get_current_user();
if ( ! wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product->id ) ) {
    _e( 'This content is only visible for users who bought this product.' );
}



//Include Hoja de estilos con version, evita cache del browser

 function theme_styles()
 {
   global $ver_num;
   $ver_num = mt_rand();
   wp_enqueue_style( 'style-css', get_template_directory_uri() . '/style.css', array(), $ver_num, 'all' );
 }
 add_action('wp_enqueue_scripts', 'theme_styles');



//Query con custom fields
$args = array( 
    'posts_per_page' => -1, 
    'offset'         => 0,    
    'post_type'      => 'portfolio',
    'meta_key'       => 'up_count'
    'orderby'        => 'meta_value_num'
    'order'          => 'DESC',
);

//Quitar la etiqueta hentry (search Console webmaster tools. error)
//Remove the Hentry Class
function remove_hentry_post_class( $classes ) {
    $classes = array_diff( $classes, array( 'hentry' ) );
    return $classes;
}
add_filter( 'post_class', 'remove_hentry_post_class' );


//Selector de post types en Rules Location de ACF (jun/2018)

// add group and filter
add_filter('acf/location/rule_types', 'acf_my_custom_post_type_filters');
function acf_my_custom_post_type_filters($choices) {
	// we want to insert it after pages
	// so it's in a nice order
	if (!isset($choices['Bloques'])) {
		$new_choices = array();
		foreach ($choices as $key => $value) {
			$new_choices[$key] = $value;
			if ($key == 'Page') {
				$new_choices['Bloques'] = array();
			}
		} // end foreach choices
		$choices = $new_choices;
	} // end if not in choices
	if (!isset($choices['Bloques']['post'])) {
		$choices['Bloques']['my_custom_post_type_post'] = 'Bloques';
	}
	return $choices;
}

// add choices
add_filter('acf/location/rule_values/my_custom_post_type_post', 'acf_location_rules_values_my_custom_post_type');
function acf_location_rules_values_my_custom_post_type($choices) {
	// adjust the for loop to the number of levels you need
	$args = array(
		'post_type' => 'bloque',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => array('title' => 'ASC', 'date' => 'DESC'),
	);
	$query = new WP_Query($args);
	//echo '<pre>'; print_r($query->posts); echo '</pre>';
	if (count($query->posts)) {
		foreach ($query->posts as $post) {
			$choices[$post->ID] = $post->post_title;
		}
	}
	return $choices;
}

// use the standard post matching
// this is copied directly form ACF
add_filter('acf/location/rule_match/my_custom_post_type_post', 'acf_location_rule_match_my_custom_post_type_post', 10, 3);
function acf_location_rule_match_my_custom_post_type_post($match, $rule, $options) {
	$post_id = $options['post_id'];
	if( !$post_id ) {
		return false;
	}
	if ($rule['operator'] == "==") {
		$match = ($options['post_id'] == $rule['value']);
	} elseif ($rule['operator'] == "!=") {
		$match = ($options['post_id'] != $rule['value']);
	}
	return $match;
}


/* Filtro para selectores notas ACF objetc type - Lean 22/11 */

add_filter('acf/fields/post_object/query', 'change_posts_order', 10, 3);

function change_posts_order( $args, $field, $post )
{
$args['post_status'] = 'publish';
$args['date_query'] = array(
        array(
            'column' => 'post_date_gmt',
            'after'  => '30 days ago',
        )
);

return $args;
}

//Cambiar los separadores del titulo a pipe - minimal
function wploop_change_separator()
{
return '|';
}
add_filter('document_title_separator', 'wploop_change_separator');



//Remover Default Jquery en Wordpress (Colocar en header.php antes del include del head)
?>
<?php wp_deregister_script('jquery'); ?>



//Escapar comillas dobles en Meta description usando el excerpt

<meta name="description" content="<?php echo esc_attr(htmlentities(get_the_excerpt()));  ?>">



Redirigir siempre a https (va en htaccess)

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

Luego en header.php 
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

//ACF Contenido Flexible con Slick

<div id="carrousel_clientes">
  <?php if( have_rows('carrousel_logos') ):?>
    <?php  while ( have_rows('carrousel_logos') ) : the_row(); ?>
      <?php  if( get_row_layout() == 'logo' ):  //notas?>
        <div class="carrousel_item">
          <a href="<?php the_sub_field('link');?>" target="_blank"><img src="<?php the_sub_field('logo_imagen');?>" alt="<?php the_sub_field('empresa');?>"></a>
        </div>
        <?php wp_reset_postdata(); // IMPORTANTE ?>
      <?php  endif; ?>
    <?php  endwhile; ?>
  <?php endif; //carrousel_logos?>
</div><!-- #carrousel_clientes -->



// Las ultimas 3 noticias

<?php
  $args = array(
        'post_type' => 'post',
        'posts_per_page'         => '3'

    );
$the_query = new WP_Query( $args );
?>
<?php if ( $the_query->have_posts() ) : ?>
  <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
    <div class="col">
      <img src="<?php the_post_thumbnail_url();?>" alt="">
      <h4><?php the_title(); ?></h4>
      <p><?php the_excerpt(); ?></p>
      <a href="<?php the_permalink();?>" class="btn verde">Leer más</a>
    </div><!-- .col -->
  <?php endwhile; ?>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>


//Contenido flexible con campos de un post

//repeated field: directivos, layout: miembro_directorio, row tipo object: directivo
<?php if( have_rows('directivos') ):?>
  <?php  while ( have_rows('directivos') ) : the_row(); ?>
    <?php  if( get_row_layout() == 'miembro_directorio' ):  //Destacadas?>
      <?php $directivo = get_sub_field('directivo');?>
      <?php if( $directivo ): $post = $directivo; setup_postdata( $post ); ?>
        <div class="col">
          <img src="<?php the_post_thumbnail_url();?>">
          <div class="data">
            <h3><?php the_title();?></h3>
            <p class="cargo"><?php the_field('cargo');?></p>
            <p><?php the_field('bio');?></p>
          </div>
        </div><!-- .col -->
      <?php wp_reset_postdata(); // IMPORTANTE ?>
      <?php endif; ?>
    <?php  endif; ?>
  <?php  endwhile; ?>
<?php endif;?>

<?php 
//Objeto Post



//Ordenar los productos de Woocommerce por fecha

add_filter('woocommerce_get_catalog_ordering_args', 'am_woocommerce_catalog_orderby');
function am_woocommerce_catalog_orderby( $args ) {
$args['meta_key'] = '';
$args['orderby'] = 'date';
$args['order'] = 'desc';
return $args;
}

?>