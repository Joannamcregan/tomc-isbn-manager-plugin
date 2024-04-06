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
    $query = 'select IFNULL(books.title, posts.post_title) as title
    from %i posts
    left join %i bookproducts on posts.id = bookproducts.productid
    left join %i books on bookproducts.bookid = books.id
    where posts.id = 230';
    $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $book_products_table, $books_table), ARRAY_A);
    return $results;
    // return $wpdb->prepare($query, $posts_table, $book_products_table, $books_table);
}