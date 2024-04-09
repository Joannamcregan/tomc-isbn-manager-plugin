<?php

add_action('rest_api_init', 'tomcIsbnRegisterRoute');

function tomcIsbnRegisterRoute() {
    register_rest_route('tomcISBN/v1', 'populate', array(
        'methods' => 'GET',
        'callback' => 'populate'
    ));
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
}

function markRecordFiled($data){
    $recordId = sanitize_text_field($data['recordId']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        $userId = get_current_user_id();
        global $wpdb;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = '';
        $record = [];
        $record['isbnproductid'] = $recordId;
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

function populate($data){
    $productId = sanitize_text_field($data['productId']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
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

function getUnfiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $posts_table = $wpdb->prefix . "posts";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = 'select 
        products.id as isbn_for, 
        users.display_name as user_display_name, 
        users.user_email,
        titlemeta.meta_value as title,
        subtitlemeta.meta_value as subtitle,
        descriptionmeta.meta_value as description,
        formatmeta.meta_value as format,
        firstgenremeta.meta_value as first_genre,
        secondgenremeta.meta_value as second_genre,
        contributor1meta.meta_value as contributor1,
        biography1meta.meta_value as biography1,
        function1meta.meta_value as function1,
        contributor2meta.meta_value as contributor2,
        biography2meta.meta_value as biography2,
        function2meta.meta_value as function2,
        contributor3meta.meta_value as contributor3,
        biography3meta.meta_value as biography3,
        function3meta.meta_value as function3,
        pubdate.meta_value as publication_date,
        status.meta_value as status,
        targ.meta_value as target_audience,
        price.meta_value as book_price,
        language.meta_value as book_language,
        cr.meta_value as copyright_year,
        num.meta_value as control_number,
        tt.meta_value as translated_title,
        pages.meta_value as number_of_pages,
        ills.meta_value as number_of_illustrations
        from %i isbnposts
        join %i pm on isbnposts.id = pm.post_id
        and pm.meta_key = "tomc_isbn_product"
        and pm.meta_value not in (select isbnproductid from %i)
        left join %i products on pm.meta_value = products.id
        left join %i users on products.post_author = users.id
        left join %i titlemeta on isbnposts.id = titlemeta.post_id
        and titlemeta.meta_key = "tomc_isbn_title"
        left join %i subtitlemeta on isbnposts.id = subtitlemeta.post_id
        and subtitlemeta.meta_key = "tomc_isbn_subtitle"
        left join %i descriptionmeta on isbnposts.id = descriptionmeta.post_id
        and descriptionmeta.meta_key = "tomc_isbn_description"
        left join %i formatmeta on isbnposts.id = formatmeta.post_id
        and formatmeta.meta_key = "tomc_isbn_format"
        left join %i firstgenremeta on isbnposts.id = firstgenremeta.post_id
        and firstgenremeta.meta_key = "tomc_isbn_first_genre"
        left join %i secondgenremeta on isbnposts.id = secondgenremeta.post_id
        and secondgenremeta.meta_key = "tomc_isbn_second_genre"
        left join %i contributor1meta on isbnposts.id = contributor1meta.post_id
        and contributor1meta.meta_key = "tomc_isbn_contributor1"
        left join %i biography1meta on isbnposts.id = biography1meta.post_id
        and biography1meta.meta_key = "tomc_isbn_biography1"
        left join %i function1meta on isbnposts.id = function1meta.post_id
        and function1meta.meta_key = "tomc_isbn_function1"
        left join %i contributor2meta on isbnposts.id = contributor2meta.post_id
        and contributor2meta.meta_key = "tomc_isbn_contributor2"
        left join %i biography2meta on isbnposts.id = biography2meta.post_id
        and biography2meta.meta_key = "tomc_isbn_biography2"
        left join %i function2meta on isbnposts.id = function2meta.post_id
        and function2meta.meta_key = "tomc_isbn_function2"
        left join %i contributor3meta on isbnposts.id = contributor3meta.post_id
        and contributor3meta.meta_key = "tomc_isbn_contributor3"
        left join %i biography3meta on isbnposts.id = biography3meta.post_id
        and biography3meta.meta_key = "tomc_isbn_biography3"
        left join %i function3meta on isbnposts.id = function3meta.post_id
        and function3meta.meta_key = "tomc_isbn_function3"
        left join %i pubdate on isbnposts.id = pubdate.post_id
        and pubdate.meta_key = "tomc_isbn_publication_date"
        left join %i status on isbnposts.id = status.post_id
        and status.meta_key = "tomc_isbn_status"
        left join %i targ on isbnposts.id = targ.post_id
        and targ.meta_key = "tomc_isbn_target_audience"
        left join %i price on isbnposts.id = price.post_id
        and price.meta_key = "tomc_isbn_book_price"
        left join %i language on isbnposts.id = language.post_id
        and language.meta_key = "tomc_isbn_book_language"
        left join %i cr on isbnposts.id = cr.post_id
        and cr.meta_key = "tomc_isbn_copyright_year"
        left join %i num on isbnposts.id = num.post_id
        and num.meta_key = "tomc_isbn_control_number"
        left join %i tt on isbnposts.id = tt.post_id
        and tt.meta_key = "tomc_isbn_translated_title"
        left join %i pages on isbnposts.id = pages.post_id
        and pages.meta_key = "tomc_isbn_number_of_pages"
        left join %i ills on isbnposts.id = ills.post_id
        and ills.meta_key = "tomc_isbn_number_of_illustrations"';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        // return prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

function getFiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $posts_table = $wpdb->prefix . "posts";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = 'select 
        products.id as isbn_for, 
        users.display_name as user_display_name, 
        users.user_email,
        titlemeta.meta_value as title,
        subtitlemeta.meta_value as subtitle,
        descriptionmeta.meta_value as description,
        formatmeta.meta_value as format,
        firstgenremeta.meta_value as first_genre,
        secondgenremeta.meta_value as second_genre,
        contributor1meta.meta_value as contributor1,
        biography1meta.meta_value as biography1,
        function1meta.meta_value as function1,
        contributor2meta.meta_value as contributor2,
        biography2meta.meta_value as biography2,
        function2meta.meta_value as function2,
        contributor3meta.meta_value as contributor3,
        biography3meta.meta_value as biography3,
        function3meta.meta_value as function3,
        pubdate.meta_value as publication_date,
        status.meta_value as status,
        targ.meta_value as target_audience,
        price.meta_value as book_price,
        language.meta_value as book_language,
        cr.meta_value as copyright_year,
        num.meta_value as control_number,
        tt.meta_value as translated_title,
        pages.meta_value as number_of_pages,
        ills.meta_value as number_of_illustrations
        from %i isbnposts
        join %i pm on isbnposts.id = pm.post_id
        and pm.meta_key = "tomc_isbn_product"
        and pm.meta_value in (select isbnproductid from %i)
        left join %i products on pm.meta_value = products.id
        left join %i users on products.post_author = users.id
        left join %i titlemeta on isbnposts.id = titlemeta.post_id
        and titlemeta.meta_key = "tomc_isbn_title"
        left join %i subtitlemeta on isbnposts.id = subtitlemeta.post_id
        and subtitlemeta.meta_key = "tomc_isbn_subtitle"
        left join %i descriptionmeta on isbnposts.id = descriptionmeta.post_id
        and descriptionmeta.meta_key = "tomc_isbn_description"
        left join %i formatmeta on isbnposts.id = formatmeta.post_id
        and formatmeta.meta_key = "tomc_isbn_format"
        left join %i firstgenremeta on isbnposts.id = firstgenremeta.post_id
        and firstgenremeta.meta_key = "tomc_isbn_first_genre"
        left join %i secondgenremeta on isbnposts.id = secondgenremeta.post_id
        and secondgenremeta.meta_key = "tomc_isbn_second_genre"
        left join %i contributor1meta on isbnposts.id = contributor1meta.post_id
        and contributor1meta.meta_key = "tomc_isbn_contributor1"
        left join %i biography1meta on isbnposts.id = biography1meta.post_id
        and biography1meta.meta_key = "tomc_isbn_biography1"
        left join %i function1meta on isbnposts.id = function1meta.post_id
        and function1meta.meta_key = "tomc_isbn_function1"
        left join %i contributor2meta on isbnposts.id = contributor2meta.post_id
        and contributor2meta.meta_key = "tomc_isbn_contributor2"
        left join %i biography2meta on isbnposts.id = biography2meta.post_id
        and biography2meta.meta_key = "tomc_isbn_biography2"
        left join %i function2meta on isbnposts.id = function2meta.post_id
        and function2meta.meta_key = "tomc_isbn_function2"
        left join %i contributor3meta on isbnposts.id = contributor3meta.post_id
        and contributor3meta.meta_key = "tomc_isbn_contributor3"
        left join %i biography3meta on isbnposts.id = biography3meta.post_id
        and biography3meta.meta_key = "tomc_isbn_biography3"
        left join %i function3meta on isbnposts.id = function3meta.post_id
        and function3meta.meta_key = "tomc_isbn_function3"
        left join %i pubdate on isbnposts.id = pubdate.post_id
        and pubdate.meta_key = "tomc_isbn_publication_date"
        left join %i status on isbnposts.id = status.post_id
        and status.meta_key = "tomc_isbn_status"
        left join %i targ on isbnposts.id = targ.post_id
        and targ.meta_key = "tomc_isbn_target_audience"
        left join %i price on isbnposts.id = price.post_id
        and price.meta_key = "tomc_isbn_book_price"
        left join %i language on isbnposts.id = language.post_id
        and language.meta_key = "tomc_isbn_book_language"
        left join %i cr on isbnposts.id = cr.post_id
        and cr.meta_key = "tomc_isbn_copyright_year"
        left join %i num on isbnposts.id = num.post_id
        and num.meta_key = "tomc_isbn_control_number"
        left join %i tt on isbnposts.id = tt.post_id
        and tt.meta_key = "tomc_isbn_translated_title"
        left join %i pages on isbnposts.id = pages.post_id
        and pages.meta_key = "tomc_isbn_number_of_pages"
        left join %i ills on isbnposts.id = ills.post_id
        and ills.meta_key = "tomc_isbn_number_of_illustrations"';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        // return $wpdb->prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}