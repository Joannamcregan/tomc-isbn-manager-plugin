<?php global $wpdb;
$isbn_records_table = $wpdb->prefix . 'tomc_isbn_records';
$isbn_field_values_table = $wpdb->prefix . 'tomc_isbn_field_values';
$isbn_numbers_table = $wpdb->prefix . 'tomc_isbn_numbers';
$posts_table = $wpdb->prefix . 'posts';
$book_products_table = $wpdb->prefix . 'tomc_book_products';
$books_table = $wpdb->prefix . 'tomc_books';
$order_items_table = $wpdb->prefix . 'woocommerce_order_items';
$item_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
$updates_table = $wpdb->prefix . 'tomc_isbn_update_notes';
$users_table = $wpdb->prefix . 'users';
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
                <?php $query = 'select distinct numbers.isbn, posts.post_title, records.submitteddate, records.processeddate, records.id as recordid, books.product_image_id, books.title, itemmeta.meta_value
                    from %i numbers
                    join %i records on numbers.id = records.isbnid
                    left join %i posts on records.assignedproductid = posts.id
                    left join %i bookproducts on records.assignedproductid = bookproducts.productid
                    left join %i books on bookproducts.bookid = books.id
                    join %i orderitems on numbers.shoporderid = orderitems.order_id
                    and numbers.orderitemid = orderitems.order_item_id
                    left join %i itemmeta on orderitems.order_item_id = itemmeta.order_item_id
                    and itemmeta.meta_key = "Add Barcode (only for physical books)"
                    where records.processeddate is null
                    order by records.submitteddate desc
                    limit 3'; //change to 30 after testing
                    $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $book_products_table, $books_table, $order_items_table, $item_meta_table), ARRAY_A);
                    if (count($results) > 0){
                        for ($i = 0; $i < count($results); $i++){
                            if ($i % 2 == 0){
                                ?><div class="tomc-purple-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-recordid="<?php echo $results[$i]['recordid']; ?>">
                            <?php } else {
                                ?><div class="tomc-plain-isbn-field" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-recordid="<?php echo $results[$i]['recordid']; ?>">
                            <?php }    
                                ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                                <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                                <?php if ($results[$i]['meta_value'] == 'Include Barcode (only for physical books)'){
                                    echo '<p><strong>**includes barcode**</strong></p>';
                                }  else {
                                    echo $results[$i]['meta_value'];
                                }
                                ?><p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate']; ?></p>
                                <span class="see-isbn-info-button" data-image="<?php echo get_the_post_thumbnail_url($results[$i]['product_image_id']); ?>" data-title="<?php echo $results[$i]['title']; ?>">see info</span>
                            </div>
                        <?php }
                    } else {
                        ?><p class="centered-text">Yay! We're all caught up.</p>
                    <?php }
                ?></div>
            </div>
            <div id="tomc-isbn-filed-records-section">
                <div id="tomc-isbn-filed-records-banner">
                    <h1>Filed Records</h1>
                </div>
                <div id="tomc-isbn-filed-records-container" class="generic-content">
                    <?php $query =  'select distinct numbers.isbn, posts.post_title, records.submitteddate, records.processeddate
                    from %i numbers
                    join %i records on numbers.id = records.isbnid
                    join %i posts on records.assignedproductid = posts.id
                    join %i orderitems on numbers.shoporderid = orderitems.order_id
                    and numbers.orderitemid = orderitems.order_item_id
                    left join %i itemmeta on orderitems.order_item_id = itemmeta.order_item_id
                    and itemmeta.meta_key = "Add Barcode (only for physical books)"
                    where records.processeddate is not null
                    order by records.submitteddate desc
                    limit 3'; //change to 30 after testing
                    $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $order_items_table, $item_meta_table), ARRAY_A);
                    for ($i = 0; $i < count($results); $i++){
                        if ($i % 2 == 0){
                            ?><div class="tomc-purple-isbn-field">
                        <?php } else {
                            ?><div class="tomc-plain-isbn-field">
                        <?php }                    
                        ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                        <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                        <?php if ($results[$i]['meta_value'] == 'Include Barcode (only for physical books)'){
                            echo '<p><strong>**includes barcode**</strong></p>';
                        }  
                        ?><p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate']; ?></p>
                        <p><strong>Filed on: </strong><?php echo $results[$i]['processeddate']; ?></p>
                        </div>
                    <?php }
                ?></div>                
                <?php if (count($results) > 3){ //change to 30 after testing
                    ?><span id="tomc-isbn-get-filed-records" class="block" data-count=3>show older records</span>
                <?php }
            ?></div>
            <div id="tomc-isbn-filed-records-section">
                <div id="tomc-isbn-updated-records-banner">
                    <h1>Updates Needed</h1>
                </div>
                <div id="tomc-isbn-updated-records-container" class="generic-content">
                <?php $query = 'select distinct numbers.isbn, updates.id as updateid, updates.updatetext, updates.updateddate, users.user_email, users.display_name
                from %i numbers
                join %i updates on numbers.id = updates.isbnid
                join %i users on numbers.assignedto = users.id
                where updates.processedby is null
                order by updates.updateddate desc;';
                $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $updates_table, $users_table), ARRAY_A);
                if ($results){
                    for ($i = 0; $i < count($results); $i++){
                        if ($i % 2 == 0){
                            ?><div class="tomc-purple-isbn-field">
                        <?php } else {
                            ?><div class="tomc-plain-isbn-field">
                        <?php }                    
                        ?><p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                        <p><strong>Submitted on: </strong><?php echo $results[$i]['updateddate']; ?></p>
                        <p><strong>Author's Display Name: </strong><?php echo $results[$i]['display_name']; ?></p>
                        <p><strong>Author's Email: </strong><?php echo $results[$i]['user_email']; ?></p>
                        <p><strong>Update Note: </strong><?php echo $results[$i]['updatetext']; ?></p>
                        <span class="isbn-updates--mark-processed" data-updateid="<?php echo $results[$i]['updateid']; ?>">mark processed</span>
                        </div>
                    <?php }
                } else {
                    ?><p class="centered-text">None at this time</p>
                <?php }
                ?></div>
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