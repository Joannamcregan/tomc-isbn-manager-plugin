<?php global $wpdb;
$isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
$isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
$isbn_field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
$user = wp_get_current_user();
$posts_table = $wpdb->prefix . "posts";
$terms_table = $wpdb->prefix . "terms";
$term_relationships_table = $wpdb->prefix . "term_relationships";
$term_taxonomy_table = $wpdb->prefix . "term_taxonomy";
$book_products_table = $wpdb->prefix . "tomc_book_products";
$books_table = $wpdb->prefix . "tomc_books";
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
                        ?><div class="tomc-purple-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php }                    
                    ?><p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <span class="add-isbn-info-button">add info</span>
                    </div>
                <?php }
            }

            $query = 'select numbers.isbn, records.submitteddate
            from %i numbers
            join %i records on numbers.id = records.isbnid
            and records.rejecteddate is null
            and records.processeddate is null
            where numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $userid), ARRAY_A);
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
        <i class="fa fa-window-close search-overlay__close" id="isbn-info-overlay__close" aria-label="close overlay"></i>
        <br>
        <h2 class="centered-text">Edit Info for ISBN </h2>
        <label for="isbn-info--assigned-product" required>Assigned Product</label>
        <select id="isbn-info--assigned-product">
            <?php $query="select posts.id as image_url, posts.post_title, terms.name as termname, numbers.assignedproductid
            from %i posts
            left join %i numbers on posts.id = numbers.assignedproductid
            join %i tr on posts.id = tr.object_id
            join %i terms on tr.term_taxonomy_id = terms.term_id
            join %i tt on tr.term_taxonomy_id = tt.term_taxonomy_id
            and tt.taxonomy = 'product_cat'
            where posts.post_type='product'
            and posts.post_author = %d";
            $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $term_relationships_table, $terms_table, $term_taxonomy_table, $userid), ARRAY_A);
            for ($i= 0; $i < count($results); $i++){
                ?><option >
                <?php echo $results[$i]['post_title'] . ' (categorized in ' . $results[$i]['termname'] . ')';
                ?><option>
            <?php }
        ?></select>
        <br><br>
        <label for="isbn-info--book-title" required>Book Title</label>
        <input type="text" id="isbn-info--book-title" />
        <br><br>
        <label for="isbn-info--book-subtitle">Subtitle (optional)</label>
        <input type="text" id="isbn-info--book-subtitle" />
        <br><br>
        <label for="isbn-info--book-description">Description (up to 350 words)</label>
        <br>
        <input type="textarea" id="isbn-info--book-description" required />
        <br><br>
        <label for="isbn-info--book-medium" required>Medium</label>
        <select id="isbn-info--book-medium">
            <option id="isbn-info--book-medium--audio">Audio</option>
            <option id="isbn-info--book-medium--ebook">E-book</option>
            <option id="isbn-info--book-medium--print">Print</option>
        </select>
        <br><br>
        <div id="isbn-info--section-Audio" class="isbn-info--format-section">
            <label for="isbn-info--audio-section--format">Format</label>
            <select id="isbn-info--audio-section--format">
                <option>Digital File</option>
                <option>CD</option>
                <option>DVD</option>
            </select>
        </div>
        <div class="hidden isbn-info--format-section" id="isbn-info--section-E-book">
            <label for="isbn-info--ebook-section--format">Format</label>
            <select id="isbn-info--ebook-section--format">
                <option>Digital Online</option>
                <option>Digital File</option>
            </select>
        </div>
        <div class="hidden isbn-info--format-section" id="isbn-info--section-Print">
            <label for="isbn-info--print-section--format">Format</label>
            <select id="isbn-info--print-section--format">
                <option>Hardback</option>
                <option>Paperback</option>
            </select>
        </div>

    </div>
</main>

<?php get_footer(); ?>