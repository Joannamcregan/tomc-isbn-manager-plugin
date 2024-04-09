import $ from 'jquery';

class ISBNRecords{
    constructor(){
        this.getUnfiled = $('#tomc-isbn-get-unfiled-records');
        this.getFiled = $('#tomc-isbn-get-filed-records');
        this.unfiledSection = $('#tomc-isbn-unfiled-records-container');
        this.filedSection = $('#tomc-isbn-filed-records-container');
        this.events();
    }
    events(){
        this.getUnfiled.on('click', this.getUnfiledRecords.bind(this));
        this.getFiled.on('click', this.getFiledRecords.bind(this));
    }
    toggleHiddenFields(e){
        $(e.target).parent('.tomc-isbn-record').children('.tomc-isbn-hidden-fields').toggleClass('hidden');
        console.log('hidden toggle called');
    }
    markCompleted(e){
        var recordId = $(e.target).parent('.tomc-isbn-hidden-fields').parent('.tomc-isbn-record').data('isbn-for');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/markRecordFiled',
            type: 'POST',
            data: {
                'recordId' : recordId
            },
            success: (response) => {
                console.log(response);
                if (response > 0){
                    // location.reload(true);
                }
            },
            failure: (response) => {
                console.log(response);
            }
        })
    }
    getUnfiledRecords(){
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getUnfiledRecords',
            type: 'GET',
            success: (response) => {
                if(response.length){
                    for(let i = 0; i < response.length; i++){
                        this.newDiv = $('<div />').addClass('tomc-isbn-record').attr('data-isbn-for', response[i]['isbn_for']);
                        this.field = $('<h2 />').addClass('centered-text tomc-book-options--cursor-pointer blue-text').html('<strong>Title:</strong> ' + response[i]['title']).on('click', this.toggleHiddenFields.bind(this));
                        this.newDiv.append(this.field);
                        this.hiddenSection = $('<div />').addClass('hidden tomc-isbn-hidden-fields');                    
                        this.field = $('<p />').html('<strong>Subtitle:</strong> ' + response[i]['subtitle']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Description:</strong> ' + response[i]['description']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Format:</strong> ' + response[i]['format']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>First Genre:</strong> ' + response[i]['first_genre']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Second Genre:</strong> ' + response[i]['second_genre']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Author Name:</strong> ' + response[i]['contributor1']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Author Biography:</strong> ' + response[i]['biography1']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor2']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function2']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography2']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor3']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function3']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography3']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Publication Date:</strong> ' + response[i]['publication_date']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Publication Status:</strong> ' + response[i]['publication_status']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Target Audience:</strong> ' + response[i]['target_audience']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Current Price:</strong> ' + response[i]['book_price']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Language:</strong> ' + response[i]['book_language']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Copyright Year:</strong> ' + response[i]['copyright_year']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Library of Congress Control Number:</strong> ' + response[i]['control_number']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Translated Title:</strong> ' + response[i]['translated_title']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Number of Pages:</strong> ' + response[i]['number_of_pages']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Number of Illustrations:</strong> ' + response[i]['number_of_illustrations']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<span />').html('mark as submitted').addClass('tomc-isbn-submit').on('click', this.markCompleted.bind(this));
                        this.hiddenSection.append(this.field);
                        this.newDiv.append(this.hiddenSection);
                        this.unfiledSection.append(this.newDiv)
                    }
                    this.getUnfiled.addClass('hidden');
                    this.getUnfiled.removeClass('block');
                } else {
                    this.newDiv = $('<div />').addClass('tomc-isbn-record');
                    this.field = $('<p />').addClass('centered-text tomc-book-options--cursor-pointer').html('No records found');
                    this.newDiv.append(this.field);
                    this.unfiledSection.append(this.newDiv);
                }   
            },
            error: (response) => {
                console.log(response);
            }
        })
    }
    getFiledRecords(){
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFiledRecords',
            type: 'GET',
            success: (response) => {
                console.log(response);
                if (response.length){
                    for(let i = 0; i < response.length; i++){
                        this.newDiv = $('<div />').addClass('tomc-isbn-record').attr('data-isbn-for', response[i]['isbn_for']);
                        this.field = $('<h2 />').addClass('centered-text tomc-book-options--cursor-pointer blue-text').html('<strong>Title:</strong> ' + response[i]['title']).on('click', this.toggleHiddenFields.bind(this));
                        this.newDiv.append(this.field);
                        this.hiddenSection = $('<div />').addClass('hidden tomc-isbn-hidden-fields');                    
                        this.field = $('<p />').html('<strong>Subtitle:</strong> ' + response[i]['subtitle']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Description:</strong> ' + response[i]['description']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Format:</strong> ' + response[i]['format']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>First Genre:</strong> ' + response[i]['first_genre']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Second Genre:</strong> ' + response[i]['second_genre']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Author Name:</strong> ' + response[i]['contributor1']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Author Biography:</strong> ' + response[i]['biography1']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor2']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function2']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography2']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor3']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function3']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography3']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Publication Date:</strong> ' + response[i]['publication_date']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Publication Status:</strong> ' + response[i]['publication_status']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Target Audience:</strong> ' + response[i]['target_audience']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Current Price:</strong> ' + response[i]['book_price']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Language:</strong> ' + response[i]['book_language']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Copyright Year:</strong> ' + response[i]['copyright_year']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Library of Congress Control Number:</strong> ' + response[i]['control_number']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Translated Title:</strong> ' + response[i]['translated_title']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Number of Pages:</strong> ' + response[i]['number_of_pages']).addClass('tomc-plain-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.field = $('<p />').html('<strong>Number of Illustrations:</strong> ' + response[i]['number_of_illustrations']).addClass('tomc-purple-isbn-field');
                        this.hiddenSection.append(this.field);
                        this.newDiv.append(this.hiddenSection);
                        this.filedSection.append(this.newDiv)
                    }
                } else {
                    this.newDiv = $('<div />').addClass('tomc-isbn-record');
                    this.field = $('<p />').addClass('centered-text tomc-book-options--cursor-pointer').html('No records found');
                    this.newDiv.append(this.field);
                    this.filedSection.append(this.newDiv);
                }                
                this.getFiled.addClass('hidden');
                this.getFiled.removeClass('block');
            },
            error: (response) => {
                console.log(response);
            }
        })
    }
}

export default ISBNRecords;