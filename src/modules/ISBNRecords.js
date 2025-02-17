import $ from 'jquery';

class ISBNRecords{
    constructor(){
        this.getFiled = $('#tomc-isbn-get-filed-records');
        this.filedSection = $('#tomc-isbn-filed-records-container');
        this.showInfoButton = $('.see-isbn-info-button');
        this.overlay = $('#tomc-isbn-view-info-overlay');
        this.closeOverlayButton = $('#isbn-view-overlay__close');
        this.overlayContainer = $('#isbn-view--container');
        this.events();
    }

    events(){
        this.getFiled.on('click', this.getMoreFiledRecords.bind(this));
        this.showInfoButton.on('click', this.showInfo.bind(this));
    }

    showInfo(e){
        let isbn = $(e.target).parent('div').data('isbn');
        $(e.target).addClass('contracting');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFieldValues',
            type: 'GET',
            data: {
                'isbn' : isbn
            },
            success: (response) => {
                console.log(response);
                this.overlay.find('h2').append(' ' + isbn);
                this.overlay.addClass('search-overlay--active');
                for (let i = 0; i < response.length; i++){
                    let p = $('<p />').addClass(i % 2 == 0 ? 'purple-field' : 'plain-field');
                    let strong = $('<strong />').text(response[i]['fieldlabel'] + ': ');
                    p.append(strong);
                    let span = $('<span />').text(response[i]['fieldvalue']);
                    p.append(span);
                    this.overlayContainer.append(p);
                }
            },
            failure: (response) => {
                // console.log(response);
            }
        })
    }

    markCompleted(e){
        var recordId = $(e.target).parent('.tomc-isbn-hidden-fields').parent('.tomc-isbn-record').data('isbn-product-id');
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
                // console.log(response);
                if (response > 0){
                    location.reload(true);
                }
            },
            failure: (response) => {
                // console.log(response);
            }
        })
    }

    getMoreFiledRecords(){
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getMoreFiledRecords',
            type: 'GET',
            success: (response) => {
                console.log(response);
            },
            error: (response) => {
                console.log(response);
            }
        })
    }
}

export default ISBNRecords;