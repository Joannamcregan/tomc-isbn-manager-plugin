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
                <?php $query = 'select distinct numbers.isbn, posts.post_title, concat(month(records.submitteddate), "/", day(records.submitteddate), "/", year(records.submitteddate)) as submitteddate, records.processeddate, records.id as recordid, books.product_image_id, books.title, itemmeta.meta_value
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
                                <?php if ($results[$i]['meta_value'] == 'Include Barcode (for physical books only)'){
                                    echo '<p><strong>**includes barcode**</strong></p>';
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
                    <?php $query =  'select distinct numbers.isbn, posts.post_title, concat(month(records.submitteddate), "/", day(records.submitteddate), "/", year(records.submitteddate)) as submitteddate, records.processeddate, itemmeta.meta_value
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
                        <?php if ($results[$i]['meta_value'] == 'Include Barcode (for physical books only)'){
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
                <!-- <i class="fa fa-window-close search-overlay__close" id="isbn-view-overlay__close" aria-label="close overlay"></i> -->
                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" height="30" viewBox="0 0 30 30" class="search-overlay__close" id="isbn-view-overlay__close">
                    <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAABWVJREFUWEfNmHtQ1FUUx7+/H08BRRTDMTABETXGhMxEBZTl5UiLzqRhY2EN5pj5ABSyIRBtGmiC1IqaMCUtGZRpUBoNZbd4+MhHUKZoiPkg0RRBVAwEbnN+Kz9h2XV/v90d2/PPzu7ee87nnnPvuedcDhYuXG++zKCwSJ5nC1k3AsGBf7LsjFiO8YzfuaaybE+PbREwK0SRB4b4Jwul11phSoUqlv4VAD8KDo1g4EotBE7A4Dj2anK5ukAAzAoK2w6OvWZJgIyxk+9WqidpAIMVfwLw0QVoY28Hryn+aL1+E4215822BjtHB3hPfR6NtXVobrimS29LSoXKRQMYojgPBm9do0KXxcE7MACMMRzc+DUunTxlMiQtWpm+CkM8RqCzowO713yIu03NffRyQGtyhcrZIOCs5KVwnzBWmEzKSjZsxs2/rhgNyfE8Ile/BY8J4zQ6GEPR2iw0NzQaB+g4dDDmZCTBYfAgQUFbSyuK07Nxr6nFKMhpi+ZhfNh0ce7xXT+gZu/Bfroke5Bmuo5yR3TqClBoSG5dvoq96zfiwb/tsiD9omYgcOFccc5Z9WFUbi3UqUMWIGnwmDgekYmLQSEiuVJzBqU5eWDd3ZIgRwb4ISIhHhynSb2Xa07jQM4WvfNlA5LScYppmP7GfBHo9MFKHP6myCDg0FHuUL6/EtZ2tsJY2sMlH2xGZ3uH3rlGAZK2ybFKPBetEBUf2fE9/igt12vI0cUZMeuTQJ8kd27cwp51Obh/+85jF2Y0IDgOinfi4PWiv+YQdnfjwCdbcLn6dD+D5DHyHHmQpP1um7B3W65eN+h14wEBWNnYYPZ7y+Dm4ykYosNSsmETmi79LRqmvRaeEI9nAvyE37oedGJf5ue4du6CQTgaYBIgKbB3coRyXQKchw8TDN671YLi9By0Nd8WvtNppVOrcTOD6rN8XPilRhKcWQBJySA3V8SsS4T9QEfB8M2LDYInxwRNBuW7Hjn6XTFO7f9JMpzZAEkRhZnCTWEnuVF/Ca6eHmI6ogNEB0mumBzi3gY9J09E2PJFVCP14bh44neUbdoq3ONyxayAZHxq3Mt4NjxI5KArsTBxPTo7HshlE8abFdDFfTiUaatg6zCgD8yxwhL8VlL2/wJSERGTkQinoS7iiRVDzRjUuTtQf+SkbEizeJCKh5dSV4iJmHJdafZXCJgbieG+mvKSftuflYvGs/WyIE0G5K14RCT1revUudtRf+RX2Dk5IEbIkU8JUO332rA3Q9oN0rMKkwGD4mMxdkag6BXt/aadI6XewWYB9I+JwKR5s0W4WvUhVG3d1S+EbmO8MHst5Uhr4T8pVYzJgKOnTcLMpY8aQEN1IfUzoW+/LuZIQ3WgSYAjxvuAehTe2krjkYdX2+PqOho3URmOF+ZHP/K46hCqtvX3eO8QyN6D2rmOurA9VBy0tEo6ncGLF8A3ZIrePautRBagdq7raLsv1HV6+lmdwLyVFaLWLMHTfr5ivnxcjpQMqJ3ruru6sD/rC1w9UyfJc70H0U1DNw5Fw1COlAwYtXqJ0DD1yM9ffou6quOy4XomOLkOwZyMRAxwHijmyOK0bOHlQvYepDShTFspzjtRtA/Vxaa/MQ3zGono1OWwttU0UefKj6Iir0A+IIXklY9TYT/ICVI7OKmupTaUehuCPJS/G2fKqvQDZgYr6jnAS5dyKkSdXF1wu/EfqbYlj6OF29jZCp2eoVNcAAbhwdCCpDqlQhXw8PltphLgxWdXi4Dk2Jsp5eptj56Ag8J2gmMLLAKO4ceUStUsYtF6RFfE8hwSGdhogHvCj+iCa+o5xj5NrlTn9ziqb5djEe7rC/EfEN2XR0giN+8AAAAASUVORK5CYII=" x="0" y="0" width="30" height="30" alt="X close icon" />
                </svg>
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