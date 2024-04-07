import $ from 'jquery';

class ISBNForm{
    constructor(){
        this.product = $('#tomc_isbn_product');
        this.title = $('#tomc_isbn_title');
        this.subtitle = $('#tomc_isbn_subtitle');
        this.description = $('#tomc_isbn_description');
        this.format = $('#tomc_isbn_format');
        this.contributor1 = $('#tomc_isbn_contributor1');
        this.biography1 = $('#tomc_isbn_biography1');
        this.publicationdate = $('#tomc_isbn_publication_date');
        this.status = $('#tomc_isbn_status');
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
                if (response.length > 0){
                    this.title.val(response[0]['title']);
                    this.subtitle.val(response[0]['subtitle']);
                    this.description.val(response[0]['description']);
                    this.format.val(response[0]['format']);
                    this.contributor1.val(response[0]['contributor']);
                    this.biography1.val(response[0]['biography']);
                    this.publicationdate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
                    this.status.val(response[0]['islive'] === 1 ? 'status_active' : 'status_forthcoming');
                }
            },
            error: (response) => {
                console.log(response);
            }
        })
    }
}

export default ISBNForm;