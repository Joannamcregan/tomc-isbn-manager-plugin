<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//require files
require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('/inc/settings-route.php');

// function marketplace_custom_rest() {
//     register_rest_field('post', 'authorName', array(
//         'get_callback' => function () {return get_the_author();}
//     ));
// }

// add_action('rest_api_init', 'marketplace_custom_rest');

$ebookCategoryID = 32; /*ebooks*/

function marketplace_files(){
    wp_enqueue_script('main-ebook-marketplace-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('buddypress', get_stylesheet_directory_uri() . '/css/buddypress.css', false, '', 'all');
    wp_enqueue_style('bbpress', get_stylesheet_directory_uri() . '/css/bbpress.css', false, '', 'all');
    wp_enqueue_style('book-display-styles', get_stylesheet_directory_uri() . '/css/book-display-styles.css', false, '', 'all');
    wp_enqueue_style('general-styles', get_stylesheet_directory_uri() . '/css/general-styles.css', false, '', 'all');
    wp_enqueue_style('front-styles', get_stylesheet_directory_uri() . '/css/front-styles.css', false, '', 'all');
    wp_enqueue_style('category-styles', get_stylesheet_directory_uri() . '/css/category-styles.css', false, '', 'all');
    wp_enqueue_style('community-members-styles', get_stylesheet_directory_uri() . '/css/community-members-styles.css', false, '', 'all');
    wp_enqueue_style('image-text-container-styles', get_stylesheet_directory_uri() . '/css/image-text-container-styles.css', false, '', 'all');
    wp_enqueue_style('comment-styles', get_stylesheet_directory_uri() . '/css/comment-styles.css', false, '', 'all');
    wp_enqueue_style('seventies-styles', get_stylesheet_directory_uri() . '/css/seventies-style.css', false, '', 'all');
    wp_enqueue_style('woo-styles', get_stylesheet_directory_uri() . '/css/woo-page-styles.css', false, '', 'all');
    wp_enqueue_style('forminator-styles', get_stylesheet_directory_uri() . '/css/forminator-styles.css', false, '', 'all');
    wp_enqueue_style('icon-styles', get_stylesheet_directory_uri() . '/css/icon-styles.css', false, '', 'all');

    wp_localize_script('main-ebook-marketplace-js', 'marketplaceData', array(
        'root_url' => get_site_url(),        
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action('wp_enqueue_scripts','marketplace_files');

/* Disable WordPress Admin Bar for all users */
add_filter( 'show_admin_bar', '__return_false' );

// Disable default WooCommerce styling
add_filter('woocommerce_enqueue_styles', '__return_false');

/*add theme support-------------------------------------------------------------*/
function marketplace_features(){
    add_image_size('bookCover', 300, 450, true);
    add_image_size('bookThumbnail', 150, 225, true);
    add_image_size('authorImage', 300, 450, true);
    add_theme_support('woocommerce', array(
        'product_grid' => array(
            'default_rows' => 5,
            'max_rows' => 10,
            'default_columns' => 2,
            'min-columns' => 1,
            'max_columns' => 5
        )
    ));
    add_theme_support('wc_product_gallery_slider');
}

add_action('after_setup_theme', 'marketplace_features');

//Free Offer Post Type------------------------------------------------------------------------------------
// function free_offer_custom_post_types() {
//     register_post_type('free offer', array(
//         'supports' => array('title', 'editor', 'excerpt'),
//         'has_archive' => true,
//         'rewrite' => array(
//             'slug' => 'free-offers'
//         ),
//         'public' => true,
//         'menu_position' => 0,
//         'labels' => array(
//             'name' => 'Free Offers',
//             'add_new' => 'Add New Free Offer',
//             'edit_item' => 'Edit Free Offer',
//             'all_items' => 'All Free Offers',
//             'singular_item' > 'Free Offer'
//         ),
//         'menu_icon' => 'dashicons-smiley'
//     ));
// }

// add_action('init', 'free_offer_custom_post_types');

//Need Post Type------------------------------------------------------------------------------------
// function need_custom_post_types() {
//     register_post_type('need', array(
//         'supports' => array('title', 'editor', 'excerpt'),
//         'has_archive' => true,
//         'rewrite' => array(
//             'slug' => 'needs'
//         ),
//         'public' => true,
//         'menu_position' => 0,
//         'labels' => array(
//             'name' => 'Needs',
//             'add_new' => 'Add New Need',
//             'edit_item' => 'Edit Needs',
//             'all_items' => 'All Needs',
//             'singular_item' > 'Need'
//         ),
//         'menu_icon' => 'dashicons-heart'
//     ));
// }

// add_action('init', 'need_custom_post_types');

/*customize login logo----------------------------------------------------------------------*/
function ebook_marketplace_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
        background-image: url(<?php echo get_theme_file_uri('/images/TOMC logo.jpg'); ?>);
        height:100px;
        width:300px;
        background-size: 300px 100px;
        background-repeat: no-repeat;
        padding-bottom: 10px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'ebook_marketplace_login_logo' );

/*remove sidebar-------------------------------------------------------------------*/
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/*remove breadcrumbs from all pages-------------------------------------------------------*/
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

/*genre category archive page-----------------------------------------------------------------------*/

function woocommerce_template_loop_product_link_open() {
    global $product;
 
    $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
 
    echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="' . $product->get_title() . ' product page">';
}

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

function ebook_marketplace_product_collection_container(){
    echo '<div class="product-collection-container">';
}
add_action('woocommerce_product_loop_start', 'ebook_marketplace_product_collection_container', 10);

function ebook_marketplace_product_details_opening_div(){
    echo '<div class="product-section">';
}
add_action( 'woocommerce_before_shop_loop_item', 'ebook_marketplace_product_details_opening_div', 12 );

function ebook_marketplace_genre_archive_include_price(){
    add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_price', 25);
}
add_action('woocommerce_after_shop_loop_item_title', 'ebook_marketplace_genre_archive_include_price', 20);

function ebook_marketplace_product_details_closing_div(){
    echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'ebook_marketplace_product_details_closing_div', 10 );

function ebook_marketplace_product_collection_container_close(){
    echo '</div>';
}
add_action('woocommerce_after_shop_loop', 'ebook_marketplace_product_collection_container_close', 10);

remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
add_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 30);

/*accessibility for product archive------------------------------------------------------------------------*/
// add_filter('wp_get_attachment_image_attributes', 'ebook_marketplace_attachement_image_attributes', 20, 2);

// function ebook_marketplace_attachement_image_attributes($attr, $attachment) {
//     global $post;
//     if ($post->post_type == 'product') {
//         $title = $post->post_title;
//         $attr['alt'] = $title;
//         $attr['title'] = $title;
//     }
//     return $attr;
// }

// if ( ! function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
// 	/**
// 	 * Insert the opening anchor tag for products in the loop.
// 	 */
// 	function woocommerce_template_loop_product_link_open() {
// 		global $product;

// 		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

// 		echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link" aria-label="' . get_the_title() . '">';
// 	}
// }

/*style single product page ------------------------------------------------------------------*/

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

function ebook_marketplace_single_product_main_div(){
    echo '<div class="product-page-content">';
}
add_action('woocommerce_before_single_product', 'ebook_marketplace_single_product_main_div', 10);

function ebook_marketplace_bottom_section_start(){
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);
    // echo '</div>';
}
add_action('woocommerce_single_product_summary', 'ebook_marketplace_bottom_section_start', 20);

function ebook_marketplace_single_product_closing_bottom_div(){
    echo '</div>';
}
add_action('woocommerce_after_single_product_summary', 'ebook_marketplace_single_product_closing_bottom_div', 10);

function ebook_marketplace_single_product_closing_main_div(){
    echo '</div>';
}
add_action('woocommerce_after_single_product_summary', 'ebook_marketplace_single_product_closing_main_div', 20);

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

function ebook_marketplace_single_product_additional_info() {  
    global $MVX, $product, $wpdb;
    $productid = $product->get_id();
    $books_pennames_table = $wpdb->prefix . "tomc_pen_names_books";
    $posts_table = $wpdb->prefix . "posts";
    $book_products_table = $wpdb->prefix . "tomc_book_products";
    $query = 'select p.post_title 
    from %i bp
    join %i bpn on bp.bookid = bpn.bookid
    and bp.productid = %d 
    join %i p on bpn.pennameid = p.id
    limit 1';
    $results = $wpdb->get_results($wpdb->prepare($query, $book_products_table, $books_pennames_table, $productid, $posts_table), ARRAY_A);
    if ($results){
        echo '<h2>by ' . $results[0]['post_title'] . '</h2>';
    }
}
add_action( 'woocommerce_single_product_summary', 'ebook_marketplace_single_product_additional_info', 13 );

function ebook_marketplace_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] ); 
    unset($tabs['description']);
    unset($tabs['reviews']);
    unset($tabs['vendor']);
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'ebook_marketplace_remove_product_tabs', 98 );

