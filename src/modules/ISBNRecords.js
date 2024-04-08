import $ from 'jquery';

class ISBNRecords{
    constructor(){
        this.getUnfiled = $('#tomc-isbn-get-unfiled-records');
        this.getFiled = $('#tomc-isbn-get-filed-records');
        this.events();
    }
    events(){
        this.getUnfiled.on('click', this.getUnfiledRecords.bind(this));
    }
    getUnfiledRecords(){
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getUnfiledRecords',
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