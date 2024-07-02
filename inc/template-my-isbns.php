<?php global $wpdb;
$posts_table = $wpdb->prefix . "posts";
$postmeta_table = $wpdb->prefix . "postmeta";
$isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
$isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
$userid = get_current_user_id();
$user = wp_get_current_user();

get_header();

?><main>
    <div class="banner"><h1 class="centered-text">My ISBNs</h1></div>
    <br>
    <div class="generic-content half-screen">
        <p class="centered-text"><strong>Get a free ISBN</strong> when you <a href="<?php echo esc_url(site_url('/product/isbn'));?>">purchase our ISBN registration service<a>.</p>
    <?php if (is_user_logged_in()){
        if (in_array( 'dc_vendor', (array) $user->roles ) ){
            $query = 'select isbnnumbers.isbn, titlemeta.meta_value as title
            from %i isbnposts
            join %i isbnnumbers on isbnposts.id = isbnnumbers.shoporderid     
            and isbnposts.post_author = %d
            join %i titlemeta on isbnposts.id = titlemeta.post_id
            and titlemeta.meta_key = "tomc_isbn_title"
            where isbnposts.id not in (select isbnrecords.shoporderid from %i isbnrecords)';
            $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $userid, $postmeta_table, $isbn_records_table), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2>Unfiled ISBNs</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field">
                    <?php }                    
                    ?><p><strong>Title: </strong><?php echo $results[$i]['title']; ?></p>
                    <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    </div>
                <?php }
            }
            $query = 'select isbnnumbers.isbn, titlemeta.meta_value as title, CONCAT(MONTH(isbnrecords.processeddate), "/", DAY(isbnrecords.processeddate), "/", YEAR(isbnrecords.processeddate)) as date
            from %i isbnposts
            join %i isbnnumbers on isbnposts.id = isbnnumbers.shoporderid     
            and isbnposts.post_author = %d
            join %i titlemeta on isbnposts.id = titlemeta.post_id
            and titlemeta.meta_key = "tomc_isbn_title"
            join %i isbnrecords on isbnposts.id = isbnrecords.shoporderid';
            $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $userid, $postmeta_table, $isbn_records_table), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2>Filed ISBNs</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field">
                    <?php }                    
                    ?><p><strong>Title: </strong><?php echo $results[$i]['title']; ?></p>
                    <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <p><strong>Filed on: </strong><?php echo $results[$i]['date']; ?></p>
                    </div>
                <?php }
            }
        } else {
            ?><p class="centered-text">Our ISBN Registration service is only for vendors. Interested in selling books on our platform? <a href="<?php echo esc_url(site_url('/own'));?>">Learn more</a> about joining our co-op!</p>
        <?php }
    } else {
        ?><p class="centered-text">Our ISBN Registration service is only available to logged-in vendors. <a href="<?php echo esc_url(site_url('/my-account'));?>">Login</a></p>
    <?php }
    ?></div>
</main>

<?php get_footer(); ?>