function tomc_get_gallery_files() {
    global $product, $wpdb;
    $productid = $product->get_id();
    $posts_table = $wpdb->prefix . "posts";
    $query = 'select id, guid, post_name, post_mime_type from %i where post_type = "attachment" and post_mime_type in ("audio/ogg", "audio/mpeg") and post_parent = %d';
    $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $productid));
    if ($results){
        echo '<div class="tomc-product-preview-section-wrapper"><div class="tomc-product-preview-section"><h2 class="centered-text">Preview</h2>';
        foreach($results as $result){
            // echo '<p class="centered-text">' . $result->post_name . '</p>';
            if ($result->post_mime_type = "audio/ogg"){
                echo '<audio controls><source src="' . $result->guid . '" type="audio/ogg">Sorry! Your browser does not support the audio element.</audio>';
            } else {
                echo '<audio controls><source src="' . $result->guid . '" type="audio/mpeg">Sorry! Your browser does not support the audio element.</audio>';
            }
        }
        echo '</div></div>';
    } 
}
add_action( 'woocommerce_after_single_product_summary', 'tomc_get_gallery_files', 20 );

function tomc_get_book_info() {
    // global $MVX, $product, $wpdb;
    global $product, $wpdb;
    $productid = $product->get_id();
    $books_table = $wpdb->prefix . "tomc_books";
    $book_products_table = $wpdb->prefix . "tomc_book_products";
    $query = 'select books.book_description, books.book_excerpt
    from %i bp
    join %i books on bp.bookid = books.id
    and bp.productid = %d 
    limit 1';
    $results = $wpdb->get_results($wpdb->prepare($query, $book_products_table, $books_table, $productid), ARRAY_A);
    if ($results){
        echo '<div class="tomc-single-book-description-wrapper"><div class="tomc-single-book-description"><h2>Description</h2><p style="white-space: pre-line">' . $results[0]['book_description'] . '</p></div></div>';
        echo '<div class="tomc-single-book-excerpt-wrapper"><div class="tomc-single-book-excerpt"><h2>Excerpt</h2><p style="white-space: pre-line">' . $results[0]['book_excerpt'] . '</p></div></div>';
    }
}
add_action( 'woocommerce_after_single_product_summary', 'tomc_get_book_info', 30 );

