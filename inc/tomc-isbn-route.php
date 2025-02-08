<?php

add_action('rest_api_init', 'tomcIsbnRegisterRoute');

function tomcIsbnRegisterRoute() {
    register_rest_route('tomcISBN/v1', 'getUnfiledRecords', array(
        'methods' => 'GET',
        'callback' => 'getUnfiledRecords'
    ));
    register_rest_route('tomcISBN/v1', 'getFiledRecords', array(
        'methods' => 'GET',
        'callback' => 'getFiledRecords'
    ));
    register_rest_route('tomcISBN/v1', 'markRecordFiled', array(
        'methods' => 'POST',
        'callback' => 'markRecordFiled'
    ));
}

//redo to just update isbn_records table
function markRecordFiled($data){
    $recordId = sanitize_text_field($data['recordId']);
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        $userId = get_current_user_id();
        global $wpdb;
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $query = '';
        $record = [];
        $record['shoporderid'] = $recordId;
        $record['processeddate'] = date('Y-m-d H:i:s');
        $record['processedby'] = $userId;
        $wpdb->insert($isbn_records_table, $record);
        $recordId = $wpdb->insert_id;
        return $recordId;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//will need to go through each field in select statement
function getUnfiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $isbn_field_values_table = $wpdb->prefix . "tomc_isbn_field_values";
        $query = 'select * 
        from %i records
        join %i numbers on records.isbnid = numbers.id
        join %i fieldvalues on fieldvalues.isbnid = numbers.id
        
        where records.submitteddate is not null
        order by records.submitteddate';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $postmeta_table, $posts_table, $users_table, $postmeta_table, $isbn_records_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//update select statement
function getFiledRecords(){
    $user = wp_get_current_user();
    if (is_user_logged_in() && (in_array( 'administrator', (array) $user->roles ) )){
        global $wpdb;
        $posts_table = $wpdb->prefix . "posts";
        $postmeta_table = $wpdb->prefix . "postmeta";
        $users_table = $wpdb->prefix . "users";
        $isbn_records_table = $wpdb->prefix . "tomc_isbn_records";
        $isbn_numbers_table = $wpdb->prefix . "tomc_isbn_numbers";
        $query = 'select ';
        $results = $wpdb->get_results($wpdb->prepare($query, $posts_table, $isbn_numbers_table, $postmeta_table, $posts_table, $users_table, $postmeta_table, $isbn_records_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table), ARRAY_A);
        // return $wpdb->prepare($query, $posts_table, $postmeta_table, $isbn_records_table, $posts_table, $users_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table, $postmeta_table);
        return $results;
    } else {
        wp_safe_redirect(site_url('/my-account'));
        return 'fail';
    }
}

//also need route to get status of filed records for users

//also need route to get fields to enter and save field values

//also need route to edit field values

//also need route to submit isbn info for filing
