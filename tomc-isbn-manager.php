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

        wp_localize_script('tomc-bookorg-js', 'tomcBookorgData', array(
            'root_url' => get_site_url()
        ));

        add_action('activate_tomc-isbn-manager/tomc-isbn-manager.php', array($this, 'onActivate'));
        add_action('init', array($this, 'registerScripts'));
        add_action('wp_enqueue_scripts', array($this, 'pluginFiles'));
        add_filter('template_include', array($this, 'loadTemplate'), 99);

        add_action('woocommerce_after_order_notes', array($this, 'isbnProduct'));
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

    function isbnProduct($checkout){
        $cart = WC()->cart->get_cart(); 
        foreach( $cart as $cart_item_key => $cart_item ){  
            $product = $cart_item['data'];
            if ($product->get_name() == 'ISBN'){
                echo '<div id="tomcIsbnProductDiv"><h2>' . __('Which product will you be using your new ISBN for?') . '</h2>';
                woocommerce_form_field('tomc_isbn_product', array(
                    'type' => 'text',
                    'class' => array(
                        'form-row-wide'
                    ),
                    'label' => __('select your product'),
                ),
                $checkout->get_value('tomc_isbn_product'));
                echo '</div>';
            }
        }
    }
}

$tomcBookISBNPlugin = new TOMCBookISBNPlugin();