function tomc_get_book_author_info() {
    global $MVX, $product, $wpdb;
    $productid = $product->get_id();
    $books_pennames_table = $wpdb->prefix . "tomc_pen_names_books";
    $posts_table = $wpdb->prefix . "posts";
    $book_products_table = $wpdb->prefix . "tomc_book_products";
    $query = 'select p.post_content 
    from %i bp
    join %i bpn on bp.bookid = bpn.bookid
    and bp.productid = %d 
    join %i p on bpn.pennameid = p.id
    limit 1';
    $results = $wpdb->get_results($wpdb->prepare($query, $book_products_table, $books_pennames_table, $productid, $posts_table), ARRAY_A);
    if ($results){
        if (strlen($results[0]['post_content']) > 0){
            echo '<div class="tomc-single-product-author-wrapper"><div class="tomc-single-product-author"><h2>About the Author</h2><p style="white-space: pre-line">' . $results[0]['post_content'] . '</div></div>';
        }
    }
}
add_action( 'woocommerce_after_single_product_summary', 'tomc_get_book_author_info', 40 );

function tomc_template_product_reviews() {
    echo '<div class="tomc-review-section-wrapper">';
    woocommerce_get_template( 'single-product-reviews.php' );
    echo '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'tomc_template_product_reviews', 50 );

/*allow dashicons to display----------------------------------------------------------------------*/
function ebook_marketplace_load_dashicons(){
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'ebook_marketplace_load_dashicons');

/*infinite scroll*/
// add_theme_support( 'infinite-scroll', array(
//     'type' => 'scroll',
//     'footer_widgets' => false,
//     'footer' => false,
//     'container' => 'content',
//     'wrapper' => true,
//     'render' => false,
//     'posts_per_page' => false,
//    ) );

/*remove proudly powered by wordpress */
function ebook_marketplace_remove_storefront_credit() {
    remove_action( 'storefront_footer', 'storefront_credit' );
}
add_action('wp_head', 'ebook_marketplace_remove_storefront_credit', 5);

// change author url base to contributor
function ebook_marketplace_new_author_base() {
    global $wp_rewrite;
    $myauthor_base = 'people';
    $wp_rewrite->author_base = $myauthor_base;
}
add_action('init', 'ebook_marketplace_new_author_base');

// Make book covers not clickable on individual product pages-------------------------------------------
function remove_product_image_link( $html, $post_id ) {
    return preg_replace( "!<(a|/a).*?>!", '', $html );
}
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'remove_product_image_link', 10, 2 );

