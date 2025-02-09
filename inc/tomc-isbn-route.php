<?php

add_action('rest_api_init', 'tomcIsbnRegisterRoute');

function tomcIsbnRegisterRoute() {
    register_rest_route('tomcISBN/v1', 'getUnfiledRecords', array(
        'methods' => 'GET',
        'callback' => 'getUnfiledRecords'
    ));
    register_rest_route('tomcISBN/v1', 'getFiledRecords', array(
        'methods' => 'GET',
        'callback' => 'getFiledRecords'
    ));
    register_rest_route('tomcISBN/v1', 'markRecordFiled', array(
        'methods' => 'POST',
        'callback' => 'markRecordFiled'
    ));
    register_rest_route('tomcISBN/v1', 'populateByProduct', array(
        'methods' => 'GET',
        'callback' => 'populateByProduct'
    ));
}

function populateByProduct($data){
    $productId = sanitize_text_field($data['productId']);
    $user = wp_get_current_user();
    if (is_user_logged_in()){
        global $wpdb;
        $books_table = $wpdb->prefix . "tomc_books";
        // $book_genres_table = $wpdb->prefix .  "tomc_book_genres";
        $book_languages_table = $wpdb->prefix . "tomc_book_languages";
        $languages_table = $wpdb->prefix . "tomc_publication_languages";
        $book_products_table = $wpdb->prefix . "tomc_book_products";
        $posts_table = $wpdb->prefix . "posts";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $product_types_table = $wpdb->prefix . "tomc_product_types";
        $pen_names_table = $wpdb->prefix . "tomc_pen_names_books";
        $users_table = $wpdb->prefix . "users";
        $usermeta_table = $wpdb->prefix . "usermeta";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = 'select IFNULL(books.title, posts.post_title) as title,
        books.subtitle,
        books.book_description as description,
        producttypes.type_name as format,
        IFNULL(authorposts.post_title, users.display_name) as contributor,
        IFNULL(authorposts.post_content, usermeta.meta_value) as biography,
        CONCAT(MONTH(books.createdate), "/", DAY(books.createdate), "/", YEAR(books.createdate)) as publicationdate0, 
        CONCAT(MONTH(posts.post_date), "/", DAY(posts.post_date), "/", YEAR(posts.post_date)) as publicationdate1,
        books.islive,
        postmeta.meta_value as price,
        languages.language_name as language
        from %i posts
        left join %i postmeta on posts.id = postmeta.post_id
        and postmeta.meta_key = "_price"
        join %i users on posts.post_author = users.id
        left join %i usermeta on users.id = usermeta.user_id
        left join %i bookproducts on posts.id = bookproducts.productid
        left join %i books on bookproducts.bookid = books.id
        left join %i producttypes on bookproducts.typeid = producttypes.id
        left join %i pennamesbooks on books.id = pennamesbooks.bookid
        left join %i authorposts on pennamesbooks.pennameid = authorposts.id
        left join %i booklanguages on books.id = booklanguages.bookid
        left join %i languages on booklanguages.languageid = languages.id
        where posts.id = %d
        limit 1';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $postmeta_table, $users_table, $usermeta_table, $book_products_table, $books_table, $product_types_table, $pen_names_table, $posts_table, $book_languages_table, $languages_table, $productId), ARRAY_A);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//redo to just update isbn_records table
function markRecordFiled($data){
    $recordId = sanitize_text_field($data['recordId']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        $userId = get_current_user_id();
        global $wpdb;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = '';
        $record = [];
        $record['shoporderid'] = $recordId;
        $record['processeddate'] = date('Y-m-d H:i:s');
        $record['processedby'] = $userId;
        $wpdb->insert($isbn_records_table, $record);
        $recordId = $wpdb->insert_id;
        return $recordId;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//will need to go through each field in select statement
function getUnfiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $isbn_field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $query = 'select * 
        from %i records
        join %i numbers on records.isbnid = numbers.id
        join %i fieldvalues on fieldvalues.isbnid = numbers.id
        
        where records.submitteddate is not null
        order by records.submitteddate';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $postmeta_table, $posts_table, $users_table, $postmeta_table, $isbn_records_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//update select statement
function getFiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $posts_table = $wpdb->prefix . "posts";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $query = 'select ';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $postmeta_table, $posts_table, $users_table, $postmeta_table, $isbn_records_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        // return $wpdb->prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//also need route to get status of filed records for users

//also need route to get fields to enter and save field values

//also need route to edit field values

//also need route to submit isbn info for filing
