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
        $this->isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $this->isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $this->users_table = $wpdb->prefix . "users";
        $this->posts_table = $wpdb->prefix . "posts";
        $this->meta_table = $wpdb->prefix . "postmeta";
        $this->book_products_table = $wpdb->prefix . "tomc_book_products";
        $this->books_table = $wpdb->prefix .  "tomc_books";
        $this->book_products_table = $wpdb->prefix . "tomc_book_products";

        $this->cartContainsISBN = false;

        wp_localize_script('tomc-isbn-js', 'tomcBookorgData', array(
            'root_url' => get_site_url()
        ));

        add_action('activate_tomc-isbn-manager/tomc-isbn-manager.php', array($this, 'onActivate'));
        //add_action('init', array($this, 'onActivate'));
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

    function MyISBNs() {
        $isbns_page = array(
            'post_title' => 'My ISBNs',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 0,
            'post_type' => 'page'
        );
        wp_insert_post($isbns_page);
    }

    function onActivate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_records_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            isbnproductid bigint(20) unsigned NOT NULL,
            processeddate datetime NULL,
            processedby bigint(20) unsigned NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (isbnproductid) REFERENCES $this->posts_table(id),
            FOREIGN KEY  (processedby) REFERENCES $this->users_table(id)
        ) $this->charset;");

        dbDelta("CREATE TABLE IF NOT EXISTS $this->isbn_numbers_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            isbn varchar(20) NOT NULL,
            assignedproductid bigint(20) unsigned NULL,
            assigneddate datetime NULL,
            addeddate datetime NOT NULL,
            addedby bigint(20) unsigned NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (assignedproductid) REFERENCES $this->posts_table(id),
            FOREIGN KEY  (addedby) REFERENCES $this->users_table(id)
        ) $this->charset;");

        if (post_exists('ISBN Records', '', '', 'page', 'publish') == ''){
            $this->addISBNRecordsPage();
        }

        if (post_exists('My ISBNs', '', '', 'page', 'publish') == ''){
            $this->addMyISBNsPage();
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
                if (!(is_user_logged_in() && (in_array( 'dc_vendor', (array) $user->roles ) ))){
                    echo '<h2 class="small-heading tomc-book-organization--red-text">Please log in</h2><p class="tomc-book-organization--red-text">Only logged in authors can purchase ISBN registration services.</p>';
                } else {
                    $query = 'select a.id, a.post_title from %i a where a.post_type = %s and a.post_status = %s and a.post_author = %d order by a.post_title';
                    $products = $wpdb->get_results($wpdb->prepare($query, $this->posts_table, 'product', 'publish', $userId), ARRAY_A);
                    $productsArr = [];
                    for($i = 0; $i < count($products); $i++){
                        $productsArr[$products[$i]['id']] = $products[$i]['post_title'];
                    }
                    echo '<div id="tomcIsbnInfoFieldsDiv"><h2 class="small-heading">' . __('ISBN Book Information') . '</h2><p>Each ISBN can only be used for one book in one format (such as ebook or audiobook).</p>';
                    woocommerce_form_field('tomc_isbn_product', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __('Select your book and format.'),
                        'required'    => true,
                        'options'     => $productsArr,
                        'id' => 'tomc_isbn_product'
                    ),
                    $checkout->get_value('tomc_isbn_product'));
                    woocommerce_form_field('tomc_isbn_title', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's title."),
                        'required'    => true,
                        'id' => 'tomc_isbn_title'
                    ),
                    $checkout->get_value('tomc_isbn_title'));
                    woocommerce_form_field('tomc_isbn_subtitle', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's subtitle."),
                        'required'    => false,
                        'id' => 'tomc_isbn_subtitle'
                    ),
                    $checkout->get_value('tomc_isbn_subtitle'));
                    woocommerce_form_field('tomc_isbn_description', array(
                        'type' => 'textarea',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's description (up to 350 words)."),
                        'required'    => true,
                        'id' => 'tomc_isbn_description'
                    ),
                    $checkout->get_value('tomc_isbn_description'));
                    woocommerce_form_field('tomc_isbn_format', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Select your book's format."),
                        'required'    => true,
                        'options' => array(
                            'audiobook' => __('audiobook'),
                            'ebook' => __('ebook')
                        ),
                        'id' => 'tomc_isbn_format'
                    ),
                    $checkout->get_value('tomc_isbn_format'));
                    woocommerce_form_field('tomc_isbn_first_genre', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Select your book's first genre. (Note: this can be different from the genres you use for the Trunk of My Car search and browse features.)"),
                        'required'    => true,
                        'options' => array(
                            'nonfiction_agriculture' => __('Agriculture (Nonfiction)'),
                            'nonfiction_architecture' => __('Architecture (Nonfiction)'),
                            'nonfiction_art' => __('Art (Nonfiction)'),
                            'nonfiction_astrology' => __('Astrology (Nonfiction)'),
                            'nonfiction_bible_commentaries' => __('Bible Commentaries (Nonfiction)'),
                            'nonfiction_biography' => __('Biography (Nonfiction)'),
                            'nonfiction_business' => __('Business (Nonfiction)'),
                            'nonfiction_collectors_and_collecting' => __('Collectors and Collecting (Nonfiction)'),
                            'nonfiction_computer_software' => __('Computer Software (Nonfiction)'),
                            'nonfiction_computers' => __('Computers (Nonfiction)'),
                            'nonfiction_cooking' => __('Cooking (Nonfiction)'),
                            'nonfiction_crime' => __('Crime (Nonfiction)'),
                            'nonfiction_curiosities_and_wonders' => __('Curiosities and Wonders (Nonfiction)'),
                            'nonfiction_drama_history_and_criticism' => __('Drama History and Criticism (Nonfiction)'),
                            'nonfiction_economics' => __('Economics (Nonfiction)'),
                            'nonfiction_education' => __('Education (Nonfiction)'),
                            'nonfiction_family' => __('Family (Nonfiction)'),
                            'nonfiction_gardening' => __('Gardening (Nonfiction)'),
                            'nonfiction_geneology' => __('Geneology (Nonfiction)'),
                            'nonfiction_handicraft' => __('Handicraft (Nonfiction)'),
                            'nonfiction_health' => __('Health (Nonfiction)'),
                            'nonfiction_interior_decorating' => __('Interior Decorating (Nonfiction)'),
                            'nonfiction_internet' => __('Internet (Nonfiction)'),
                            'nonfiction_interpersonal_relationships' => __('Interpersonal Relationships (Nonfiction)'),
                            'nonfiction_language_and_languages' => __('Language and Languages (Nonfiction)'),
                            'nonfiction_language_arts' => __('Language Arts (Nonfiction)'),
                            'nonfiction_law' => __('Law (Nonfiction)'),
                            'nonfiction_literature_history_and_criticism' => __('Literary History and Criticism (Nonfiction)'),
                            'nonfiction_mathematics' => __('Mathematics (Nonfiction)'),
                            'nonfiction_medicine' => __('Medicine (Nonfiction)'),
                            'nonfiction_military_art_and_science' => __('Military Art and Science (Nonfiction)'),
                            'nonfiction_mind_and_body' => __('Mind and Body (Nonfiction)'),
                            'nonfiction_music' => __('Music (Nonfiction)'),
                            'nonfiction_nature' => __('Nature (Nonfiction)'),
                            'nonfiction_nutrition' => __('Nutrition (Nonfiction)'),
                            'nonfiction_parenting' => __('Parenting (Nonfiction)'),
                            'nonfiction_performing_arts' => __('Performing Arts (Nonfiction)'),
                            'nonfiction_personal_finance' => __('Personal Finance (Nonfiction)'),
                            'nonfiction_pets' => __('Pets (Nonfiction)'),
                            'nonfiction_philosophy' => __('Philosophy (Nonfiction)'),
                            'nonfiction_physical_fitness' => __('Physical Fitness (Nonfiction)'),
                            'nonfiction_physics' => __('Physics (Nonfiction)'),
                            'nonfiction_poetry_history_and_criticism' => __('Poetry History and Criticism (Nonfiction)'),
                            'nonfiction_political_science' => __('Political Science (Nonfiction)'),
                            'nonfiction_psychology' => __('Psychology (Nonfiction)'),
                            'nonfiction_reference_books' => __('Reference Books (Nonfiction)'),
                            'nonfiction_science' => __('Science (Nonfiction)'),
                            'nonfiction_self_help_techniques' => __('Self-Help Techniques (Nonfiction)'),
                            'nonfiction_social_sciences' => __('Social Sciences (Nonfiction)'),
                            'nonfiction_sports' => __('Sports (Nonfiction)'),
                            'nonfiction_technology' => __('Technology (Nonfiction)'),
                            'nonfiction_transportation' => __('Transportation (Nonfiction)'),
                            'nonfiction_travel' => __('Travel (Nonfiction)'),
                            'nonfiction_world_history' => __('World History (Nonfiction)'),

                            'childrens_fiction' => __("Children's Fiction"),
                            'comics_and_graphic_novels' => __('Comics and Graphic Novels'),
                            'poetry' => __("Poetry from One Author"),

                            'drama' => __('Dramatic Works from One Author'),
                            'games' => __('Games'),
                            'photography' => __('Photography'),
                            'religion' => __("Religion"),
                            'spirituality' => __("Spirituality"),
                            'wit_and_humor' => __("Wit and Humor"),

                            'fiction_action_and_adventure' => __('Action and Adventure (Fiction)'),
                            'fiction_erotica' => __('Erotica (Fiction)'),
                            'fiction_espionage' => __('Espionage (Fiction)'),
                            'fiction_fantasy' => __('Fantasy (Fiction)'),
                            'fiction_gay' => __('Gay (Fiction)'),
                            'fiction_general' => __('General (Fiction)'),
                            'fiction_historical' => __('Historical (Fiction)'),
                            'fiction_horror' => __('Horror (Fiction)'),
                            'fiction_mystery_and_detective' => __('Mystery and Detective (Fiction)'),
                            'fiction_psychological' => __('Psychological (Fiction)'),
                            'fiction_religious' => __('Religious (Fiction)'),
                            'fiction_romance' => __('Romance (Fiction)'),
                            'fiction_science_fiction' => __('Science Fiction'),
                            'fiction_short_stories' => __('Short Story Collections from One Author (Fiction)'),
                            'fiction_suspense' => __('Suspense (Fiction)'),
                            'fiction_thrillers' => __('Thrillers (Fiction)'),
                            'fiction_visionary_and_metaphysical' => __('Visionary and Metaphysical Fiction'),
                            'fiction_war_and_military' => __('War and Military (Fiction)'),
                            'fiction_westerns' => __('Westerns (Fiction)'),
                        ),
                        'default' => 'fiction_general'
                    ),
                    $checkout->get_value('tomc_isbn_first_genre'));
                    woocommerce_form_field('tomc_isbn_second_genre', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Select your book's second genre. (Note: this can be different from the genres you use for the Trunk of My Car search and browse features.)"),
                        'required'    => false,
                        'options' => array(
                            'none' => '',
                            'nonfiction_agriculture' => __('Agriculture (Nonfiction)'),
                            'nonfiction_architecture' => __('Architecture (Nonfiction)'),
                            'nonfiction_art' => __('Art (Nonfiction)'),
                            'nonfiction_astrology' => __('Astrology (Nonfiction)'),
                            'nonfiction_bible_commentaries' => __('Bible Commentaries (Nonfiction)'),
                            'nonfiction_biography' => __('Biography (Nonfiction)'),
                            'nonfiction_business' => __('Business (Nonfiction)'),
                            'nonfiction_collectors_and_collecting' => __('Collectors and Collecting (Nonfiction)'),
                            'nonfiction_computer_software' => __('Computer Software (Nonfiction)'),
                            'nonfiction_computers' => __('Computers (Nonfiction)'),
                            'nonfiction_cooking' => __('Cooking (Nonfiction)'),
                            'nonfiction_crime' => __('Crime (Nonfiction)'),
                            'nonfiction_curiosities_and_wonders' => __('Curiosities and Wonders (Nonfiction)'),
                            'nonfiction_drama_history_and_criticism' => __('Drama History and Criticism (Nonfiction)'),
                            'nonfiction_economics' => __('Economics (Nonfiction)'),
                            'nonfiction_education' => __('Education (Nonfiction)'),
                            'nonfiction_family' => __('Family (Nonfiction)'),
                            'nonfiction_gardening' => __('Gardening (Nonfiction)'),
                            'nonfiction_geneology' => __('Geneology (Nonfiction)'),
                            'nonfiction_handicraft' => __('Handicraft (Nonfiction)'),
                            'nonfiction_health' => __('Health (Nonfiction)'),
                            'nonfiction_interior_decorating' => __('Interior Decorating (Nonfiction)'),
                            'nonfiction_internet' => __('Internet (Nonfiction)'),
                            'nonfiction_interpersonal_relationships' => __('Interpersonal Relationships (Nonfiction)'),
                            'nonfiction_language_and_languages' => __('Language and Languages (Nonfiction)'),
                            'nonfiction_language_arts' => __('Language Arts (Nonfiction)'),
                            'nonfiction_law' => __('Law (Nonfiction)'),
                            'nonfiction_literature_history_and_criticism' => __('Literary History and Criticism (Nonfiction)'),
                            'nonfiction_mathematics' => __('Mathematics (Nonfiction)'),
                            'nonfiction_medicine' => __('Medicine (Nonfiction)'),
                            'nonfiction_military_art_and_science' => __('Military Art and Science (Nonfiction)'),
                            'nonfiction_mind_and_body' => __('Mind and Body (Nonfiction)'),
                            'nonfiction_music' => __('Music (Nonfiction)'),
                            'nonfiction_nature' => __('Nature (Nonfiction)'),
                            'nonfiction_nutrition' => __('Nutrition (Nonfiction)'),
                            'nonfiction_parenting' => __('Parenting (Nonfiction)'),
                            'nonfiction_performing_arts' => __('Performing Arts (Nonfiction)'),
                            'nonfiction_personal_finance' => __('Personal Finance (Nonfiction)'),
                            'nonfiction_pets' => __('Pets (Nonfiction)'),
                            'nonfiction_philosophy' => __('Philosophy (Nonfiction)'),
                            'nonfiction_physical_fitness' => __('Physical Fitness (Nonfiction)'),
                            'nonfiction_physics' => __('Physics (Nonfiction)'),
                            'nonfiction_poetry_history_and_criticism' => __('Poetry History and Criticism (Nonfiction)'),
                            'nonfiction_political_science' => __('Political Science (Nonfiction)'),
                            'nonfiction_psychology' => __('Psychology (Nonfiction)'),
                            'nonfiction_reference_books' => __('Reference Books (Nonfiction)'),
                            'nonfiction_science' => __('Science (Nonfiction)'),
                            'nonfiction_self_help_techniques' => __('Self-Help Techniques (Nonfiction)'),
                            'nonfiction_social_sciences' => __('Social Sciences (Nonfiction)'),
                            'nonfiction_sports' => __('Sports (Nonfiction)'),
                            'nonfiction_technology' => __('Technology (Nonfiction)'),
                            'nonfiction_transportation' => __('Transportation (Nonfiction)'),
                            'nonfiction_travel' => __('Travel (Nonfiction)'),
                            'nonfiction_world_history' => __('World History (Nonfiction)'),

                            'childrens_fiction' => __("Children's Fiction"),
                            'comics_and_graphic_novels' => __('Comics and Graphic Novels'),
                            'poetry' => __("Poetry from One Author"),

                            'drama' => __('Dramatic Works from One Author'),
                            'games' => __('Games'),
                            'photography' => __('Photography'),
                            'religion' => __("Religion"),
                            'spirituality' => __("Spirituality"),
                            'wit_and_humor' => __("Wit and Humor"),

                            'fiction_action_and_adventure' => __('Action and Adventure (Fiction)'),
                            'fiction_erotica' => __('Erotica (Fiction)'),
                            'fiction_espionage' => __('Espionage (Fiction)'),
                            'fiction_fantasy' => __('Fantasy (Fiction)'),
                            'fiction_gay' => __('Gay (Fiction)'),
                            'fiction_general' => __('General (Fiction)'),
                            'fiction_historical' => __('Historical (Fiction)'),
                            'fiction_horror' => __('Horror (Fiction)'),
                            'fiction_mystery_and_detective' => __('Mystery and Detective (Fiction)'),
                            'fiction_psychological' => __('Psychological (Fiction)'),
                            'fiction_religious' => __('Religious (Fiction)'),
                            'fiction_romance' => __('Romance (Fiction)'),
                            'fiction_science_fiction' => __('Science Fiction'),
                            'fiction_short_stories' => __('Short Story Collections from One Author (Fiction)'),
                            'fiction_suspense' => __('Suspense (Fiction)'),
                            'fiction_thrillers' => __('Thrillers (Fiction)'),
                            'fiction_visionary_and_metaphysical' => __('Visionary and Metaphysical Fiction'),
                            'fiction_war_and_military' => __('War and Military (Fiction)'),
                            'fiction_westerns' => __('Westerns (Fiction)'),
                        ),
                        'default' => ''
                    ),
                    $checkout->get_value('tomc_isbn_second_genre'));
                    woocommerce_form_field('tomc_isbn_contributor1', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What name did you publish this book under?"),
                        'required'    => true,
                        'id' => 'tomc_isbn_contributor1'
                    ),
                    $checkout->get_value('tomc_isbn_contributor1'));
                    woocommerce_form_field('tomc_isbn_function1', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What is your relationship to this work?"),
                        'required'    => true,
                        'options' => array(
                            'author' => __('Author')
                        ),
                        'default' => 'author',
                        'id' => 'tomc_isbn_function1'
                    ),
                    $checkout->get_value('tomc_isbn_function1'));
                    woocommerce_form_field('tomc_isbn_biography1', array(
                        'type' => 'textarea',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your biography (up to 350 words)."),
                        'required'    => true,
                        'id' => 'tomc_isbn_biography1'
                    ),
                    $checkout->get_value('tomc_isbn_biography1'));
                    woocommerce_form_field('tomc_isbn_contributor2', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Add the name of someone who contributed to your book."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_contributor2'));
                    woocommerce_form_field('tomc_isbn_function2', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What is their relationship to this work?"),
                        'required'    => false,
                        'options' => array(
                            'appendix_by' => __('Appendix by'),
                            'artist' => __('Artist'),
                            'assisted_by' => __('Assisted by'),
                            'author' => __('Author'),
                            'continued_by' => __('Continued by'),
                            'cover_design_by' => __('Cover Design by'),
                            'editor' => __('Editor'),
                            'epilogue_by' => __('Epilogue by'),
                            'footnotes_by' => __('Footnotes by'),
                            'forward_by' => __('Forward by'),
                            'historical_advisor' => __('Historical Advisor'),
                            'illustrator' => __('Illustrator'),
                            'interviewee' => __('Interviewee'),
                            'interviewer' => __('Interviewer'),
                            'introduction_by' => __('Introduction by'),
                            'notes_by' => __('Notes by'),
                            'performed_by' => __('Performed by'),
                            'photographer' => __('Photographer'),
                            'preface_by' => __('Preface by'),
                            'prologue_by' => __('Prologue by'),
                            'research_by' => __('Retold by'),
                            'reviewed_by' => __('Reviewed by'),
                            'scientific_editor' => __('Scientific Editor'),
                            'software_by' => __('Software by'),
                            'technical_editor' => __('Technical Editor'),
                            'thesis_advisor_or_supervisor' => __('Thesis Advisor or Supervisor'),
                            'transcribed_by' => __('Transcribed by'),
                            'translated_by' => __('Translated by')
                        )
                    ),
                    $checkout->get_value('tomc_isbn_function2'));
                    woocommerce_form_field('tomc_isbn_biography2', array(
                        'type' => 'textarea',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter their biography (up to 350 words)."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_biography2'));
                    woocommerce_form_field('tomc_isbn_contributor3', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Add the name of someone else who contributed to your book."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_contributor3'));
                    woocommerce_form_field('tomc_isbn_function3', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What is their relationship to this work?"),
                        'required'    => false,
                        'options' => array(
                            'appendix_by' => __('Appendix by'),
                            'artist' => __('Artist'),
                            'assisted_by' => __('Assisted by'),
                            'author' => __('Author'),
                            'continued_by' => __('Continued by'),
                            'cover_design_by' => __('Cover Design by'),
                            'editor' => __('Editor'),
                            'epilogue_by' => __('Epilogue by'),
                            'footnotes_by' => __('Footnotes by'),
                            'forward_by' => __('Forward by'),
                            'historical_advisor' => __('Historical Advisor'),
                            'illustrator' => __('Illustrator'),
                            'interviewee' => __('Interviewee'),
                            'interviewer' => __('Interviewer'),
                            'introduction_by' => __('Introduction by'),
                            'notes_by' => __('Notes by'),
                            'performed_by' => __('Performed by'),
                            'photographer' => __('Photographer'),
                            'preface_by' => __('Preface by'),
                            'prologue_by' => __('Prologue by'),
                            'research_by' => __('Retold by'),
                            'reviewed_by' => __('Reviewed by'),
                            'scientific_editor' => __('Scientific Editor'),
                            'software_by' => __('Software by'),
                            'technical_editor' => __('Technical Editor'),
                            'thesis_advisor_or_supervisor' => __('Thesis Advisor or Supervisor'),
                            'transcribed_by' => __('Transcribed by'),
                            'translated_by' => __('Translated by')
                        )
                    ),
                    $checkout->get_value('tomc_isbn_function3'));
                    woocommerce_form_field('tomc_isbn_biography3', array(
                        'type' => 'textarea',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter their biography (up to 350 words)."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_biography3'));
                    woocommerce_form_field('tomc_isbn_publication_date', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's publication date (for this format--please use 'm/d/yyyy' format)."),
                        'required'    => true,
                        'id' => 'tomc_isbn_publication_date'
                    ),
                    $checkout->get_value('tomc_isbn_publication_date'));
                    woocommerce_form_field('tomc_isbn_status', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What is the publication status of this book?"),
                        'required'    => true,
                        'options' => array(
                            'status_active' => __('Active Record'),
                            'status_forthcoming' => __('Forthcoming')
                        ),
                        'default' => 'status_active',
                        'id' => 'tomc_isbn_status'
                    ),
                    $checkout->get_value('tomc_isbn_status'));
                    woocommerce_form_field('tomc_isbn_target_audience', array(
                        'type' => 'select',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("What is the target audience for this book? (Note: if your book is intended for a general adult audience, choose 'trade'.)"),
                        'required'    => true,
                        'options' => array(
                            'status_adult_education' => __('Adult Education'),
                            'status_college_audience' => __('College Audience'),
                            'status_elementary_high_school' => __('Elementary/High School'),
                            'status_english_as_second_language' => __('English as Second Language'),
                            'status_family' => __('Family'),
                            'status_juvenile_audience' => __('Juvenile Audience'),
                            'status_lower_secondary_education' => __('Lower Secondary Education'),
                            'status_pre_primary_education' => __('Pre-Primary Education'),
                            'status_scholarly_and_professional' => __('Scholarly and Professional'),
                            'status_second_language_teaching' => __('Second Language Teaching'),
                            'status_trade' => __('Trade'),
                            'status_upper_secondary_education' => __('Upper Secondary Education'),
                            'status_young_adult_audience' => __('Young Adult Audience'),
                        ),
                        'default' => 'status_trade',
                        'id' => 'tomc_isbn_target_audience'
                    ),
                    $checkout->get_value('tomc_isbn_target_audience'));
                    woocommerce_form_field('tomc_isbn_book_price', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's current price (for this format--it's okay if the price changes later)."),
                        'required'    => true,
                        'id' => 'tomc_isbn_book_price'
                    ),
                    $checkout->get_value('tomc_isbn_book_price'));
                    woocommerce_form_field('tomc_isbn_book_language', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's primary language."),
                        'required'    => false,
                        'id' => 'tomc_isbn_book_language'
                    ),
                    $checkout->get_value('tomc_isbn_book_language'));
                    woocommerce_form_field('tomc_isbn_copyright_year', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's copyright year."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_copyright_year'));
                    woocommerce_form_field('tomc_isbn_control_number', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your Library of Congress Control Number."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_control_number'));
                    woocommerce_form_field('tomc_isbn_translated_title', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter your book's translated title."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_translated_title'));
                    woocommerce_form_field('tomc_isbn_number_of_pages', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter the number of pages in your book."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_number_of_pages'));
                    woocommerce_form_field('tomc_isbn_number_of_illustrations', array(
                        'type' => 'text',
                        'class' => array(
                            'form-row-wide'
                        ),
                        'label' => __("Enter the number of illustrations in your book."),
                        'required'    => false
                    ),
                    $checkout->get_value('tomc_isbn_number_of_illustrations'));

                    echo '</div>';
                }
            } else {
                echo $item->get_name();
            }
        }
    }

    function validateIsbnInfo(){
        if ($this->cartContainsISBN){
            if ((!$_POST['tomc_isbn_product']) || $_POST['tomc_isbn_product'] == '') wc_add_notice(__("You must select a product you've uploaded if you are purchasing an ISBN registration service. ") , 'error');
            if (!$_POST['tomc_isbn_title']) wc_add_notice(__('You must enter a book title if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_description']) wc_add_notice(__('You must enter a book description if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_format']) wc_add_notice(__('You must select a book format if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_first_genre']) wc_add_notice(__("You must select one of Bowker's genres if you are purchasing an ISBN registration service. ") , 'error');
            if (!$_POST['tomc_isbn_contributor1']) wc_add_notice(__('You must enter the name you published your book under if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_biography1']) wc_add_notice(__('You must enter a biography if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_function1']) wc_add_notice(__('You must select your relationship to the work if you are purchasing an ISBN registration service. ') , 'error');
            if (!$_POST['tomc_isbn_publication_date']) wc_add_notice(__("You must enter your book's publication date if you are purchasing an ISBN registration service. ") , 'error');
            if (!$_POST['tomc_isbn_status']) wc_add_notice(__("You must enter your book's publication status if you are purchasing an ISBN registration service. ") , 'error');
            if (!$_POST['tomc_isbn_target_audience']) wc_add_notice(__("You must enter your book's target audience if you are purchasing an ISBN registration service. ") , 'error');
            if (!$_POST['tomc_isbn_book_price']) wc_add_notice(__("You must enter your book's current price if you are purchasing an ISBN registration service. ") , 'error');
        }
    }

    function isbnInfoUpdateMeta($order_id){
        if ((!empty($_POST['tomc_isbn_product'])) && ($_POST['tomc_isbn_product'] != 0)) {
            update_post_meta($order_id, 'tomc_isbn_product',sanitize_text_field($_POST['tomc_isbn_product']));
        }
        if (!empty($_POST['tomc_isbn_title'])) {
            update_post_meta($order_id, 'tomc_isbn_title',sanitize_text_field($_POST['tomc_isbn_title']));
        }
        if (!empty($_POST['tomc_isbn_subtitle'])) {
            update_post_meta($order_id, 'tomc_isbn_subtitle',sanitize_text_field($_POST['tomc_isbn_subtitle']));
        }
        if (!empty($_POST['tomc_isbn_description'])) {
            update_post_meta($order_id, 'tomc_isbn_description',sanitize_text_field($_POST['tomc_isbn_description']));
        }
        if (!empty($_POST['tomc_isbn_format'])) {
            update_post_meta($order_id, 'tomc_isbn_format',sanitize_text_field($_POST['tomc_isbn_format']));
        }    
        if (!empty($_POST['tomc_isbn_first_genre'])) {
            update_post_meta($order_id, 'tomc_isbn_first_genre',sanitize_text_field($_POST['tomc_isbn_first_genre']));
        }    
        if (!empty($_POST['tomc_isbn_second_genre'])) {
            update_post_meta($order_id, 'tomc_isbn_second_genre',sanitize_text_field($_POST['tomc_isbn_second_genre']));
        }  
        if (!empty($_POST['tomc_isbn_contributor1'])) {
            update_post_meta($order_id, 'tomc_isbn_contributor1',sanitize_text_field($_POST['tomc_isbn_contributor1']));
        }  
        if (!empty($_POST['tomc_isbn_biography1'])) {
            update_post_meta($order_id, 'tomc_isbn_biography1',sanitize_text_field($_POST['tomc_isbn_biography1']));
        }    
        if (!empty($_POST['tomc_isbn_function1'])) {
            update_post_meta($order_id, 'tomc_isbn_function1',sanitize_text_field($_POST['tomc_isbn_function1']));
        }  
        if (!empty($_POST['tomc_isbn_contributor2'])) {
            update_post_meta($order_id, 'tomc_isbn_contributor1',sanitize_text_field($_POST['tomc_isbn_contributor1']));
        }  
        if (!empty($_POST['tomc_isbn_biography2'])) {
            update_post_meta($order_id, 'tomc_isbn_biography1',sanitize_text_field($_POST['tomc_isbn_biography1']));
        }    
        if (!empty($_POST['tomc_isbn_function2'])) {
            update_post_meta($order_id, 'tomc_isbn_function1',sanitize_text_field($_POST['tomc_isbn_function1']));
        }
        if (!empty($_POST['tomc_isbn_contributor3'])) {
            update_post_meta($order_id, 'tomc_isbn_contributor1',sanitize_text_field($_POST['tomc_isbn_contributor1']));
        }  
        if (!empty($_POST['tomc_isbn_biography3'])) {
            update_post_meta($order_id, 'tomc_isbn_biography1',sanitize_text_field($_POST['tomc_isbn_biography1']));
        }    
        if (!empty($_POST['tomc_isbn_function3'])) {
            update_post_meta($order_id, 'tomc_isbn_function1',sanitize_text_field($_POST['tomc_isbn_function1']));
        }  
        if (!empty($_POST['tomc_isbn_publication_date'])) {
            update_post_meta($order_id, 'tomc_isbn_publication_date',sanitize_text_field($_POST['tomc_isbn_publication_date']));
        }    
        if (!empty($_POST['tomc_isbn_status'])) {
            update_post_meta($order_id, 'tomc_isbn_status',sanitize_text_field($_POST['tomc_isbn_status']));
        }    
        if (!empty($_POST['tomc_isbn_target_audience'])) {
            update_post_meta($order_id, 'tomc_isbn_target_audience',sanitize_text_field($_POST['tomc_isbn_target_audience']));
        }    
        if (!empty($_POST['tomc_isbn_book_price'])) {
            update_post_meta($order_id, 'tomc_isbn_book_price',sanitize_text_field($_POST['tomc_isbn_book_price']));
        }    
        if (!empty($_POST['tomc_isbn_book_language'])) {
            update_post_meta($order_id, 'tomc_isbn_book_language',sanitize_text_field($_POST['tomc_isbn_book_language']));
        }    
        if (!empty($_POST['tomc_isbn_copyright_year'])) {
            update_post_meta($order_id, 'tomc_isbn_copyright_year',sanitize_text_field($_POST['tomc_isbn_copyright_year']));
        }    
        if (!empty($_POST['tomc_isbn_control_number'])) {
            update_post_meta($order_id, 'tomc_isbn_control_number',sanitize_text_field($_POST['tomc_isbn_control_number']));
        }   
        if (!empty($_POST['tomc_isbn_translated_title'])) {
            update_post_meta($order_id, 'tomc_isbn_translated_title',sanitize_text_field($_POST['tomc_isbn_translated_title']));
        }    
        if (!empty($_POST['tomc_isbn_number_of_pages'])) {
            update_post_meta($order_id, 'tomc_isbn_number_of_pages',sanitize_text_field($_POST['tomc_isbn_number_of_pages']));
        }    
        if (!empty($_POST['tomc_isbn_number_of_illustrations'])) {
            update_post_meta($order_id, 'tomc_isbn_number_of_illustrations',sanitize_text_field($_POST['tomc_isbn_number_of_illustrations']));
        }    
    }
}

$tomcBookISBNPlugin = new TOMCBookISBNPlugin();