// Customize My Account nav-----------------------------------------------------------------------------
add_filter( 'woocommerce_account_menu_items', function($items) {
    unset($items['subscriptions']);
    unset($items['edit-address']);
    return $items;
}, 99, 1 );

// Assign roles on submission of certain Forminator forms------------------------------------------------------------------------------
add_action( 'forminator_form_after_handle_submit', 'assignReaderMemberRole', 10, 2 );
add_action( 'forminator_form_after_save_entry', 'assignReaderMemberRole', 10, 2 );
add_action( 'forminator_form_after_handle_submit', 'assignCreatorMemberRole', 10, 2 );
add_action( 'forminator_form_after_save_entry', 'assignCreatorMemberRole', 10, 2 );

function assignReaderMemberRole($form_id, $response) {
    if( $response['success']  && $form_id ==2372 /* Reader-Member Signup */){
        $user = wp_get_current_user();
        $user->add_role( 'reader-member' );
    }
}

function assignCreatorMemberRole($form_id, $response) {
    if( $response['success']  && $form_id ==4212 /* Creator-Member Signup */){
        $user = wp_get_current_user();
        $user->add_role( 'creator-member' );
        $user->add_role( 'dc_vendor' );
    }
} 

// maintenance mode-----------------------------------------------------------------------
function tomc_maintenance_mode() {
    if (!is_user_logged_in()) {    
        wp_die("<h1 style='color: #0c6980; text-align: center'>Coming Soon!</h1><p style='text-align: center'>The Trunk of My Car Cooperative Marketplace is launching May 2024. Our current member-owners are uploading works and conducting final tests before making the platform widely available in the coming days. You can learn more about the cooperative community we're building <a href='https://www.trunkofmycar.org/'>here</a>. </p>");    
    }    
}
    
add_action('get_header', 'tomc_maintenance_mode');

