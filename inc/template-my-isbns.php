<?php global $wpdb;
$isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
$isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
$isbn_field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
$posts_table = $wpdb->prefix . "posts";
$user = wp_get_current_user();
$userid = $user->ID;

get_header();

?><main>
    <div class="banner"><h1 class="centered-text banner-heading-28">My ISBN Registrations</h1></div>
    <br>
    <div class="generic-content half-screen">
        <p class="centered-text"><strong>Get a free ISBN</strong> when you <a href="<?php echo esc_url(site_url('/product/isbn-registration'));?>">purchase our ISBN registration service</a>.</p>
        <?php if (is_user_logged_in()){
            $query = 'select numbers.isbn
            from %i numbers
            where numbers.id not in (select isbnid from %i records)
            and numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $userid), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2 class="centered-text">Unsubmitted Registrations</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php }                    
                    ?><p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <span class="add-isbn-info-button">add info</span>
                    </div>
                <?php }
            }

            $query = 'select numbers.isbn, products.post_title, records.submitteddate
            from %i numbers
            join %i records on numbers.id = records.isbnid
            and records.rejecteddate is null
            and records.processeddate is null
            join %i products on numbers.assignedproductid = products.id
            where numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $userid), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2 class="centered-text">Submitted Registrations</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field">
                    <?php }                    
                    ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                    <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate'] ?></p>
                    </div>
                <?php }
            }
        } else {
            ?><p class="centered-text">Our ISBN Registration service is only available to logged-in vendors. <a href="<?php echo esc_url(site_url('/my-account'));?>">Login</a></p>
        <?php }
    ?></div>

    <div class="search-overlay" id="tomc-isbn-edit-info-overlay">
            <h2 class="centered-text">Edit Info for ISBN </h2>
    </div>
</main>

<?php get_footer(); ?>