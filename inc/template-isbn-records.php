<?php global $wpdb;
$isbn_records_table = $wpdb->prefix . 'tomc_isbn_records';
$isbn_field_values_table = $wpdb->prefix . 'tomc_isbn_field_values';
$isbn_numbers_table = $wpdb->prefix . 'tomc_isbn_numbers';
$posts_table = $wpdb->prefix . 'posts';
$userid = get_current_user_id();
$user = wp_get_current_user();

get_header();
if (is_user_logged_in()){
    if (in_array( 'administrator', (array) $user->roles ) ){
        ?><main>
            <div id="tomc-isbn-unfiled-records-section">
                <div id="tomc-isbn-unfiled-records-banner">
                    <h1>Unfiled Records</h1>
                </div>
                <div id="tomc-isbn-unfiled-records-container" class="generic-content">
                <?php $query = 'select numbers.isbn, posts.post_title, records.submitteddate, records.processeddate, records.id as recordid
                    from %i numbers
                    join %i records on numbers.id = records.isbnid
                    left join %i posts on records.assignedproductid = posts.id
                    where records.processeddate is null
                    order by records.submitteddate desc
                    limit 3'; //change to 30 after testing
                    $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table), ARRAY_A);
                    for ($i = 0; $i < count($results); $i++){
                        if ($i % 2 == 0){
                            ?><div class="tomc-purple-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-recordid="<?php echo $results[$i]['recordid']; ?>">
                        <?php } else {
                            ?><div class="tomc-plain-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-recordid="<?php echo $results[$i]['recordid']; ?>">
                        <?php }                    
                            ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                            <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                            <p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate']; ?></p>
                            <span class="see-isbn-info-button">see info</span>
                        </div>
                    <?php }
                ?></div>
            </div>
            <div id="tomc-isbn-filed-records-section">
                <div id="tomc-isbn-filed-records-banner">
                    <h1>Filed Records</h1>
                </div>
                <div id="tomc-isbn-filed-records-container" class="generic-content">
                    <?php $query = 'select numbers.isbn, posts.post_title, records.submitteddate, records.processeddate
                    from %i numbers
                    join %i records on numbers.id = records.isbnid
                    join %i fieldvals on numbers.id = fieldvals.isbnid
                    where records.processeddate is not null
                    order by records.submitteddate desc
                    limit 3'; //change to 30 after testing
                    $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table), ARRAY_A);
                    for ($i = 0; $i < count($results); $i++){
                        if ($i % 2 == 0){
                            ?><div class="tomc-purple-isbn-field">
                        <?php } else {
                            ?><div class="tomc-plain-isbn-field">
                        <?php }                    
                        ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                        <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                        <p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate']; ?></p>
                        <p><strong>Filed on: </strong><?php echo $results[$i]['processeddate']; ?></p>
                        <span class="see-isbn-info-button">see info</span>
                        </div>
                    <?php }
                ?></div>
                <span id="tomc-isbn-get-filed-records" class="block">show older records</span>
            </div>

            <div class="search-overlay" id="tomc-isbn-view-info-overlay">
                <i class="fa fa-window-close search-overlay__close" id="isbn-view-overlay__close" aria-label="close overlay"></i>
                <br>
                <h2 class="centered-text">View Info for ISBN </h2>
                <div id="isbn-view--container" class="generic-content"></div>
                <span id="tomc-isbn-mark-filed">mark record complete</span>
            </div>
        </main>
    <?php } else {
        ?><main>
            <p class="centered-text half-screen">Only logged in admin can access ISBN registration records. If you're an author looking for your personal ISBN Registration records, please contact us by email.</p>
        </main>
    <?php }
} else {
    ?><main>
        <p class="centered-text">Only logged in admin can access ISBN registration records.</p>
    </main>
<?php }
get_footer();
?>