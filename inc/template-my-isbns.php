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
$order_items_table = $wpdb->prefix . 'woocommerce_order_items';
$item_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
$updates_table = $wpdb->prefix . 'tomc_isbn_update_notes';
$userid = $user->ID;

get_header();

?><main>
    <div class="banner"><h1 class="centered-text banner-heading-28">My ISBN Registrations</h1></div>
    <br>
    <div class="generic-content half-screen">
        <!-- <p class="centered-text"><strong>Get a free ISBN</strong> when you purchase our <a href="<?php echo esc_url(site_url('/product/isbn-registration'));?>">ISBN registration service</a>.</p> -->
         <p class="centered-text">Free ISBNs, but <a href="<?php echo esc_url(site_url('/product/isbn-registration'));?>">Registration and Barcodes</a> will cost you.</p>
        <?php if (is_user_logged_in()){
            $query = 'select distinct numbers.isbn, numbers.id, itemmeta.meta_key, orderitems.*, numbers.orderitemid
            from %i numbers
            join %i orderitems on numbers.shoporderid = orderitems.order_id
            and numbers.orderitemid = orderitems.order_item_id
            left join %i itemmeta on orderitems.order_item_id = itemmeta.order_item_id
            and itemmeta.meta_key = "Add Barcode (only for physical books)"
            where numbers.id not in (select isbnid from %i records)
            and numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $order_items_table, $item_meta_table, $isbn_records_table, $userid), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2 class="centered-text">Unsubmitted Registrations</h2>
                <p class="centered-text">Creators are responsible for providing complete information and hitting Submit on each registration form. Creators who purchase ISBNs and do not complete the form, will not have their work registered with Bowker. Creators with more than five unsubmitted registrations will be barred from purchasing additional registrations until all outstanding ISBNs registration forms are complete.</p>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-isbnid="<?php echo $results[$i]['id']; ?>">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>" data-isbnid="<?php echo $results[$i]['id']; ?>">
                    <?php }                    
                    ?><p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <?php if ($results[$i]['meta_key'] == 'Add Barcode (only for physical books)'){
                        echo '<p><strong>**includes barcode**</strong></p>';
                    }
                    ?><span class="add-isbn-info-button">add info</span>
                    </div>
                <?php }
            } 

            $query = 'select distinct numbers.isbn, concat(month(records.submitteddate), "/", day(records.submitteddate), "/", year(records.submitteddate)) as submitteddate, posts.post_title, itemmeta.meta_key, records.id as recordid, orderitems.*
            from %i numbers
            join %i records on numbers.id = records.isbnid
            join %i posts on records.assignedproductid = posts.id
            and records.processeddate is null            
            join %i orderitems on numbers.shoporderid = orderitems.order_id
            and numbers.orderitemid = orderitems.order_item_id
            left join %i itemmeta on orderitems.order_item_id = itemmeta.order_item_id
            and itemmeta.meta_key = "Add Barcode (only for physical books)"
            where numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $order_items_table, $item_meta_table, $userid), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2 class="centered-text">Submitted Registrations</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field tomc-isbn-field-section" data-isbn="<?php echo $results[$i]['isbn']; ?>">
                    <?php }                    
                    ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                    <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                    <?php if ($results[$i]['meta_key'] == 'Add Barcode (only for physical books)'){
                        echo '<p><strong>**includes barcode**</strong></p>';
                    }
                    ?><p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate'] ?></p>
                    <span class="view-isbn-info-button">view info</span>
                    <span class="unsubmit-isbn-button" data-record="<?php echo $results[$i]['recordid']; ?>">unsubmit</span>
                    </div>
                <?php }
            }

            $query = 'select distinct numbers.isbn, concat(month(records.submitteddate), "/", day(records.submitteddate), "/", year(records.submitteddate)) as submitteddate, concat(month(records.processeddate), "/", day(records.processeddate), "/", year(records.processeddate)) as processeddate, posts.post_title, itemmeta.meta_key, numbers.id as isbnid
            from %i numbers
            join %i records on numbers.id = records.isbnid
            join %i posts on records.assignedproductid = posts.id
            and records.processeddate is not null
            join %i orderitems on numbers.shoporderid = orderitems.order_id
            and numbers.orderitemid = orderitems.order_item_id
            left join %i itemmeta on orderitems.order_item_id = itemmeta.order_item_id
            and itemmeta.meta_key = "Add Barcode (only for physical books)"
            where numbers.assignedto = %d';
            $results = $wpdb->get_results($wpdb->prepare($query, $isbn_numbers_table, $isbn_records_table, $posts_table, $order_items_table, $item_meta_table, $userid), ARRAY_A);
            if (($results) && count($results) > 0){
                ?><h2 class="centered-text">Processed Registrations</h2>
                <?php for ($i = 0; $i < count($results); $i++){
                    if ($i % 2 == 0){
                        ?><div class="tomc-purple-isbn-field">
                    <?php } else {
                        ?><div class="tomc-plain-isbn-field">
                    <?php }                    
                        ?><p><strong>Title: </strong><?php echo $results[$i]['post_title']; ?></p>
                        <p><strong>ISBN: </strong><?php echo $results[$i]['isbn']; ?></p>
                        <?php if ($results[$i]['meta_key'] == 'Add Barcode (only for physical books)'){
                            echo '<p><strong>**includes barcode**</strong></p>';
                        }
                        ?><p><strong>Submitted on: </strong><?php echo $results[$i]['submitteddate']; ?></p>
                        <p><strong>Processed on: </strong><?php echo $results[$i]['processeddate']; ?></p>
                        <!-- put a subquery to check for updates here -->
                        <?php $subquery = "select updates.updatetext, concat(month(updates.updateddate), '/', day(updates.updateddate), '/', year(updates.updateddate)) as updateddate, concat(month(updates.processeddate), '/', day(updates.processeddate), '/', year(updates.processeddate)) as processeddate
                        from %i updates
                        join %i numbers on updates.isbnid = numbers.id
                        where numbers.isbn = %s";
                        $subresults = $wpdb->get_results($wpdb->prepare($subquery, $updates_table, $isbn_numbers_table, $results[$i]['isbn']), ARRAY_A);
                        if ($subresults){
                            for ($j = 0; $j < count($subresults); $j++){
                                ?><div class="my-isbn-update-info">
                                <p><strong>Update requested </strong><?php echo $subresults[$j]['updateddate']; ?></p>
                                <p><?php echo $subresults[$j]['processeddate'] == null ? '<strong>Update not yet processed</strong>' : '<strong>Update processed: </strong>' . $subresults[$j]['processeddate']; ?></p>
                                <p><strong>Update note: </strong><?php echo $subresults[$j]['updatetext']; ?></p>
                                </div>
                            <?php }
                        } 
                        ?><span class="view-isbn-info-button">view info</span>
                        <span class="update-isbn-button" data-isbnid="<?php echo $results[$i]['isbnid']; ?>" data-isbn="<?php echo $results[$i]['isbn']; ?>">update</span>
                    </div>
                <?php }
            }
        } else {
            ?><p class="centered-text">Our ISBN Registration service is only available to logged-in vendors. <a href="<?php echo esc_url(site_url('/my-account'));?>">Login</a></p>
        <?php }
    ?></div>

    <div class="search-overlay" id="tomc-isbn-view-info-overlay">
    <!-- <i class="fa fa-window-close search-overlay__close" id="isbn-view-info-overlay__close" aria-label="close overlay"></i> -->
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" height="30" viewBox="0 0 30 30" class="search-overlay__close" id="isbn-view-info-overlay__close">
        <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAABWVJREFUWEfNmHtQ1FUUx7+/H08BRRTDMTABETXGhMxEBZTl5UiLzqRhY2EN5pj5ABSyIRBtGmiC1IqaMCUtGZRpUBoNZbd4+MhHUKZoiPkg0RRBVAwEbnN+Kz9h2XV/v90d2/PPzu7ee87nnnPvuedcDhYuXG++zKCwSJ5nC1k3AsGBf7LsjFiO8YzfuaaybE+PbREwK0SRB4b4Jwul11phSoUqlv4VAD8KDo1g4EotBE7A4Dj2anK5ukAAzAoK2w6OvWZJgIyxk+9WqidpAIMVfwLw0QVoY28Hryn+aL1+E4215822BjtHB3hPfR6NtXVobrimS29LSoXKRQMYojgPBm9do0KXxcE7MACMMRzc+DUunTxlMiQtWpm+CkM8RqCzowO713yIu03NffRyQGtyhcrZIOCs5KVwnzBWmEzKSjZsxs2/rhgNyfE8Ile/BY8J4zQ6GEPR2iw0NzQaB+g4dDDmZCTBYfAgQUFbSyuK07Nxr6nFKMhpi+ZhfNh0ce7xXT+gZu/Bfroke5Bmuo5yR3TqClBoSG5dvoq96zfiwb/tsiD9omYgcOFccc5Z9WFUbi3UqUMWIGnwmDgekYmLQSEiuVJzBqU5eWDd3ZIgRwb4ISIhHhynSb2Xa07jQM4WvfNlA5LScYppmP7GfBHo9MFKHP6myCDg0FHuUL6/EtZ2tsJY2sMlH2xGZ3uH3rlGAZK2ybFKPBetEBUf2fE9/igt12vI0cUZMeuTQJ8kd27cwp51Obh/+85jF2Y0IDgOinfi4PWiv+YQdnfjwCdbcLn6dD+D5DHyHHmQpP1um7B3W65eN+h14wEBWNnYYPZ7y+Dm4ykYosNSsmETmi79LRqmvRaeEI9nAvyE37oedGJf5ue4du6CQTgaYBIgKbB3coRyXQKchw8TDN671YLi9By0Nd8WvtNppVOrcTOD6rN8XPilRhKcWQBJySA3V8SsS4T9QEfB8M2LDYInxwRNBuW7Hjn6XTFO7f9JMpzZAEkRhZnCTWEnuVF/Ca6eHmI6ogNEB0mumBzi3gY9J09E2PJFVCP14bh44neUbdoq3ONyxayAZHxq3Mt4NjxI5KArsTBxPTo7HshlE8abFdDFfTiUaatg6zCgD8yxwhL8VlL2/wJSERGTkQinoS7iiRVDzRjUuTtQf+SkbEizeJCKh5dSV4iJmHJdafZXCJgbieG+mvKSftuflYvGs/WyIE0G5K14RCT1revUudtRf+RX2Dk5IEbIkU8JUO332rA3Q9oN0rMKkwGD4mMxdkag6BXt/aadI6XewWYB9I+JwKR5s0W4WvUhVG3d1S+EbmO8MHst5Uhr4T8pVYzJgKOnTcLMpY8aQEN1IfUzoW+/LuZIQ3WgSYAjxvuAehTe2krjkYdX2+PqOho3URmOF+ZHP/K46hCqtvX3eO8QyN6D2rmOurA9VBy0tEo6ncGLF8A3ZIrePautRBagdq7raLsv1HV6+lmdwLyVFaLWLMHTfr5ivnxcjpQMqJ3ruru6sD/rC1w9UyfJc70H0U1DNw5Fw1COlAwYtXqJ0DD1yM9ffou6quOy4XomOLkOwZyMRAxwHijmyOK0bOHlQvYepDShTFspzjtRtA/Vxaa/MQ3zGono1OWwttU0UefKj6Iir0A+IIXklY9TYT/ICVI7OKmupTaUehuCPJS/G2fKqvQDZgYr6jnAS5dyKkSdXF1wu/EfqbYlj6OF29jZCp2eoVNcAAbhwdCCpDqlQhXw8PltphLgxWdXi4Dk2Jsp5eptj56Ag8J2gmMLLAKO4ceUStUsYtF6RFfE8hwSGdhogHvCj+iCa+o5xj5NrlTn9ziqb5djEe7rC/EfEN2XR0giN+8AAAAASUVORK5CYII=" x="0" y="0" width="30" height="30" alt="X close icon" />
    </svg>
    <br>
    <h2 class="centered-text">View Info for ISBN </h2>
    <div id="tomc-isbn-view-info-container" class="generic-content"></div>
    </div>

    <div class="search-overlay" id="tomc-isbn-update-info-overlay">
    <!-- <i class="fa fa-window-close search-overlay__close" id="isbn-update-info-overlay__close" aria-label="close overlay"></i> -->
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" height="30" viewBox="0 0 30 30" class="search-overlay__close" id="isbn-update-info-overlay__close">
        <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAABWVJREFUWEfNmHtQ1FUUx7+/H08BRRTDMTABETXGhMxEBZTl5UiLzqRhY2EN5pj5ABSyIRBtGmiC1IqaMCUtGZRpUBoNZbd4+MhHUKZoiPkg0RRBVAwEbnN+Kz9h2XV/v90d2/PPzu7ee87nnnPvuedcDhYuXG++zKCwSJ5nC1k3AsGBf7LsjFiO8YzfuaaybE+PbREwK0SRB4b4Jwul11phSoUqlv4VAD8KDo1g4EotBE7A4Dj2anK5ukAAzAoK2w6OvWZJgIyxk+9WqidpAIMVfwLw0QVoY28Hryn+aL1+E4215822BjtHB3hPfR6NtXVobrimS29LSoXKRQMYojgPBm9do0KXxcE7MACMMRzc+DUunTxlMiQtWpm+CkM8RqCzowO713yIu03NffRyQGtyhcrZIOCs5KVwnzBWmEzKSjZsxs2/rhgNyfE8Ile/BY8J4zQ6GEPR2iw0NzQaB+g4dDDmZCTBYfAgQUFbSyuK07Nxr6nFKMhpi+ZhfNh0ce7xXT+gZu/Bfroke5Bmuo5yR3TqClBoSG5dvoq96zfiwb/tsiD9omYgcOFccc5Z9WFUbi3UqUMWIGnwmDgekYmLQSEiuVJzBqU5eWDd3ZIgRwb4ISIhHhynSb2Xa07jQM4WvfNlA5LScYppmP7GfBHo9MFKHP6myCDg0FHuUL6/EtZ2tsJY2sMlH2xGZ3uH3rlGAZK2ybFKPBetEBUf2fE9/igt12vI0cUZMeuTQJ8kd27cwp51Obh/+85jF2Y0IDgOinfi4PWiv+YQdnfjwCdbcLn6dD+D5DHyHHmQpP1um7B3W65eN+h14wEBWNnYYPZ7y+Dm4ykYosNSsmETmi79LRqmvRaeEI9nAvyE37oedGJf5ue4du6CQTgaYBIgKbB3coRyXQKchw8TDN671YLi9By0Nd8WvtNppVOrcTOD6rN8XPilRhKcWQBJySA3V8SsS4T9QEfB8M2LDYInxwRNBuW7Hjn6XTFO7f9JMpzZAEkRhZnCTWEnuVF/Ca6eHmI6ogNEB0mumBzi3gY9J09E2PJFVCP14bh44neUbdoq3ONyxayAZHxq3Mt4NjxI5KArsTBxPTo7HshlE8abFdDFfTiUaatg6zCgD8yxwhL8VlL2/wJSERGTkQinoS7iiRVDzRjUuTtQf+SkbEizeJCKh5dSV4iJmHJdafZXCJgbieG+mvKSftuflYvGs/WyIE0G5K14RCT1revUudtRf+RX2Dk5IEbIkU8JUO332rA3Q9oN0rMKkwGD4mMxdkag6BXt/aadI6XewWYB9I+JwKR5s0W4WvUhVG3d1S+EbmO8MHst5Uhr4T8pVYzJgKOnTcLMpY8aQEN1IfUzoW+/LuZIQ3WgSYAjxvuAehTe2krjkYdX2+PqOho3URmOF+ZHP/K46hCqtvX3eO8QyN6D2rmOurA9VBy0tEo6ncGLF8A3ZIrePautRBagdq7raLsv1HV6+lmdwLyVFaLWLMHTfr5ivnxcjpQMqJ3ruru6sD/rC1w9UyfJc70H0U1DNw5Fw1COlAwYtXqJ0DD1yM9ffou6quOy4XomOLkOwZyMRAxwHijmyOK0bOHlQvYepDShTFspzjtRtA/Vxaa/MQ3zGono1OWwttU0UefKj6Iir0A+IIXklY9TYT/ICVI7OKmupTaUehuCPJS/G2fKqvQDZgYr6jnAS5dyKkSdXF1wu/EfqbYlj6OF29jZCp2eoVNcAAbhwdCCpDqlQhXw8PltphLgxWdXi4Dk2Jsp5eptj56Ag8J2gmMLLAKO4ceUStUsYtF6RFfE8hwSGdhogHvCj+iCa+o5xj5NrlTn9ziqb5djEe7rC/EfEN2XR0giN+8AAAAASUVORK5CYII=" x="0" y="0" width="30" height="30" alt="X close icon" />
    </svg>
    <br>
    <h2 class="centered-text">Update Info for ISBN </h2>
    <div class="generic-content">
        <p class="centered-text">Include field(s) and desired change(s) with as much detail as possible below.</p>
        <textarea id="isbn-info--update-note" required></textarea>
        <button id="isbn-info--send-update" class="blue-button">Submit Update</button>
    </div>
    </div>

    <div class="search-overlay" id="tomc-isbn-edit-info-overlay">
        <!-- <i class="fa fa-window-close search-overlay__close" id="isbn-info-overlay__close" aria-label="close overlay"></i> -->
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" height="30" viewBox="0 0 30 30" class="search-overlay__close" id="isbn-info-overlay__close">
            <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAABWVJREFUWEfNmHtQ1FUUx7+/H08BRRTDMTABETXGhMxEBZTl5UiLzqRhY2EN5pj5ABSyIRBtGmiC1IqaMCUtGZRpUBoNZbd4+MhHUKZoiPkg0RRBVAwEbnN+Kz9h2XV/v90d2/PPzu7ee87nnnPvuedcDhYuXG++zKCwSJ5nC1k3AsGBf7LsjFiO8YzfuaaybE+PbREwK0SRB4b4Jwul11phSoUqlv4VAD8KDo1g4EotBE7A4Dj2anK5ukAAzAoK2w6OvWZJgIyxk+9WqidpAIMVfwLw0QVoY28Hryn+aL1+E4215822BjtHB3hPfR6NtXVobrimS29LSoXKRQMYojgPBm9do0KXxcE7MACMMRzc+DUunTxlMiQtWpm+CkM8RqCzowO713yIu03NffRyQGtyhcrZIOCs5KVwnzBWmEzKSjZsxs2/rhgNyfE8Ile/BY8J4zQ6GEPR2iw0NzQaB+g4dDDmZCTBYfAgQUFbSyuK07Nxr6nFKMhpi+ZhfNh0ce7xXT+gZu/Bfroke5Bmuo5yR3TqClBoSG5dvoq96zfiwb/tsiD9omYgcOFccc5Z9WFUbi3UqUMWIGnwmDgekYmLQSEiuVJzBqU5eWDd3ZIgRwb4ISIhHhynSb2Xa07jQM4WvfNlA5LScYppmP7GfBHo9MFKHP6myCDg0FHuUL6/EtZ2tsJY2sMlH2xGZ3uH3rlGAZK2ybFKPBetEBUf2fE9/igt12vI0cUZMeuTQJ8kd27cwp51Obh/+85jF2Y0IDgOinfi4PWiv+YQdnfjwCdbcLn6dD+D5DHyHHmQpP1um7B3W65eN+h14wEBWNnYYPZ7y+Dm4ykYosNSsmETmi79LRqmvRaeEI9nAvyE37oedGJf5ue4du6CQTgaYBIgKbB3coRyXQKchw8TDN671YLi9By0Nd8WvtNppVOrcTOD6rN8XPilRhKcWQBJySA3V8SsS4T9QEfB8M2LDYInxwRNBuW7Hjn6XTFO7f9JMpzZAEkRhZnCTWEnuVF/Ca6eHmI6ogNEB0mumBzi3gY9J09E2PJFVCP14bh44neUbdoq3ONyxayAZHxq3Mt4NjxI5KArsTBxPTo7HshlE8abFdDFfTiUaatg6zCgD8yxwhL8VlL2/wJSERGTkQinoS7iiRVDzRjUuTtQf+SkbEizeJCKh5dSV4iJmHJdafZXCJgbieG+mvKSftuflYvGs/WyIE0G5K14RCT1revUudtRf+RX2Dk5IEbIkU8JUO332rA3Q9oN0rMKkwGD4mMxdkag6BXt/aadI6XewWYB9I+JwKR5s0W4WvUhVG3d1S+EbmO8MHst5Uhr4T8pVYzJgKOnTcLMpY8aQEN1IfUzoW+/LuZIQ3WgSYAjxvuAehTe2krjkYdX2+PqOho3URmOF+ZHP/K46hCqtvX3eO8QyN6D2rmOurA9VBy0tEo6ncGLF8A3ZIrePautRBagdq7raLsv1HV6+lmdwLyVFaLWLMHTfr5ivnxcjpQMqJ3ruru6sD/rC1w9UyfJc70H0U1DNw5Fw1COlAwYtXqJ0DD1yM9ffou6quOy4XomOLkOwZyMRAxwHijmyOK0bOHlQvYepDShTFspzjtRtA/Vxaa/MQ3zGono1OWwttU0UefKj6Iir0A+IIXklY9TYT/ICVI7OKmupTaUehuCPJS/G2fKqvQDZgYr6jnAS5dyKkSdXF1wu/EfqbYlj6OF29jZCp2eoVNcAAbhwdCCpDqlQhXw8PltphLgxWdXi4Dk2Jsp5eptj56Ag8J2gmMLLAKO4ceUStUsYtF6RFfE8hwSGdhogHvCj+iCa+o5xj5NrlTn9ziqb5djEe7rC/EfEN2XR0giN+8AAAAASUVORK5CYII=" x="0" y="0" width="30" height="30" alt="X close icon" />
        </svg>
        <br>
        <h2 class="centered-text">Edit Info for ISBN </h2>
        <div id="tomc-isbn-edit-info-overlay-column-container">
            <div class="isbn-info-overlay-column">
                <label for="isbn-info--assigned-product" required>Assigned product</label>
                <select id="isbn-info--assigned-product">
                    <option data-productid=0></option>
                    <?php $query="select posts.id, posts.post_title, terms.name as termname
                    from %i posts
                    join %i tr on posts.id = tr.object_id
                    join %i terms on tr.term_taxonomy_id = terms.term_id
                    join %i tt on tr.term_taxonomy_id = tt.term_taxonomy_id
                    and tt.taxonomy = 'product_cat'
                    join %i bp on posts.id = bp.productid
                    join %i books on bp.bookid = books.id
                    and books.islive = 1
                    where posts.post_type='product'
                    and posts.post_author = %d
                    and posts.id not in (select assignedproductid from %i)";
                    $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $term_relationships_table, $terms_table, $term_taxonomy_table, $book_products_table, $books_table, $userid, $isbn_records_table), ARRAY_A);
                    for ($i= 0; $i < count($results); $i++){
                        ?><option data-productid="<?php echo $results[$i]['id']; ?>">
                        <?php echo $results[$i]['post_title'] . ' (categorized in ' . $results[$i]['termname'] . ')';
                        ?></option>
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
                <textarea id="isbn-info--book-description" required class="isbn-info--textarea"></textarea>
                <br><br>
                <label for="isbn-info--book-medium" required>Medium</label>
                <select id="isbn-info--book-medium">
                    <option id="isbn-info--book-medium--ebook">E-book</option>
                    <option id="isbn-info--book-medium--audio">Audio</option>
                    <option id="isbn-info--book-medium--print">Print</option>
                </select>
                <br><br>
                <div id="isbn-info--section-Audio" class="hidden isbn-info--format-section">
                    <label for="isbn-info--audio-section--format" class="isbn-info--format-label">Format</label>
                    <select id="isbn-info--audio-section--format" class="isbn-info--format-select">
                        <option selected id="section-Audio--digital-file">Digital File</option>
                        <option id="section-Audio--cd">CD</option>
                        <option id="section-Audio--dvd">DVD</option>
                    </select>
                </div>
                <div class="isbn-info--format-section" id="isbn-info--section-E-book">
                    <label for="isbn-info--ebook-section--format" class="isbn-info--format-label">Format</label>
                    <select id="isbn-info--ebook-section--format" class="isbn-info--format-select">
                        <option id="section-ebook--digital-online">Digital Online</option>
                        <option selected id="section-ebook--digital-file">Digital File</option>
                    </select>
                </div>
                <div class="hidden isbn-info--format-section" id="isbn-info--section-Print">
                    <label for="isbn-info--print-section--format" class="isbn-info--format-label">Format</label>
                    <select id="isbn-info--print-section--format" class="isbn-info--format-select">
                        <option id="section-Print--Hardback">Hardback</option>
                        <option id="section-Print--Paperback">Paperback</option>
                    </select>
                </div>
                <br><br>
                <label for="isbn-info--first-genre" required>First genre</label>
                <select id="isbn-info--first-genre">
                    <option>Agriculture</option>
                    <option>Architecture</option>
                    <option>Art</option>
                    <option>Astrology</option>
                    <option>Bible_Commentaries</option>
                    <option>Biography</option>
                    <option>Business</option>
                    <option>Children’s Fiction</option>
                    <option>Collectors and Collecting</option>
                    <option>Computer Software</option>
                    <option>Computers</option>
                    <option>Cooking</option>
                    <option>Crime</option>
                    <option>Curiosities and Wonders</option>
                    <option>Drama (Dramatic Works By One Author)</option>
                    <option>Drama_Collections</option>
                    <option>Drama_History and Criticism</option>
                    <option>Economics</option>
                    <option>Education</option>
                    <option>Family</option>
                    <option>Fiction_Action and Adventure</option>
                    <option>Fiction_Erotica</option>
                    <option>Fiction_Espionage</option>
                    <option>Fiction_Fantasy_General</option>
                    <option>Fiction_Gay</option>
                    <option id="isbn-info--first-genre--fiction-general" selected>Fiction_General</option>
                    <option>Fiction_Historical</option>
                    <option>Fiction_Horror</option>
                    <option>Fiction_Mystery and Detective_General</option>
                    <option>Fiction_Psychological</option>
                    <option>Fiction_Religious</option>
                    <option>Fiction_Romance_General</option>
                    <option>Fiction_Science Fiction_General</option>
                    <option>Fiction_Short Stories (single author)</option>
                    <option>Fiction_Suspense</option>
                    <option>Fiction_Thrillers</option>
                    <option>Fiction_Visionary and Metaphysical</option>
                    <option>Fiction_War and Military</option>
                    <option>Fiction_Westerns</option>
                    <option>Finance, Personal</option>
                    <option>Games</option>
                    <option>Gardening</option>
                    <option>Genealogy</option>
                    <option>Great Britain_History</option>
                    <option>Handicraft</option>
                    <option>Health</option>
                    <option>Interior Decoration</option>
                    <option>Internet</option>
                    <option>Interpersonal Relations</option>
                    <option>Language and Languages_Study and Teaching</option>
                    <option>Language Arts</option>
                    <option>Law</option>
                    <option>Literature_Collections</option>
                    <option>Literature_History and Criticism</option>
                    <option>Mathematics</option>
                    <option>Medicine</option>
                    <option>Military Art and Science</option>
                    <option>Mind and Body</option>
                    <option>Music</option>
                    <option>Nature</option>
                    <option>Nutrition</option>
                    <option>Parenting</option>
                    <option>Performing Arts</option>
                    <option>Pets</option>
                    <option>Philosophy</option>
                    <option>Photography</option>
                    <option>Physical Fitness</option>
                    <option>Physics</option>
                    <option>Poetry (Poetic Works by One Author)</option>
                    <option>Poetry_Collections</option>
                    <option>Poetry_History and Criticism</option>
                    <option>Political Science</option>
                    <option>Psychology</option>
                    <option>Reference Books</option>
                    <option>Religion</option>
                    <option>Science</option>
                    <option>Self-Help Techniques</option>
                    <option>Social Sciences</option>
                    <option>Spirituality</option>
                    <option>Sports</option>
                    <option>Technology</option>
                    <option>Transportation</option>
                    <option>Travel</option>
                    <option>Wit and Humor</option>
                    <option>World History</option>
                </select>
                <br><br>
                <label for="isbn-info--second-genre" required>Second genre (optional)</label>
                <select id="isbn-info--second-genre">
                    <option id="isbn-info--second-genre--blank"></option>
                    <option>Agriculture</option>
                    <option>Architecture</option>
                    <option>Art</option>
                    <option>Astrology</option>
                    <option>Bible_Commentaries</option>
                    <option>Biography</option>
                    <option>Business</option>
                    <option>Children’s Fiction</option>
                    <option>Collectors and Collecting</option>
                    <option>Computer Software</option>
                    <option>Computers</option>
                    <option>Cooking</option>
                    <option>Crime</option>
                    <option>Curiosities and Wonders</option>
                    <option>Drama (Dramatic Works By One Author)</option>
                    <option>Drama_Collections</option>
                    <option>Drama_History and Criticism</option>
                    <option>Economics</option>
                    <option>Education</option>
                    <option>Family</option>
                    <option>Fiction_Action and Adventure</option>
                    <option>Fiction_Erotica</option>
                    <option>Fiction_Espionage</option>
                    <option>Fiction_Fantasy_General</option>
                    <option>Fiction_Gay</option>
                    <option>Fiction_General</option>
                    <option>Fiction_Historical</option>
                    <option>Fiction_Horror</option>
                    <option>Fiction_Mystery and Detective_General</option>
                    <option>Fiction_Psychological</option>
                    <option>Fiction_Religious</option>
                    <option>Fiction_Romance_General</option>
                    <option>Fiction_Science Fiction_General</option>
                    <option>Fiction_Short Stories (single author)</option>
                    <option>Fiction_Suspense</option>
                    <option>Fiction_Thrillers</option>
                    <option>Fiction_Visionary and Metaphysical</option>
                    <option>Fiction_War and Military</option>
                    <option>Fiction_Westerns</option>
                    <option>Finance, Personal</option>
                    <option>Games</option>
                    <option>Gardening</option>
                    <option>Genealogy</option>
                    <option>Great Britain_History</option>
                    <option>Handicraft</option>
                    <option>Health</option>
                    <option>Interior Decoration</option>
                    <option>Internet</option>
                    <option>Interpersonal Relations</option>
                    <option>Language and Languages_Study and Teaching</option>
                    <option>Language Arts</option>
                    <option>Law</option>
                    <option>Literature_Collections</option>
                    <option>Literature_History and Criticism</option>
                    <option>Mathematics</option>
                    <option>Medicine</option>
                    <option>Military Art and Science</option>
                    <option>Mind and Body</option>
                    <option>Music</option>
                    <option>Nature</option>
                    <option>Nutrition</option>
                    <option>Parenting</option>
                    <option>Performing Arts</option>
                    <option>Pets</option>
                    <option>Philosophy</option>
                    <option>Photography</option>
                    <option>Physical Fitness</option>
                    <option>Physics</option>
                    <option>Poetry (Poetic Works by One Author)</option>
                    <option>Poetry_Collections</option>
                    <option>Poetry_History and Criticism</option>
                    <option>Political Science</option>
                    <option>Psychology</option>
                    <option>Reference Books</option>
                    <option>Religion</option>
                    <option>Science</option>
                    <option>Self-Help Techniques</option>
                    <option>Social Sciences</option>
                    <option>Spirituality</option>
                    <option>Sports</option>
                    <option>Technology</option>
                    <option>Transportation</option>
                    <option>Travel</option>
                    <option>Wit and Humor</option>
                    <option>World History</option>
                </select>
                <br><br><label for="isbn-contributor--name-0">Your name</label>
                <input type="text" id="isbn-contributor--name-0" />
                <br><br>
                <label for="isbn-contributor--bio-0">Your bio</label>
                <textarea id="isbn-contributor--bio-0" class="isbn-info--textarea"></textarea>
                <br><br>
                <label for="isbn-contributor-function-0" required>Your function</label>
                <select id="isbn-contributor-function-0">
                    <option id="isbn-contributor-function-0--author" selected>author</option>
                    <option>editor</option>
                    <option>illustrator</option>
                    <option>various roles</option>
                    <option>abridged by</option>
                    <option>adapted by</option>
                    <option>afterward by</option>
                    <option>animated by</option>
                    <option>annotations by</option>
                    <option>appendix by</option>
                    <option>arranged by</option>
                    <option>artist</option>
                    <option>as told by</option>
                    <option>as told to</option>
                    <option>assisted by</option>
                    <option>associated editor</option>
                    <option>based on a work by</option>
                    <option>book and lyrics by</option>
                    <option>by (photographer)</option>
                    <option>characters by</option>
                    <option>colorist (comic)</option>
                    <option>comic script by</option>
                    <option>commentaries by</option>
                    <option>compiled by</option>
                    <option>composed by</option>
                    <option>concept by</option>
                    <option>conducted by</option>
                    <option>continued by</option>
                    <option>contribution by</option>
                    <option>cover design by</option>
                    <option>curated by</option>
                    <option>demonstrated by</option>
                    <option>designed by</option>
                    <option>directed by</option>
                    <option>drawings by</option>
                    <option>edited and translated by</option>
                    <option>editor-in-chief</option>
                    <option>editorial board member</option>
                    <option>editorial coordinator</option>
                    <option>engineer</option>
                    <option>epilogue by</option>
                    <option>executive producer</option>
                    <option>experiments by</option>
                    <option>featuring</option>
                    <option>filmed by</option>
                    <option>featuring</option>
                    <option>footnotes by</option>
                    <option>foreward by</option>
                    <option>general editor</option>
                    <option>guest editor</option>
                    <option>historical advisor</option>
                    <option>index by</option>
                    <option>inked or colored by</option>
                    <option>inker (comics)</option>
                    <option>instructed by</option>
                    <option>instrumental soloist</option>
                    <option>interviewed by</option>
                    <option>interviewee</option>
                    <option>interviewer</option>
                    <option>intro and notes by</option>
                    <option>introduction by</option>
                    <option>letterer (comics)</option>
                    <option>libretto by</option>
                    <option>literary editor</option>
                    <option>lyrics by</option>
                    <option>managing editor</option>
                    <option>maps by</option>
                    <option>memoir by</option>
                    <option>moderated by</option>
                    <option>music by</option>
                    <option>narrated by</option>
                    <option>non-text materials selected by</option>
                    <option>notes by</option>
                    <option>original author</option>
                    <option>original editor</option>
                    <option>other</option>
                    <option>other adaptation by</option>
                    <option>other direction by</option>
                    <option>other primary creator</option>
                    <option>other recording by</option>
                    <option>other compilation by</option>
                    <option>performed by</option>
                    <option>performed by musical group</option>
                    <option>photographer</option>
                    <option>pop-ups by</option>
                    <option>preface by</option>
                    <option>preliminary work by</option>
                    <option>prepared for publication by</option>
                    <option>presented by</option>
                    <option>produced by</option>
                    <option>prologue by</option>
                    <option>read by</option>
                    <option>research by</option>
                    <option>retold by</option>
                    <option>reviewed by</option>
                    <option>revised by</option>
                    <option>scientific editor</option>
                    <option>score by</option>
                    <option>screenplay by</option>
                    <option>selected by</option>
                    <option>series edited by</option>
                    <option>software by</option>
                    <option>speaker</option>
                    <option>summary by</option>
                    <option>supplement by</option>
                    <option>technical editor</option>
                    <option>text by</option>
                    <option>thesis advisor or supervisor</option>
                    <option>thesis examiner</option>
                    <option>transcribed by</option>
                    <option>translated with commentary by</option>
                    <option>translator</option>
                    <option>voice by</option>
                    <option>volume editor</option>
                </select>
                <br><br>
            </div>
            <div class="isbn-info-overlay-column">      
            <p>Do you want to add another contributor?</p>
                <div class="isbn-radio-option">
                    <input type="radio" name="isbn-contributors-radio-1" id="isbn-contributors--yes-1" class="isbn-radio-yes" data-section="1"/>
                    <label for="isbn-contributors--yes-1" class="isbn-radio-label">yes</label>
                </div>
                <div class="isbn-radio-option">
                    <input type="radio" name="isbn-contributors-radio-1" id="isbn-contributors--no-1"  class="isbn-radio-no" data-section="1"checked/>
                    <label for="isbn-contributors--no-1" class="isbn-radio-label">no</label>
                </div>
                <br><br>          
                <div class="hidden isbn-contributors-section" id="isbn-contributors-section-1">
                    <label for="isbn-contributor--name-1">Contributor name</label>
                    <input type="text" id="isbn-contributor--name-1" />
                    <br><br>
                    <label for="isbn-contributor--bio-1">Contributor bio</label>
                    <textarea id="isbn-contributor--bio-1" class="isbn-info--textarea"></textarea>
                    <br><br>
                    <label for="isbn-contributor-function-1" required>Contributor function</label>
                    <select id="isbn-contributor-function-1">
                        <option id="isbn-contributor-function-1--author" selected>author</option>
                        <option>editor</option>
                        <option>illustrator</option>
                        <option>various roles</option>
                        <option>abridged by</option>
                        <option>adapted by</option>
                        <option>afterward by</option>
                        <option>animated by</option>
                        <option>annotations by</option>
                        <option>appendix by</option>
                        <option>arranged by</option>
                        <option>artist</option>
                        <option>as told by</option>
                        <option>as told to</option>
                        <option>assisted by</option>
                        <option>associated editor</option>
                        <option>based on a work by</option>
                        <option>book and lyrics by</option>
                        <option>by (photographer)</option>
                        <option>characters by</option>
                        <option>colorist (comic)</option>
                        <option>comic script by</option>
                        <option>commentaries by</option>
                        <option>compiled by</option>
                        <option>composed by</option>
                        <option>concept by</option>
                        <option>conducted by</option>
                        <option>continued by</option>
                        <option>contribution by</option>
                        <option>cover design by</option>
                        <option>curated by</option>
                        <option>demonstrated by</option>
                        <option>designed by</option>
                        <option>directed by</option>
                        <option>drawings by</option>
                        <option>edited and translated by</option>
                        <option>editor-in-chief</option>
                        <option>editorial board member</option>
                        <option>editorial coordinator</option>
                        <option>engineer</option>
                        <option>epilogue by</option>
                        <option>executive producer</option>
                        <option>experiments by</option>
                        <option>featuring</option>
                        <option>filmed by</option>
                        <option>featuring</option>
                        <option>footnotes by</option>
                        <option>foreward by</option>
                        <option>general editor</option>
                        <option>guest editor</option>
                        <option>historical advisor</option>
                        <option>index by</option>
                        <option>inked or colored by</option>
                        <option>inker (comics)</option>
                        <option>instructed by</option>
                        <option>instrumental soloist</option>
                        <option>interviewed by</option>
                        <option>interviewee</option>
                        <option>interviewer</option>
                        <option>intro and notes by</option>
                        <option>introduction by</option>
                        <option>letterer (comics)</option>
                        <option>libretto by</option>
                        <option>literary editor</option>
                        <option>lyrics by</option>
                        <option>managing editor</option>
                        <option>maps by</option>
                        <option>memoir by</option>
                        <option>moderated by</option>
                        <option>music by</option>
                        <option>narrated by</option>
                        <option>non-text materials selected by</option>
                        <option>notes by</option>
                        <option>original author</option>
                        <option>original editor</option>
                        <option>other</option>
                        <option>other adaptation by</option>
                        <option>other direction by</option>
                        <option>other primary creator</option>
                        <option>other recording by</option>
                        <option>other compilation by</option>
                        <option>performed by</option>
                        <option>performed by musical group</option>
                        <option>photographer</option>
                        <option>pop-ups by</option>
                        <option>preface by</option>
                        <option>preliminary work by</option>
                        <option>prepared for publication by</option>
                        <option>presented by</option>
                        <option>produced by</option>
                        <option>prologue by</option>
                        <option>read by</option>
                        <option>research by</option>
                        <option>retold by</option>
                        <option>reviewed by</option>
                        <option>revised by</option>
                        <option>scientific editor</option>
                        <option>score by</option>
                        <option>screenplay by</option>
                        <option>selected by</option>
                        <option>series edited by</option>
                        <option>software by</option>
                        <option>speaker</option>
                        <option>summary by</option>
                        <option>supplement by</option>
                        <option>technical editor</option>
                        <option>text by</option>
                        <option>thesis advisor or supervisor</option>
                        <option>thesis examiner</option>
                        <option>transcribed by</option>
                        <option>translated with commentary by</option>
                        <option>translator</option>
                        <option>voice by</option>
                        <option>volume editor</option>
                    </select>
                    <br><br>
                    <p>Do you want to add another contributor?</p>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-2" id="isbn-contributors--yes-2" class="isbn-radio-yes" data-section="2"/>
                        <label for="isbn-contributors--yes-2" class="isbn-radio-label">yes</label>
                    </div>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-2" id="isbn-contributors--no-2" class="isbn-radio-no" data-section="2" checked/>
                        <label for="isbn-contributors--no-2" class="isbn-radio-label">no</label>
                    </div>
                    <br><br>
                </div>
                <div class="hidden isbn-contributors-section" id="isbn-contributors-section-2">
                    <label for="isbn-contributor--name-2">Contributor name</label>
                    <input type="text" id="isbn-contributor--name-2" />
                    <br><br>
                    <label for="isbn-contributor--bio-2">Contributor bio</label>
                    <textarea id="isbn-contributor--bio-2" class="isbn-info--textarea"></textarea>
                    <br><br>
                    <label for="isbn-contributor-function-2" required>Contributor function</label>
                    <select id="isbn-contributor-function-2">
                        <option id="isbn-contributor-function-2--author" selected>author</option>
                        <option>editor</option>
                        <option>illustrator</option>
                        <option>various roles</option>
                        <option>abridged by</option>
                        <option>adapted by</option>
                        <option>afterward by</option>
                        <option>animated by</option>
                        <option>annotations by</option>
                        <option>appendix by</option>
                        <option>arranged by</option>
                        <option>artist</option>
                        <option>as told by</option>
                        <option>as told to</option>
                        <option>assisted by</option>
                        <option>associated editor</option>
                        <option>based on a work by</option>
                        <option>book and lyrics by</option>
                        <option>by (photographer)</option>
                        <option>characters by</option>
                        <option>colorist (comic)</option>
                        <option>comic script by</option>
                        <option>commentaries by</option>
                        <option>compiled by</option>
                        <option>composed by</option>
                        <option>concept by</option>
                        <option>conducted by</option>
                        <option>continued by</option>
                        <option>contribution by</option>
                        <option>cover design by</option>
                        <option>curated by</option>
                        <option>demonstrated by</option>
                        <option>designed by</option>
                        <option>directed by</option>
                        <option>drawings by</option>
                        <option>edited and translated by</option>
                        <option>editor-in-chief</option>
                        <option>editorial board member</option>
                        <option>editorial coordinator</option>
                        <option>engineer</option>
                        <option>epilogue by</option>
                        <option>executive producer</option>
                        <option>experiments by</option>
                        <option>featuring</option>
                        <option>filmed by</option>
                        <option>featuring</option>
                        <option>footnotes by</option>
                        <option>foreward by</option>
                        <option>general editor</option>
                        <option>guest editor</option>
                        <option>historical advisor</option>
                        <option>index by</option>
                        <option>inked or colored by</option>
                        <option>inker (comics)</option>
                        <option>instructed by</option>
                        <option>instrumental soloist</option>
                        <option>interviewed by</option>
                        <option>interviewee</option>
                        <option>interviewer</option>
                        <option>intro and notes by</option>
                        <option>introduction by</option>
                        <option>letterer (comics)</option>
                        <option>libretto by</option>
                        <option>literary editor</option>
                        <option>lyrics by</option>
                        <option>managing editor</option>
                        <option>maps by</option>
                        <option>memoir by</option>
                        <option>moderated by</option>
                        <option>music by</option>
                        <option>narrated by</option>
                        <option>non-text materials selected by</option>
                        <option>notes by</option>
                        <option>original author</option>
                        <option>original editor</option>
                        <option>other</option>
                        <option>other adaptation by</option>
                        <option>other direction by</option>
                        <option>other primary creator</option>
                        <option>other recording by</option>
                        <option>other compilation by</option>
                        <option>performed by</option>
                        <option>performed by musical group</option>
                        <option>photographer</option>
                        <option>pop-ups by</option>
                        <option>preface by</option>
                        <option>preliminary work by</option>
                        <option>prepared for publication by</option>
                        <option>presented by</option>
                        <option>produced by</option>
                        <option>prologue by</option>
                        <option>read by</option>
                        <option>research by</option>
                        <option>retold by</option>
                        <option>reviewed by</option>
                        <option>revised by</option>
                        <option>scientific editor</option>
                        <option>score by</option>
                        <option>screenplay by</option>
                        <option>selected by</option>
                        <option>series edited by</option>
                        <option>software by</option>
                        <option>speaker</option>
                        <option>summary by</option>
                        <option>supplement by</option>
                        <option>technical editor</option>
                        <option>text by</option>
                        <option>thesis advisor or supervisor</option>
                        <option>thesis examiner</option>
                        <option>transcribed by</option>
                        <option>translated with commentary by</option>
                        <option>translator</option>
                        <option>voice by</option>
                        <option>volume editor</option>
                    </select>
                    <br><br>
                    <p>Do you want to add another contributor?</p>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-3" id="isbn-contributors--yes-3" class="isbn-radio-yes" data-section="3"/>
                        <label for="isbn-contributors--yes-3" class="isbn-radio-label">yes</label>
                    </div>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-3" id="isbn-contributors--no-3" class="isbn-radio-no" data-section="3" checked/>
                        <label for="isbn-contributors--no-3" class="isbn-radio-label">no</label>
                    </div>
                    <br><br>
                </div>
                <div class="hidden isbn-contributors-section" id="isbn-contributors-section-3">
                    <label for="isbn-contributor--name-3">Contributor name</label>
                    <input type="text" id="isbn-contributor--name-3" />
                    <br><br>
                    <label for="isbn-contributor--bio-3">Contributor bio</label>
                    <textarea id="isbn-contributor--bio-3" class="isbn-info--textarea"></textarea>
                    <br><br>
                    <label for="isbn-contributor-function-3" required>Contributor function</label>
                    <select id="isbn-contributor-function-3">
                        <option id="isbn-contributor-function-3--author" selected>author</option>
                        <option>editor</option>
                        <option>illustrator</option>
                        <option>various roles</option>
                        <option>abridged by</option>
                        <option>adapted by</option>
                        <option>afterward by</option>
                        <option>animated by</option>
                        <option>annotations by</option>
                        <option>appendix by</option>
                        <option>arranged by</option>
                        <option>artist</option>
                        <option>as told by</option>
                        <option>as told to</option>
                        <option>assisted by</option>
                        <option>associated editor</option>
                        <option>based on a work by</option>
                        <option>book and lyrics by</option>
                        <option>by (photographer)</option>
                        <option>characters by</option>
                        <option>colorist (comic)</option>
                        <option>comic script by</option>
                        <option>commentaries by</option>
                        <option>compiled by</option>
                        <option>composed by</option>
                        <option>concept by</option>
                        <option>conducted by</option>
                        <option>continued by</option>
                        <option>contribution by</option>
                        <option>cover design by</option>
                        <option>curated by</option>
                        <option>demonstrated by</option>
                        <option>designed by</option>
                        <option>directed by</option>
                        <option>drawings by</option>
                        <option>edited and translated by</option>
                        <option>editor-in-chief</option>
                        <option>editorial board member</option>
                        <option>editorial coordinator</option>
                        <option>engineer</option>
                        <option>epilogue by</option>
                        <option>executive producer</option>
                        <option>experiments by</option>
                        <option>featuring</option>
                        <option>filmed by</option>
                        <option>featuring</option>
                        <option>footnotes by</option>
                        <option>foreward by</option>
                        <option>general editor</option>
                        <option>guest editor</option>
                        <option>historical advisor</option>
                        <option>index by</option>
                        <option>inked or colored by</option>
                        <option>inker (comics)</option>
                        <option>instructed by</option>
                        <option>instrumental soloist</option>
                        <option>interviewed by</option>
                        <option>interviewee</option>
                        <option>interviewer</option>
                        <option>intro and notes by</option>
                        <option>introduction by</option>
                        <option>letterer (comics)</option>
                        <option>libretto by</option>
                        <option>literary editor</option>
                        <option>lyrics by</option>
                        <option>managing editor</option>
                        <option>maps by</option>
                        <option>memoir by</option>
                        <option>moderated by</option>
                        <option>music by</option>
                        <option>narrated by</option>
                        <option>non-text materials selected by</option>
                        <option>notes by</option>
                        <option>original author</option>
                        <option>original editor</option>
                        <option>other</option>
                        <option>other adaptation by</option>
                        <option>other direction by</option>
                        <option>other primary creator</option>
                        <option>other recording by</option>
                        <option>other compilation by</option>
                        <option>performed by</option>
                        <option>performed by musical group</option>
                        <option>photographer</option>
                        <option>pop-ups by</option>
                        <option>preface by</option>
                        <option>preliminary work by</option>
                        <option>prepared for publication by</option>
                        <option>presented by</option>
                        <option>produced by</option>
                        <option>prologue by</option>
                        <option>read by</option>
                        <option>research by</option>
                        <option>retold by</option>
                        <option>reviewed by</option>
                        <option>revised by</option>
                        <option>scientific editor</option>
                        <option>score by</option>
                        <option>screenplay by</option>
                        <option>selected by</option>
                        <option>series edited by</option>
                        <option>software by</option>
                        <option>speaker</option>
                        <option>summary by</option>
                        <option>supplement by</option>
                        <option>technical editor</option>
                        <option>text by</option>
                        <option>thesis advisor or supervisor</option>
                        <option>thesis examiner</option>
                        <option>transcribed by</option>
                        <option>translated with commentary by</option>
                        <option>translator</option>
                        <option>voice by</option>
                        <option>volume editor</option>
                    </select>
                    <br><br>
                    <p>Do you want to add another contributor?</p>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-4" id="isbn-contributors--yes-4" class="isbn-radio-yes" data-section="4"/>
                        <label for="isbn-contributors--yes-4" class="isbn-radio-label">yes</label>
                    </div>
                    <div class="isbn-radio-option">
                        <input type="radio" name="isbn-contributors-radio-4" id="isbn-contributors--no-4" class="isbn-radio-no" data-section="4" checked/>
                        <label for="isbn-contributors--no-4" class="isbn-radio-label">no</label>
                    </div>
                    <br><br>
                </div>
                <div class="hidden isbn-contributors-section" id="isbn-contributors-section-4">
                    <label for="isbn-contributor--name-4">Contributor name</label>
                    <input type="text" id="isbn-contributor--name-4" />
                    <br><br>
                    <label for="isbn-contributor--bio-4">Contributor bio</label>
                    <textarea id="isbn-contributor--bio-4" class="isbn-info--textarea"></textarea>
                    <br><br>
                    <label for="isbn-contributor-function-4" required>Contributor function</label>
                    <select id="isbn-contributor-function-4">
                        <option id="isbn-contributor-function-4--author" selected>author</option>
                        <option>editor</option>
                        <option>illustrator</option>
                        <option>various roles</option>
                        <option>abridged by</option>
                        <option>adapted by</option>
                        <option>afterward by</option>
                        <option>animated by</option>
                        <option>annotations by</option>
                        <option>appendix by</option>
                        <option>arranged by</option>
                        <option>artist</option>
                        <option>as told by</option>
                        <option>as told to</option>
                        <option>assisted by</option>
                        <option>associated editor</option>
                        <option>based on a work by</option>
                        <option>book and lyrics by</option>
                        <option>by (photographer)</option>
                        <option>characters by</option>
                        <option>colorist (comic)</option>
                        <option>comic script by</option>
                        <option>commentaries by</option>
                        <option>compiled by</option>
                        <option>composed by</option>
                        <option>concept by</option>
                        <option>conducted by</option>
                        <option>continued by</option>
                        <option>contribution by</option>
                        <option>cover design by</option>
                        <option>curated by</option>
                        <option>demonstrated by</option>
                        <option>designed by</option>
                        <option>directed by</option>
                        <option>drawings by</option>
                        <option>edited and translated by</option>
                        <option>editor-in-chief</option>
                        <option>editorial board member</option>
                        <option>editorial coordinator</option>
                        <option>engineer</option>
                        <option>epilogue by</option>
                        <option>executive producer</option>
                        <option>experiments by</option>
                        <option>featuring</option>
                        <option>filmed by</option>
                        <option>featuring</option>
                        <option>footnotes by</option>
                        <option>foreward by</option>
                        <option>general editor</option>
                        <option>guest editor</option>
                        <option>historical advisor</option>
                        <option>index by</option>
                        <option>inked or colored by</option>
                        <option>inker (comics)</option>
                        <option>instructed by</option>
                        <option>instrumental soloist</option>
                        <option>interviewed by</option>
                        <option>interviewee</option>
                        <option>interviewer</option>
                        <option>intro and notes by</option>
                        <option>introduction by</option>
                        <option>letterer (comics)</option>
                        <option>libretto by</option>
                        <option>literary editor</option>
                        <option>lyrics by</option>
                        <option>managing editor</option>
                        <option>maps by</option>
                        <option>memoir by</option>
                        <option>moderated by</option>
                        <option>music by</option>
                        <option>narrated by</option>
                        <option>non-text materials selected by</option>
                        <option>notes by</option>
                        <option>original author</option>
                        <option>original editor</option>
                        <option>other</option>
                        <option>other adaptation by</option>
                        <option>other direction by</option>
                        <option>other primary creator</option>
                        <option>other recording by</option>
                        <option>other compilation by</option>
                        <option>performed by</option>
                        <option>performed by musical group</option>
                        <option>photographer</option>
                        <option>pop-ups by</option>
                        <option>preface by</option>
                        <option>preliminary work by</option>
                        <option>prepared for publication by</option>
                        <option>presented by</option>
                        <option>produced by</option>
                        <option>prologue by</option>
                        <option>read by</option>
                        <option>research by</option>
                        <option>retold by</option>
                        <option>reviewed by</option>
                        <option>revised by</option>
                        <option>scientific editor</option>
                        <option>score by</option>
                        <option>screenplay by</option>
                        <option>selected by</option>
                        <option>series edited by</option>
                        <option>software by</option>
                        <option>speaker</option>
                        <option>summary by</option>
                        <option>supplement by</option>
                        <option>technical editor</option>
                        <option>text by</option>
                        <option>thesis advisor or supervisor</option>
                        <option>thesis examiner</option>
                        <option>transcribed by</option>
                        <option>translated with commentary by</option>
                        <option>translator</option>
                        <option>voice by</option>
                        <option>volume editor</option>
                    </select>
                    <br><br>
                </div>
                <label for="isbn-info--publication-date">Publication date</label>
                <input type="date" id="isbn-info--publication-date" required />
                <br><br>
                <label for="isbn-info--status">Title Status</label>
                <select id="isbn-info--status">
                    <option id="isbn-info--status-forthcoming">forthcoming</option>
                    <option id="isbn-info--status-active" selected>active record</option>
                    <option>import to order</option>
                    <option>inactive</option>
                    <option>on demand</option>
                    <option>out of stock</option>
                    <option>out of print indefinitely</option>
                    <option>postponed</option>
                    <option>publication canceled</option>
                    <option>recalled</option>
                    <option>remaindered</option>
                    <option>temporarily withdrawn from sale</option>
                    <option>unknown</option>
                    <option>unspecified</option>
                    <option>withdrawn</option>
                </select>
                <br><br>
                <label for="isbn-info--target-audience">Target audience (note: for adult fiction, select "trade")</label>
                <select id="isbn-info--target-audience" required>
                    <option>adult education</option>
                    <option>college audience</option>
                    <option>elementary/high school</option>
                    <option>english as a second language</option>
                    <option>family</option>
                    <option>juvenile audience</option>
                    <option>lower secondary education</option>
                    <option>pre-primary education</option>
                    <option>scholarly & professional</option>
                    <option>second language teaching</option>
                    <option id="isbn-info--target-audience--trade" selected>trade</option>
                    <option>upper secondary education</option>
                    <option>young adult audience</option>
                </select>
                <br><br>
                <label for="isbn-info--price">Book price</label>
                <input type="text" id="isbn-info--price" required />
                <br><br>
                <label for="isbn-info--language">Language (optional)</label>
                <input type="text" id="isbn-info--language" />
                <br><br>
                <label for="isbn-info--copyright">Copyright year (optional)</label>
                <input type="text" id="isbn-info--copyright" />
                <br><br>
                <label for="isbn-info--control">Library of Congress control number (optional)</label>
                <input type="text" id="isbn-info--control" />
                <br><br>
                <label for="isbn-info--translated-title">Translated title (optional)</label>
                <input type="text" id="isbn-info--translated-title" />
                <br><br>
                <label for="isbn-info--book-size">Format details (book size) (optional)</label>
                <input type="text" id="isbn-info--book-size" />
                <br><br>
                <label for="isbn-info--number-pages">Number of pages (optional)</label>
                <input type="text" id="isbn-info--number-pages" />
                <br><br>
                <label for="isbn-info--number-illustrations">Number of illustrations (optional)</label>
                <input type="text" id="isbn-info--number-illustrations" />
                <br><br>
                <div id="tomc-info--submission-errors"></div>
                <button id="isbn-info--save" class="hollow-purple-button">Save and Close</button>
                <button id="isbn-info--submit" class="blue-button">Submit for Filing</button>
            </div>
        </div>        
    </div>
</main>

<?php get_footer(); ?>