<?php global $wpdb;
$books_table = $wpdb->prefix .  "tomc_books";
// $product_types_table = $wpdb->prefix . "tomc_product_types";
// $book_products_table = $wpdb->prefix . "tomc_book_products";
// $pennames_table = $wpdb->prefix . "tomc_pennames";
// $user_pennames_table = $wpdb->prefix . "tomc_user_pennames";
// $pennames_books_table = $wpdb->prefix . "tomc_pennames_books";
// $book_readalikes_table = $wpdb->prefix . "tomc_book_readalikes";
// $genres_table = $wpdb->prefix . "tomc_genres";
// $book_genres_table = $wpdb->prefix . "tomc_book_genres";
// $users_table = $wpdb->prefix . "users";
// $posts_table = $wpdb->prefix . "posts";
$userid = get_current_user_id();
$user = wp_get_current_user();

get_header();
?><main class="half-screen">
<?php if (is_user_logged_in()){
    if (in_array( 'administrator', (array) $user->roles ) ){
        ?><div id="tomc-isbn-unfiled-records-section">
            <div id="tomc-isbn-unfiled-records-banner">
                <h1>Unfiled Records</h1>
            </div>
            <div id="tomc-isbn-unfiled-records-container"></div>
            <span id="tomc-isgn-get-unfiled-records">get the records</span>
        </div>
        <div id="tomc-isbn-filed-records-section">
            <div id="tomc-isbn-filed-records-banner">
                <h1>Filed Records</h1>
            </div>
            <div id="tomc-isbn-filed-records-container"></div>
            <span id="tomc-isgn-get-filed-records">get the records</span>
        </div>
    <?php } else {
        ?><p class="centered-text">Only logged in admin can access ISBN records. If you're an author looking for your personal ISBN records, please contact us by email.</p>
    <?php }
} else {
    ?><p class="centered-text">Only logged in admin can access ISBN records.</p>
<?php }
?></main>
<?php get_footer();
?>