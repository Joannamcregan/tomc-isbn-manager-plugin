<?php
/* 
    Plugin Name: TOMC ISBN Manager
    Version: 1.0
    Author: Joanna
    Description: Track ISBN Numbers for our members
*/

if( ! defined('ABSPATH') ) exit;

class TOMCBookISBNPlugin {
    function __construct() {
        global $wpdb;         
        $this->charset = $wpdb->get_charset_collate();
        $this->isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $this->users_table = $wpdb->prefix . "users";
        $this->posts_table = $wpdb->prefix . "posts";
        $this->meta_table = $wpdb->prefix . "postmeta";
        $this->book_products_table = $wpdb->prefix . "tomc_book_products";
        $this->books_table = $wpdb->prefix .  "tomc_books";
        $this->book_products_table = $wpdb->prefix . "tomc_book_products";

        $this->cartContainsISBN = false;

        wp_localize_script('tomc-bookorg-js', 'tomcBookorgData', array(
            'root_url' => get_site_url()
        ));

        add_action('activate_tomc-isbn-manager/tomc-isbn-manager.php', array($this, 'onActivate'));
        add_action('init', array($this, 'registerScripts'));
        add_action('wp_enqueue_scripts', array($this, 'pluginFiles'));
        add_filter('template_include', array($this, 'loadTemplate'), 99);

        add_action('woocommerce_after_order_notes', array($this, 'isbnInfoFields'));
        add_action('woocommerce_checkout_process', array($this, 'validateIsbnInfo'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'isbnInfoUpdateMeta'));
    }

    function registerScripts(){
        wp_register_style('tomc_isbn_styles', plugins_url('css/tomc-isbn-styles.css', __FILE__), false, '1.0', 'all');
    }

    function pluginFiles(){
        wp_enqueue_style('tomc_isbn_styles');
        // wp_enqueue_script('tomc-isbn-js', plugin_dir_url(__FILE__) . '/build/index.js', array('jquery'), '1.0', true);
        // wp_localize_script('tomc-isbn-js', 'tomcisbnData', array(
        //     'root_url' => get_site_url()
        // ));
    }

    function addISBNRecordsPage() {
        $records_page = array(
            'post_title' => 'ISBN Records',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 0,
            'post_type' => 'page'
        );
        wp_insert_post($records_page);
    }

    function onActivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_records_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            productid bigint(20) unsigned NOT NULL,
            createdate datetime NOT NULL,
            hasBeenProcessed bit NOT NULL DEFAULT 0,
            processeddate datetime NULL,
            processedby bigint(20) unsigned NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (productid) REFERENCES $this->posts_table(id),
            FOREIGN KEY  (processedby) REFERENCES $this->users_table(id)
        ) $this->charset;");

        if (post_exists('ISBN Records', '', '', 'page', 'publish') == 0){
            $this->addISBNRecordsPage();
        }
    }

    function loadTemplate($template){
        if (is_page('isbn-records')){
            return plugin_dir_path(__FILE__) . 'inc/template-isbn-records.php';
        }  else
        return $template;
    }

    function isbnInfoFields($checkout){ 
        global $wpdb;                
        $user = wp_get_current_user();
        $cart = WC()->cart->get_cart();
        $now = date('Y-m-d H:i:s');
        $userId = get_current_user_id();        

        foreach( $cart as $cart_item_key => $cart_item ){  
            $item = $cart_item['data'];
            if ($item->get_name() == 'ISBN'){
                $this->cartContainsISBN = true;
                $query = 'select a.id, a.post_title from %i a where a.post_type = %s and a.post_status = %s and a.post_author = %d order by a.post_title';
                $products = $wpdb->get_results($wpdb->prepare($query, $this->posts_table, 'product', 'publish', $userId), ARRAY_A);
                $productsArr = [];
                for($i = 0; $i < count($products); $i++){
                    $productsArr[$products[$i]['id']] = $products[$i]['post_title'];
                }
                if (!(is_user_logged_in() && (in_array( 'dc_vendor', (array) $user->roles ) ))){
                    echo '<h2 class="small-heading tomc-book-organization--red-text">Please log in</h2><p class="tomc-book-organization--red-text">Only logged in authors can purchase ISBN registration services.</p>';
                }
                echo '<div id="tomcIsbnInfoFieldsDiv"><h2 class="small-heading">' . __('ISBN Book Information') . '</h2><p>Each ISBN can only be used for one book in one format (such as ebook or audiobook).</p>';
                woocommerce_form_field('tomc_isbn_product', array(
                    'type' => 'select',
                    'class' => array(
                        'form-row-wide'
                    ),
                    'label' => __('select your book and format'),
                    'required'    => true,
                    'options'     => $productsArr,
                ),
                $checkout->get_value('tomc_isbn_product'));
                echo '</div>';
            }
        }
    }

    function validateIsbnInfo(){
        if ($this->cartContainsISBN){
            if (!$_POST['tomc_isbn_product']) wc_add_notice(__('You must choose a book format if you are purchasing an ISBN registration service. ') , 'error');
        }
    }

    function isbnInfoUpdateMeta($order_id){
        if (!empty($_POST['tomc_isbn_product'])) {
            update_post_meta($order_id, 'ISBN Product ID',sanitize_text_field($_POST['tomc_isbn_product']));  
        }
    }
}

$tomcBookISBNPlugin = new TOMCBookISBNPlugin();