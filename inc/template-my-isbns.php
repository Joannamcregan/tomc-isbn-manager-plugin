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
        <br><br>
        <label for="isbn-info--first-genre" required>First Genre</label>
        <select id="isbn-info--first-genre">
            <option>Agriculture</option>
            <option>Architecture</option>
            <option>Art</option>
            <option>Astrology</option>
            <option> Bible_Commentaries</option>
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
            <option> Fiction_Fantasy_General</option>
            <option> Fiction_Gay</option>
            <option> Fiction_General</option>
            <option> Fiction_Historical</option>
            <option>Fiction_Horror</option>
            <option>Fiction_Mystery and Detective_General</option>
            <option>Fiction_Psychological</option>
            <option>Fiction_Religious</option>
            <option> Fiction_Romance_General</option>
            <option>Fiction_Science Fiction_General</option>
            <option>Fiction_Short Stories (single author)</option>
            <option> Fiction_Suspense</option>
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
            <option> Literature_Collections</option>
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
        <label for="isbn-info--second-genre" required>Second Genre (optional)</label>
        <select id="isbn-info--second-genre">
            <option>Agriculture</option>
            <option>Architecture</option>
            <option>Art</option>
            <option>Astrology</option>
            <option> Bible_Commentaries</option>
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
            <option> Fiction_Fantasy_General</option>
            <option> Fiction_Gay</option>
            <option> Fiction_General</option>
            <option> Fiction_Historical</option>
            <option>Fiction_Horror</option>
            <option>Fiction_Mystery and Detective_General</option>
            <option>Fiction_Psychological</option>
            <option>Fiction_Religious</option>
            <option> Fiction_Romance_General</option>
            <option>Fiction_Science Fiction_General</option>
            <option>Fiction_Short Stories (single author)</option>
            <option> Fiction_Suspense</option>
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
        <label for="isbn-contributor--name-0">Your name</label>
        <input type="text" id="isbn-contributor--name-0" />
        <br><br>
        <label for="isbn-contributor--bio-0">Your bio</label>
        <input type="textarea" id="isbn-contributor--bio-0" />
        <br><br>
        <label for="isbn-contributor-function-0" required>Your function</label>
        <select id="isbn-contributor-function-0">
                <option>author</option>
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
        <span>Do you want to add another contributor?</span>
        <br>
        <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--yes-1"/>
        <label for="isbn-contributors--yes-1">yes</label>
        <br>
        <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--no-1" checked/>
        <label for="isbn-contributors--no-1">no</label>
        <br><br>
        <div class="hidden" id="isbn-contributors-section-1">
            <label for="isbn-contributor--name-1">Contributor name</label>
            <input type="text" id="isbn-contributor--name-1" />
            <br><br>
            <label for="isbn-contributor--bio-1">Contributor bio</label>
            <input type="textarea" id="isbn-contributor--bio-1" />
            <br><br>
            <label for="isbn-contributor-function-1" required>Your function</label>
            <select id="isbn-contributor-function-1">
                <option>author</option>
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
            <span>Do you want to add another contributor?</span>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--yes-2"/>
            <label for="isbn-contributors--yes-2">yes</label>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--no-2" checked/>
            <label for="isbn-contributors--no-2">no</label>
        </div>
        <div class="hidden" id="isbn-contributors-section-2">
            <label for="isbn-contributor--name-2">Contributor name</label>
            <input type="text" id="isbn-contributor--name-2" />
            <br><br>
            <label for="isbn-contributor--bio-2">Contributor bio</label>
            <input type="textarea" id="isbn-contributor--bio-2" />
            <br><br>
            <label for="isbn-contributor-function-2" required>Your function</label>
            <select id="isbn-contributor-function-2">
                <option>author</option>
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
            <span>Do you want to add another contributor?</span>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--yes-3"/>
            <label for="isbn-contributors--yes-3">yes</label>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--no-3" checked/>
            <label for="isbn-contributors--no-3">no</label>
        </div>
        <div class="hidden" id="isbn-contributors-section-3">
            <label for="isbn-contributor--name-3">Contributor name</label>
            <input type="text" id="isbn-contributor--name-3" />
            <br><br>
            <label for="isbn-contributor--bio-3">Contributor bio</label>
            <input type="textarea" id="isbn-contributor--bio-3" />
            <br><br>
            <label for="isbn-contributor-function-3" required>Your function</label>
            <select id="isbn-contributor-function-3">
                <option>author</option>
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
            <span>Do you want to add another contributor?</span>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--yes-4"/>
            <label for="isbn-contributors--yes-4">yes</label>
            <br>
            <input type="radio" name="isbn-contributors-radio" id="isbn-contributors--no-4" checked/>
            <label for="isbn-contributors--no-4">no</label>
        </div>
        <div class="hidden" id="isbn-contributors-section-4">
            <label for="isbn-contributor--name-4">Contributor name</label>
            <input type="text" id="isbn-contributor--name-4" />
            <br><br>
            <label for="isbn-contributor--bio-4">Contributor bio</label>
            <input type="textarea" id="isbn-contributor--bio-4" />
            <br><br>
            <label for="isbn-contributor-function-4" required>Your function</label>
            <select id="isbn-contributor-function-4">
                <option>author</option>
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
        </div>
    </div>
</main>

<?php get_footer(); ?>