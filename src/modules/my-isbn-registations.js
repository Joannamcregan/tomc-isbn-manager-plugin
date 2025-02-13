import $, { timers } from 'jquery';

class ISBNRegistrations{
    constructor(){
        this.isbnid;
        this.addInfoButtons = $('.add-isbn-info-button');
        this.isbnInfoOverlay = $('#tomc-isbn-edit-info-overlay');
        this.overlayCloseButton = $('#isbn-info-overlay__close');
        this.audioSection = $('#isbn-info--audio-section');
        this.ebookSection = $('#isbn-info--ebook-section');
        this.printSection = $('#isbn-info--print-section');
        this.mediumSelect = $('#isbn-info--book-medium');
        this.assignedProductDropdown = $('#isbn-info--assigned-product');
        this.assignedProductError = $('#isbn-info--assigned-book-error');
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
        this.submissionErrorSection = $('#tomc-info--submission-errors');
        this.submitButton = $('#isbn-info--submit');
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
        this.submitButton.on('click', this.submit.bind(this));
    }
    submit(e){
        let fieldVals = [];

        let productId = this.assignedProductDropdown.find(':selected').data('productid');
        let assignedProduct = this.assignedProductDropdown.val();
        let title = this.titleField.val();
        let description = this.descriptionField.val();
        let name0 = this.contributor0.val();
        let bio0 = this.biography0.val();
        let bookMedium = this.mediumSelect.val();
        let bookFormat = $('.isbn-info--format-select').val();
        let pubDate = this.publicationDate.val();
        let status = this.statusSelect.val();
        let price = this.priceField.val();

        if (assignedProduct != '' && title != ''  && description != '' && name0 != '' && bio0 != ''
        && bookMedium != '' && bookFormat != '' && pubDate != '' && pubDate != 'mm/dd/yyyy'
        && status != '' && price != '' && this.assignedProductError.hasClass('hidden')){
            $(e.target).addClass('contracting');
            this.overlayCloseButton.addClass('hidden'); //keep people from closing overlay while saving/submitting
            this.submissionErrorSection.addClass('hidden');
            fieldVals.push({ field: $('label[for="isbn-info--assigned-product"]').text(), value: productId});
            fieldVals.push({ field: $('label[for="isbn-info--book-title"]').text(), value: title});
            if (this.subtitleField.val() != ''){
                fieldVals.push({ field: $('label[for="isbn-info--book-subtitle"]').text(), value: this.subtitleField.val()})
            }
            fieldVals.push({ field: $('label[for="isbn-info--book-description"]').text(), value: description})
            
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
                },
                url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/saveFieldValues',
                type: 'GET',
                data: {
                    'isbnid': this.isbnid,
                    'fieldVals': JSON.stringify(fieldVals)
                },
                success: (response) => {
                    this.overlayCloseButton.removeClass('hidden');
                    $(e.target).removeClass('contracting');
                    console.log(response);
                    //update duplicates then save new field values, then mark submitted, then close
                },
                error: (response) => {
                    this.overlayCloseButton.removeClass('hidden');
                    // console.log(response);
                }
            })
        } else {
            this.submissionErrorSection.addClass('hidden');
            if (assignedProduct == ''){
                let p = $('<p />').text('Choose a product to assign your ISBN.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (title == ''){
                let p = $('<p />').text('Enter a title.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (description == ''){
                let p = $('<p />').text('Enter a description.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (name0 == ''){
                let p = $('<p />').text('Enter your name.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bio0 == ''){
                let p = $('<p />').text('Enter your biography.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bookMedium == ''){
                let p = $('<p />').text('Select a medium.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bookFormat == ''){
                let p = $('<p />').text('Select a format.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (pubDate == ''){
                let p = $('<p />').text('Enter a publication date.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (status == ''){
                let p = $('<p />').text('Select a status.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (price == ''){
                let p = $('<p />').text('Enter a price.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
        }
    }
    autofill(e){
        let productId = $(e.target).find(":selected").data('productid');
        if (productId > 0){
            $(e.target).addClass('contracting');
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
                },
                url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/checkAssignedProduct',
                type: 'GET',
                data: {
                    'productId': productId
                },
                success: (response) => {
                    $(e.target).removeClass('contracting');
                    if (response.length > 0){
                        $(e.target).removeClass('contracting');
                        this.assignedProductError.append(',' + response[0]['isbn'] + '.');
                        this.assignedProductError.removeClass('hidden');
                    } else {
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
                                    this.publicationDate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
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
                },
                error: (response) => {
                    console.log(response);
                }
            })
        }
    }
    showInfo(e){
        let isbn = $(e.target).closest('.tomc-isbn-field-section').data('isbn');
        this.isbnid = $(e.target).closest('.tomc-isbn-field-section').data('isbnid');
        this.isbnInfoOverlay.addClass('search-overlay--active');
        this.isbnInfoOverlay.find('h2').append(isbn);
    }
    closeOverlay(e){
        this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
        this.isbnInfoOverlay.removeClass('search-overlay--active');
    }
}

export default ISBNRegistrations;