import $ from 'jquery';

class ISBNRecords{
    constructor(){
        this.getFiled = $('#tomc-isbn-get-filed-records');
        this.filedSection = $('#tomc-isbn-filed-records-container');
        this.showInfoButton = $('.see-isbn-info-button');
        this.overlay = $('#tomc-isbn-view-info-overlay');
        this.closeOverlayButton = $('#isbn-view-overlay__close');
        this.overlayContainer = $('#isbn-view--container');
        this.markFiledButton = $('#tomc-isbn-mark-filed');
        this.events();
        this.recordid;
    }

    events(){
        this.getFiled.on('click', this.getMoreFiledRecords.bind(this));
        this.showInfoButton.on('click', this.showInfo.bind(this));
        this.markFiledButton.on('click', this.markCompleted.bind(this));
    }

    showInfo(e){
        let isbn = $(e.target).parent('div').data('isbn');
        this.recordid = $(e.target).parent('div').data('recordid');
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
                this.overlay.find('h2').append(' ' + isbn);
                this.overlay.addClass('search-overlay--active');
                for (let i = 0; i < response.length; i++){
                    let p = $('<p />').addClass(i % 2 == 0 ? 'tomc-purple-paragraph' : 'tomc-plain-paragraph');
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
        console.log(this.recordid);
        $(e.target).addClass('contracting');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/markRecordFiled',
            type: 'POST',
            data: {
                'recordId' : this.recordid
            },
            success: (response) => {
                $(e.target).removeClass('contracting');
            },
            failure: (response) => {
                // console.log(response);
            }
        })
    }

    getMoreFiledRecords(e){
        let shownCount = $(e.target).data('count');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getMoreFiledRecords',
            type: 'GET',
            data: {
                'shownCount' : shownCount
            },
            success: (response) => {
                // console.log(response);
                shownCount += response.length;
                $(e.target).data('count', shownCount);
            },
            error: (response) => {
                // console.log(response);
            }
        })
    }
}

export default ISBNRecords;