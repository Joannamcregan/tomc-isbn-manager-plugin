import $ from 'jquery';

class ISBNRecords{
    constructor(){
        
        this.events();
    }
    events(){
        // this.formSection.on('load', setTimeout(this.populate.bind(this), 300));
        // this.product.on('change', this.populate.bind(this));
    }
    populate(){
        var productId = this.product.val();
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getUnfiledRecords',
            type: 'GET',
            data: {
                'productId': productId
            },
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