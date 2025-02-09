import $ from 'jquery';

class ISBNRegistrations{
    constructor(){
        this.addInfoButtons = $('.add-isbn-info-button');
        this.isbnInfoOverlay = $('#tomc-isbn-edit-info-overlay');
        this.overlayCloseButton = $('#isbn-info-overlay__close');
        this.audioSection = $('#isbn-info--audio-section');
        this.ebookSection = $('#isbn-info--ebook-section');
        this.printSection = $('#isbn-info--print-section');
        this.mediumSelect = $('#isbn-info--book-medium');
        this.assignedProductDropdown = $('#isbn-info--assigned-product');
        this.titleField = $('#isbn-info--book-title');
        this.subtitleField = $('#isbn-info--book-subtitle');
        this.descriptionField = $('#isbn-info--book-description');
        this.firstGenreDropdown = $('#isbn-info--first-genre');
        this.contributorsSection = $('.isbn-contributors-section');
        this.radioYes = $('.isbn-radio-yes');
        this.radioNo = $('.isbn-radio-no');
        this.contributor0 = $('#isbn-contributor--name-0');
        this.biography0 = $('#isbn-contributor--bio-0');
        this.publicationDate = $('#isbn-info--publication-date');
        this.statusSelect = $('#isbn-info--status');
        this.priceField = $('#isbn-info--price');
        this.languageField = $('#isbn-info--language');
        this.events();
    }
    events(){
        this.addInfoButtons.on('click', this.showInfo.bind(this));
        this.overlayCloseButton.on('click', this.closeOverlay.bind(this));
        this.mediumSelect.on('change', (e)=>{
            $('.isbn-info--format-section').addClass('hidden');
            $('#isbn-info--section-' + $(e.target).val()).removeClass('hidden');
        })
        this.radioYes.on('change', (e)=>{
            if ($(e.target).prop("checked", true)){
                $('#isbn-contributors-section-' + $(e.target).data('section')).removeClass('hidden');
            }
        })
        this.radioNo.on('change', (e)=>{
            if ($(e.target).prop("checked", true)){
                $('#isbn-contributors-section-' + $(e.target).data('section')).addClass('hidden');
            }
        })
        this.assignedProductDropdown.on('change', this.autofill.bind(this));
    }
    autofill(e){
        let productId = $(e.target).find(":selected").data('productid');
        if (productId > 0){
            $(e.target).addClass('contracting');
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
                },
                url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/populateByProduct',
                type: 'GET',
                data: {
                    'productId': productId
                },
                success: (response) => {
                    $(e.target).removeClass('contracting');
                    if (response.length > 0){
                        this.titleField.val(response[0]['title']);
                        this.subtitleField.val(response[0]['subtitle']);
                        this.descriptionField.val(response[0]['description']);
                        $('.isbn-info--format-select').val(response[0]['format']);
                        this.contributor0.val(response[0]['contributor']);
                        this.biography0.val(response[0]['biography']);
                        // this.publicationDate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
                        this.statusSelect.val(response[0]['islive'] === 1 ? 'status_active' : 'status_forthcoming');
                        this.priceField.val('$' + response[0]['price']);
                        this.languageField.val(response[0]['language']);
                    }
                },
                error: (response) => {
                    console.log(response);
                }
            })
        }
    }
    showInfo(e){
        console.log($(e.target).closest('.tomc-isbn-field-section').data('isbn'));
        this.isbnInfoOverlay.addClass('search-overlay--active');
        this.isbnInfoOverlay.find('h2').append($(e.target).closest('.tomc-isbn-field-section').data('isbn'));
    }
    closeOverlay(e){
        this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
        this.isbnInfoOverlay.removeClass('search-overlay--active');
    }
}

export default ISBNRegistrations;