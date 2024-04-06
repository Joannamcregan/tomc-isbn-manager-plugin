import $ from 'jquery';

class ISBNForm{
    constructor(){
        this.product = $('#tomc_isbn_product');
        this.events();
    }
    events(){
        this.product.on('change', this.populate.bind(this));
    }
    populate(){
        var productId = this.product.val();
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/populate',
            type: 'POST',
            data: {
                'productId': productId
            },
            success: (response) => {
                console.log(response);
                for(let i = 0; i < response.length; i++){}
            },
            error: (response) => {
                console.log(response);
            }
        })
    }
}

export default ISBNForm;