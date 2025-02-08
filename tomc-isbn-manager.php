<?php
/* 
    Plugin Name: TOMC ISBN Manager
    Version: 1.0
    Author: Joanna
    Description: Track ISBN Numbers for our members
*/

if( ! defined('ABSPATH') ) exit;
require_once plugin_dir_path(__FILE__) . 'inc/tomc-isbn-route.php';

class TOMCBookISBNPlugin {
    function __construct() {
        global $wpdb;         
        $this->charset = $wpdb->get_charset_collate();
        $this->isbn_field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $this->isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $this->isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $this->users_table = $wpdb->prefix . "users";
        $this->posts_table = $wpdb->prefix . "posts";

        wp_localize_script('tomc-isbn-js', 'tomcBookorgData', array(
            'root_url' => get_site_url()
        ));

        add_action('activate_tomc-isbn-manager/tomc-isbn-manager.php', array($this, 'onActivate'));
        //add_action('init', array($this, 'onActivate'));
        add_action('init', array($this, 'registerScripts'));
        add_action('wp_enqueue_scripts', array($this, 'pluginFiles'));
        add_filter('template_include', array($this, 'loadTemplate'), 99);
        add_action('woocommerce_payment_complete', array($this, 'assignIsbn'));
        add_action('woocommerce_thankyou', array($this, 'showIsbn'));
    }	

    function registerScripts(){
        wp_register_style('tomc_isbn_styles', plugins_url('css/tomc-isbn-styles.css', __FILE__), false, '1.0', 'all');
    }

    function pluginFiles(){
        wp_enqueue_style('tomc_isbn_styles');
        wp_enqueue_script('tomc-isbn-js', plugin_dir_url(__FILE__) . '/build/index.js', array('jquery'), '1.0', true);
        wp_localize_script('tomc-isbn-js', 'tomcIsbnData', array(
            'root_url' => get_site_url()
        ));
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

    function addMyISBNsPage() {
        $isbns_page = array(
            'post_title' => 'My ISBN Registrations',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 0,
            'post_type' => 'page'
        );
        wp_insert_post($isbns_page);
    }

    function onActivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_numbers_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            isbn varchar(20) NOT NULL,
            addeddate datetime NOT NULL,
            addedby bigint(20) unsigned NOT NULL,
            assignedto bigint(20) unsigned NULL,
            assigneddate datetime NULL,
            shoporderid bigint(20) unsigned,
            assignedproductid bigint(20) unsigned,
            UNIQUE (isbn),
            PRIMARY KEY  (id),
            FOREIGN KEY  (addedby) REFERENCES $this->users_table(id),
            FOREIGN KEY  (assignedto) REFERENCES $this->users_table(id),
            FOREIGN KEY  (shoporderid) REFERENCES $this->posts_table(id),
            FOREIGN KEY  (assignedproductid) REFERENCES $this->posts_table(id)
        ) $this->charset;");

        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_field_values_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            isbnid bigint(20) unsigned NOT NULL,
            fieldid bigint(20) unsigned NOT NULL,
            fieldlabel varchar(100) NOT NULL,
            fieldvalue varchar(4000),
            addedby bigint(20) unsigned NOT NULL,
            addeddate datetime NOT NULL,
            displayOrder tinyint(100),
            PRIMARY KEY  (id),
            FOREIGN KEY  (addedby) REFERENCES $this->users_table(id),
            FOREIGN KEY  (isbnid) REFERENCES $this->isbn_numbers_table(id)
        ) $this->charset;");
        
        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_records_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            isbnid bigint(20) unsigned NOT NULL,
            submittedDate datetime NOT NULL,
            processeddate datetime NULL,
            processedby bigint(20) unsigned NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (isbnid) REFERENCES $this->isbn_numbers_table(id),
            FOREIGN KEY  (processedby) REFERENCES $this->users_table(id)
        ) $this->charset;");

        if (post_exists('ISBN Records', '', '', 'page', 'publish') == ''){
            $this->addISBNRecordsPage();
        }

        if (post_exists('My ISBN Registrations', '', '', 'page', 'publish') == ''){
            $this->addMyISBNsPage();
        }
    }

    function loadTemplate($template){
        if (is_page('isbn-records')){
            return plugin_dir_path(__FILE__) . 'inc/template-isbn-records.php';
        } else if (is_page('my-isbn-registrations')){
            return plugin_dir_path(__FILE__) . 'inc/template-my-isbns.php';
        }  else
        return $template;
    }

    //fix this
    function assignIsbn($order_id){
        global $wpdb;
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $user = wp_get_current_user();
        $userId = $user->ID;
        foreach($items as $item){
            if ($item->get_name() == 'ISBN Registration'){
                $query = 'select isbn from %i where assignedto is null and assigneddate is null and shoporderid is null order by addeddate limit 1';
                $item_count = $item['qty'];
                if ($item_count){
                    for($i = 0; $i < $item_count; $i++) {
                        $isbn = $wpdb->get_results($wpdb->prepare($query, $this->isbn_numbers_table), ARRAY_A);
                        $wpdb->update($this->isbn_numbers_table,  
                        array(
                            'assigneddate' => date('Y-m-d H:i:s'),
                            'assignedto' => $userId,
                            'shoporderid' => $order_id
                        ), 
                        array(
                            'ISBN' => $isbn[0]['isbn']
                        ));
                    }
                    break;
                } else {
                    $isbn = $wpdb->get_results($wpdb->prepare($query, $this->isbn_numbers_table), ARRAY_A);
                    $wpdb->update($this->isbn_numbers_table,  
                    array(
                        'assigneddate' => date('Y-m-d H:i:s'),
                        'assignedto' => $userId,
                        'shoporderid' => $order_id
                    ), 
                    array(
                        'ISBN' => $isbn[0]['isbn']
                    ));
                }
                
            }
        }
    }

    function showIsbn($order_id){
        global $wpdb;
        $isbn_product;
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $user = wp_get_current_user();
        $userId = $user->ID;
        foreach($items as $item){
            if ($item->get_name() == 'ISBN Registration'){
                $query = 'select isbn from %i where shoporderid = %d and assignedto = %d order by assigneddate desc';
                $isbn = $wpdb->get_results($wpdb->prepare($query, $this->isbn_numbers_table, $order_id, $userId), ARRAY_A);
                if (($isbn) && count($isbn) > 0){
                    for ($i = 0; $i < count($isbn); $i++){
                        ?><h2>Your new ISBN is <?php echo $isbn[$i]['isbn'] ?>.</h2>
                    <?php }
                    ?><p>You can view all ISBNs you've obtained through TOMC, as well as their current registration status, by visiting the <a href="<?php echo esc_url(site_url('/my-isbns'));?>">ISBN registration dashboard</a>.</p>
                <?php }
                break;
            }
        }
    }
}

$tomcBookISBNPlugin = new TOMCBookISBNPlugin();