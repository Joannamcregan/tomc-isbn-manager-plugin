<?php

add_action('rest_api_init', 'tomcIsbnRegisterRoute');

function tomcIsbnRegisterRoute() {
    register_rest_route('tomcISBN/v1', 'populate', array(
        'methods' => 'POST',
        'callback' => 'populate'
    ));
}

function populate($data){
    $productId = sanitize_text_field($data['productId']);
    $user = wp_get_current_user();
    global $wpdb;
    $books_table = $wpdb->prefix . "tomc_books";
    $book_genres_table = $wpdb->prefix .  "tomc_book_genres";
    $book_products_table = $wpdb->prefix . "tomc_book_products";
    $posts_table = $wpdb->prefix . "posts";
    $product_types_table = $wpdb->prefix . "tomc_product_types";
    $pen_names_table = $wpdb->prefix . "tomc_pen_names_books";
    $users_table = $wpdb->prefix . "users";
    $usermeta_table = $wpdb->prefix . "usermeta";
    $query = 'select IFNULL(books.title, posts.post_title) as title,
    books.subtitle,
    books.book_description as description,
    producttypes.type_name as format,
    IFNULL(authorposts.post_title, users.display_name) as contributor,
    IFNULL(authorposts.post_content, usermeta.meta_value) as biography,
    CONCAT(MONTH(books.createdate), "/", DAY(books.createdate), "/", YEAR(books.createdate)) as publicationdate0, 
    CONCAT(MONTH(posts.post_date), "/", DAY(posts.post_date), "/", YEAR(posts.post_date)) as publicationdate1,
    books.islive
    from %i posts
    join %i users on posts.post_author = users.id
    left join %i usermeta on users.id = usermeta.user_id
    left join %i bookproducts on posts.id = bookproducts.productid
    left join %i books on bookproducts.bookid = books.id
    left join %i producttypes on bookproducts.typeid = producttypes.id
    left join %i pennamesbooks on books.id = pennamesbooks.bookid
    left join %i authorposts on pennamesbooks.pennameid = authorposts.id
    where posts.id = %d
    limit 1';
    $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $users_table, $usermeta_table, $book_products_table, $books_table, $product_types_table, $pen_names_table, $posts_table, $productId), ARRAY_A);
    return $results;
    // return $wpdb->prepare($query, $posts_table, $users_table, $usermeta_table, $book_products_table, $books_table, $product_types_table, $pen_names_table, $posts_table, $productId);
}