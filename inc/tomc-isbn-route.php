<?php

add_action('rest_api_init', 'tomcIsbnRegisterRoute');

function tomcIsbnRegisterRoute() {
    register_rest_route('tomcISBN/v1', 'getMoreFiledRecords', array(
        'methods' => 'GET',
        'callback' => 'getMoreFiledRecords'
    ));
    register_rest_route('tomcISBN/v1', 'markRecordFiled', array(
        'methods' => 'POST',
        'callback' => 'markRecordFiled'
    ));
    register_rest_route('tomcISBN/v1', 'populateByProduct', array(
        'methods' => 'GET',
        'callback' => 'populateByProduct'
    ));
    register_rest_route('tomcISBN/v1', 'checkAssignedProduct', array(
        'methods' => 'GET',
        'callback' => 'checkAssignedProduct'
    ));
    register_rest_route('tomcISBN/v1', 'saveFieldValues', array(
        'methods' => 'POST',
        'callback' => 'saveFieldValues'
    ));
    register_rest_route('tomcISBN/v1', 'saveAndSubmitRecord', array(
        'methods' => 'POST',
        'callback' => 'saveAndSubmitRecord'
    ));
    register_rest_route('tomcISBN/v1', 'getFieldValues', array(
        'methods' => 'GET',
        'callback' => 'getFieldValues'
    ));
    register_rest_route('tomcISBN/v1', 'getInfoByISBN', array(
        'methods' => 'GET',
        'callback' => 'getInfoByISBN'
    ));
}

function getFieldValues($data){
    $isbn = sanitize_text_field($data['isbn']);
    $user = wp_get_current_user();
    if (is_user_logged_in()){
        global $wpdb;
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $userId = $user->ID;
        $query = 'select fv.fieldlabel, fv.fieldvalue 
        from %i numbers 
        join %i fv on numbers.id = fv.isbnid 
        where numbers.isbn = %d';
        $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $field_values_table, $isbn), ARRAY_A);
        return $results;
        // return $wpdb->prepare($query, $isbn_numbers_table, $field_values_table, $isbn);
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

function saveAndSubmitRecord($data){
    $fieldVals = json_decode(sanitize_text_field($data['fieldVals']), true);
    $isbnid = sanitize_text_field($data['isbnid']);
    $productid = sanitize_text_field($data['productid']);
    $user = wp_get_current_user();
    if (is_user_logged_in()){
        global $wpdb;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $userId = $user->ID;
        $query = 'delete from %i where addedby = %d and isbnid = %d and isbnid not in (select isbnid from %i)';
        $wpdb->query($wpdb->prepare($query, $field_values_table, $userId, $isbnid, $isbn_records_table), ARRAY_A);
        $query = 'insert into %i (isbnid, fieldlabel, fieldvalue, addedby, addeddate, displayOrder) values(%d, %s, %s, %d, now(), %d)';
        for ($i=0; $i< count($fieldVals); $i++){
            $wpdb->query($wpdb->prepare($query, $field_values_table, $isbnid, $fieldVals[$i]['field'], $fieldVals[$i]['value'], $userId, $i), ARRAY_A);
        }
        $query = 'insert into %i (isbnid, submitteddate, assignedproductid) values (%d, now(), %d)';
        $wpdb->query($wpdb->prepare($query, $isbn_records_table, $isbnid, $productid), ARRAY_A);
        // return 'success';
        return $wpdb->prepare($query, $isbn_records_table, $isbnid, $productid);
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

function saveFieldValues($data){
    $fieldVals = json_decode(sanitize_text_field($data['fieldVals']), true);
    $isbnid = sanitize_text_field($data['isbnid']);
    $user = wp_get_current_user();
    if (is_user_logged_in()){
        global $wpdb;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $userId = $user->ID;
        $query = 'delete from %i where addedby = %d and isbnid = %d and isbnid not in (select isbnid from %i)';
        $wpdb->query($wpdb->prepare($query, $field_values_table, $userId, $isbnid, $isbn_records_table), ARRAY_A);
        $query = 'insert into %i (isbnid, fieldlabel, fieldvalue, addedby, addeddate, displayOrder) values(%d, %s, %s, %d, now(), %d)';
        for ($i=0; $i< count($fieldVals); $i++){
            $wpdb->query($wpdb->prepare($query, $field_values_table, $isbnid, $fieldVals[$i]['field'], $fieldVals[$i]['value'], $userId, $i), ARRAY_A);
        }
        return 'success';
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

function checkAssignedProduct($data){
    $productId = sanitize_text_field($data['productId']);
    $user = wp_get_current_user();
    if (is_user_logged_in()){
        global $wpdb;
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $query = 'select numbers.isbn
        from %i numbers
        where numbers.assignedproductid = %d';
        $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $productId), ARRAY_A);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
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
        DATE(books.createdate) as publicationdate0,
        DATE(posts.post_date) as publicationdate0,
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

function markRecordFiled($data){
    $recordId = sanitize_text_field($data['recordId']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $userId = $user->ID;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = 'update %i
        set processeddate = now(),
        processedby = %d
        where id = %d';
        $wpdb->query($wpdb->prepare($query, $isbn_records_table, $userId, $recordId), ARRAY_A);
        return 'success';
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

function getMoreFiledRecords(){
    $shownCount = sanitize_text_field($data['shownCount']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $posts_table = $wpdb->prefix . "posts";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $query = 'select numbers.isbn, posts.post_title, records.submitteddate, records.processeddate
        from %i numbers
        join %i records on numbers.id = records.isbnid
        where records.processeddate is not null
        and records.id not in (select id from %i r where r.processeddate is not null order by r.submitteddate desc limit %d)
        order by records.submitteddate desc
        limit 3'; //change to 30 after testing
        $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $isbn_records_table, $shownCount), ARRAY_A);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}
