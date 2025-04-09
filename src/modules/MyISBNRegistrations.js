import $, { timers } from 'jquery';

class ISBNRegistrations{
    constructor(){
        this.isbnid;
        this.addInfoButtons = $('.add-isbn-info-button');
        this.seeInfoButtons = $('.view-isbn-info-button');
        this.unsubmitButtons = $('.unsubmit-isbn-button');
        this.updateButtons = $('.update-isbn-button');
        this.isbnInfoOverlay = $('#tomc-isbn-edit-info-overlay');
        this.viewOnlyOverlay = $('#tomc-isbn-view-info-overlay');
        this.updateOverlay = $('#tomc-isbn-update-info-overlay');
        this.updateOverlayCloseButton = $('#isbn-update-info-overlay__close');
        this.viewOnlyContainer = $('#tomc-isbn-view-info-container');
        this.overlayCloseButton = $('#isbn-info-overlay__close');
        this.viewOnlyCloseButton = $('#isbn-view-info-overlay__close');
        this.audioSection = $('#isbn-info--section-Audio');
        this.ebookSection = $('#isbn-info--section-E-book');
        this.printSection = $('#isbn-info--section-Print');
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
        this.submissionErrorSection = $('#tomc-info--submission-errors');
        this.submitButton = $('#isbn-info--submit');
        this.saveButton = $('#isbn-info--save');
        this.submitUpdateButton = $('#isbn-info--send-update');
        this.events();
    }
    events(){
        this.addInfoButtons.on('click', this.showInfo.bind(this));
        this.overlayCloseButton.on('click', this.closeOverlay.bind(this));
        this.viewOnlyCloseButton.on('click', this.closeViewOnlyOverlay.bind(this));
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
        this.saveButton.on('click', this.saveClose.bind(this));
        this.unsubmitButtons.on('click', this.unsubmit.bind(this));
        this.seeInfoButtons.on('click', this.showViewOnlyInfo.bind(this));
        this.updateButtons.on('click', this.openUpdateOverlay.bind(this));
        this.submitUpdateButton.on('click', this.updateInfo.bind(this));
    }

    updateInfo(e){
        let isbnid = $(e.target).data('isbnid');
        let updatenote = $('#isbn-info--update-note').val();
        $(e.target).addClass('contracting');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/updateISBNInfo',
            type: 'POST',
            data: {
                'isbnid' : isbnid,
                'updatenote' : updatenote
            },
            success: (response) => {
                $(e.target).removeClass('contracting');
                location.reload(true);
            },
            error: (response) => {
                // console.log(response);
            }
        })
    }

    openUpdateOverlay(e){
        let isbn = $(e.target).data('isbn');
        let isbnid = $(e.target).data('isbnid');
        this.updateOverlay.addClass('search-overlay--active');
        this.updateOverlay.find('h2').append(isbn);
        this.submitUpdateButton.data('isbnid', isbnid);
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
        let bookFormat = $('select.isbn-info--format-select:visible option:selected').text();
        let pubDate = this.publicationDate.val();
        let status = this.statusSelect.val();
        let price = this.priceField.val();

        this.submissionErrorSection.html('');

        if (assignedProduct != '' && title != ''  && description != '' && name0 != '' && bio0 != ''
        && bookMedium != '' && bookFormat != '' && pubDate != '' && pubDate != 'mm/dd/yyyy' 
        && status != '' && price != '' 
        && assignedProduct != null && title != null && description != null && name0 != null && bio0 != null
        && bookMedium != null && bookFormat != null && pubDate != null && status != null && price != null && price != '$null' && price != 'null'){
            $(e.target).addClass('contracting');
            this.overlayCloseButton.addClass('hidden'); //keep people from closing overlay while saving/submitting
            this.submissionErrorSection.addClass('hidden');
            fieldVals.push({ field: $('label[for="isbn-info--assigned-product"]').text(), value: productId});
            fieldVals.push({ field: $('label[for="isbn-info--book-title"]').text(), value: title});
            if (this.subtitleField.val() != '' && this.subtitleField.val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--book-subtitle"]').text(), value: this.subtitleField.val()});
            }
            fieldVals.push({ field: $('label[for="isbn-info--book-description"]').text(), value: description});
            fieldVals.push({ field: $('label[for="isbn-info--book-medium"]').text(), value: bookMedium});
            fieldVals.push({ field: "format", value: bookFormat});
            fieldVals.push({ field: $('label[for="isbn-info--first-genre"]').text(), value: this.firstGenreDropdown.val()});
            if ($('#isbn-info--second-genre').val() != '' && $('#isbn-info--second-genre').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--second-genre"]').text(), value: $('#isbn-info--second-genre').val()});
            }
            fieldVals.push({ field: $('label[for="isbn-contributor--name-0"]').text(), value: name0});
            fieldVals.push({ field: $('label[for="isbn-contributor--bio-0"]').text(), value: bio0});
            fieldVals.push({ field: $('label[for="isbn-contributor-function-0"]').text(), value: $('#isbn-contributor-function-0').val()});
            if ($('#isbn-contributor--name-1').val() != '' && $('#isbn-contributor--name-1').val() != null){
                fieldVals.push({ field: $('label[for="isbn-contributor--name-1"]').text() + ' 1', value: $('#isbn-contributor--name-1').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor--bio-1"]').text() + ' 1', value: $('#isbn-contributor--bio-1').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor-function-1"]').text() + ' 1', value: $('#isbn-contributor-function-1').val()});
            }
            if ($('#isbn-contributor--name-2').val() != '' && $('#isbn-contributor--name-2').val() != null){
                fieldVals.push({ field: $('label[for="isbn-contributor--name-2"]').text() + ' 2', value: $('#isbn-contributor--name-2').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor--bio-2"]').text() + ' 2', value: $('#isbn-contributor--bio-2').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor-function-2"]').text() + ' 2', value: $('#isbn-contributor-function-2').val()});
            }
            if ($('#isbn-contributor--name-3').val() != '' && $('#isbn-contributor--name-3').val() != null){
                fieldVals.push({ field: $('label[for="isbn-contributor--name-3"]').text() + ' 3', value: $('#isbn-contributor--name-3').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor--bio-3"]').text() + ' 3', value: $('#isbn-contributor--bio-3').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor-function-3"]').text() + ' 3', value: $('#isbn-contributor-function-3').val()});
            }
            if ($('#isbn-contributor--name-4').val() != '' && $('#isbn-contributor--name-4').val() != null){
                fieldVals.push({ field: $('label[for="isbn-contributor--name-4"]').text(), value: $('#isbn-contributor--name-4').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor--bio-4"]').text(), value: $('#isbn-contributor--bio-4').val()});
                fieldVals.push({ field: $('label[for="isbn-contributor-function-4"]').text(), value: $('#isbn-contributor-function-4').val()});
            }
            fieldVals.push({ field: $('label[for="isbn-info--publication-date"]').text(), value: pubDate});
            fieldVals.push({ field: $('label[for="isbn-info--status"]').text(), value: status});
            fieldVals.push({ field: 'Target audience', value: $('#isbn-info--target-audience').val()})
            fieldVals.push({ field: $('label[for="isbn-info--price"]').text(), value: price});
            if ($('#isbn-info--language').val() != '' && $('#isbn-info--language').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--language"]').text(), value: $('#isbn-info--language').val()});
            }
            if ($('#isbn-info--copyright').val() != '' && $('#isbn-info--copyright').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--copyright"]').text(), value: $('#isbn-info--copyright').val()});
            }
            if ($('#isbn-info--control').val() != '' && $('#isbn-info--control').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--control"]').text(), value: $('#isbn-info--control').val()});
            }
            if ($('#isbn-info--translated-title').val() != '' && $('#isbn-info--translated-title').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--translated-title"]').text(), value: $('#isbn-info--translated-title').val()});
            }
            if ($('#isbn-info--book-size').val() != '' && $('#isbn-info--book-size').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--book-size"]').text(), value: $('#isbn-info--book-size').val()});
            }
            if ($('#isbn-info--number-pages').val() != '' && $('#isbn-info--number-pages').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--number-pages"]').text(), value: $('#isbn-info--number-pages').val()});
            }
            if ($('#isbn-info--number-illustrations').val() != '' && $('#isbn-info--number-illustrations').val() != null){
                fieldVals.push({ field: $('label[for="isbn-info--number-illustrations"]').text(), value: $('#isbn-info--number-illustrations').val()});
            }

            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
                },
                url: '/wp-json/tomcISBN/v1/saveAndSubmitRecord',
                type: 'POST',
                data: {
                    'isbnid': this.isbnid,
                    'productid': productId,
                    'fieldVals': JSON.stringify(fieldVals)
                },
                success: (response) => {
                    this.overlayCloseButton.removeClass('hidden');
                    $(e.target).removeClass('contracting');
                    location.reload(true);
                },
                error: (response) => {
                    this.overlayCloseButton.removeClass('hidden');
                }
            })
        } else {
            this.submissionErrorSection.addClass('hidden');
            if (assignedProduct == '' || assignedProduct == null){
                let p = $('<p />').text('Choose a product to assign your ISBN.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (title == '' || title == null){
                let p = $('<p />').text('Enter a title.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (description == '' || description == null){
                let p = $('<p />').text('Enter a description.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (name0 == '' || name0 == null){
                let p = $('<p />').text('Enter your name.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bio0 == '' || bio0 == null){
                let p = $('<p />').text('Enter your biography.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bookMedium == '' || bookMedium == null){
                let p = $('<p />').text('Select a medium.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (bookFormat == '' || bookFormat == null){
                let p = $('<p />').text('Select a format.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (pubDate == '' || pubDate == null){
                let p = $('<p />').text('Enter a publication date.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (status == '' || status == null){
                let p = $('<p />').text('Select a status.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
            if (price == '' || price == null || price == '$null' || price == 'null'){
                let p = $('<p />').text('Enter a price.').addClass('red-text centered-text');
                this.submissionErrorSection.append(p);
                this.submissionErrorSection.removeClass('hidden');
            }
        }
    }

    saveClose(e){
        let fieldVals = [];

        let productId = this.assignedProductDropdown.find(':selected').data('productid');
        let title = this.titleField.val();
        let description = this.descriptionField.val();
        let name0 = this.contributor0.val();
        let bio0 = this.biography0.val();
        let bookMedium = this.mediumSelect.val();
        let bookFormat = $('select.isbn-info--format-select:visible option:selected').text();
        let pubDate = this.publicationDate.val();
        let status = this.statusSelect.val();
        let price = this.priceField.val();

        this.submissionErrorSection.html('');

        
        $(e.target).addClass('contracting');
        this.overlayCloseButton.addClass('hidden'); //keep people from closing overlay while saving/submitting
        this.submissionErrorSection.addClass('hidden');
        fieldVals.push({ field: $('label[for="isbn-info--assigned-product"]').text(), value: productId});
        fieldVals.push({ field: $('label[for="isbn-info--book-title"]').text(), value: title});
        if (this.subtitleField.val() != '' && this.subtitleField.val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--book-subtitle"]').text(), value: this.subtitleField.val()});
        }
        fieldVals.push({ field: $('label[for="isbn-info--book-description"]').text(), value: description});
        fieldVals.push({ field: $('label[for="isbn-info--book-medium"]').text(), value: bookMedium});
        fieldVals.push({ field: "format", value: bookFormat});
        fieldVals.push({ field: $('label[for="isbn-info--first-genre"]').text(), value: this.firstGenreDropdown.val()});
        if ($('#isbn-info--second-genre').val() != '' && $('#isbn-info--second-genre').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--second-genre"]').text(), value: $('#isbn-info--second-genre').val()});
        }
        fieldVals.push({ field: $('label[for="isbn-contributor--name-0"]').text(), value: name0});
        fieldVals.push({ field: $('label[for="isbn-contributor--bio-0"]').text(), value: bio0});
        fieldVals.push({ field: $('label[for="isbn-contributor-function-0"]').text(), value: $('#isbn-contributor-function-0').val()});
        if ($('#isbn-contributor--name-1').val() != '' && $('#isbn-contributor--name-1').val() != null){
            fieldVals.push({ field: $('label[for="isbn-contributor--name-1"]').text() + ' 1', value: $('#isbn-contributor--name-1').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor--bio-1"]').text() + ' 1', value: $('#isbn-contributor--bio-1').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor-function-1"]').text() + ' 1', value: $('#isbn-contributor-function-1').val()});
        }
        if ($('#isbn-contributor--name-2').val() != '' && $('#isbn-contributor--name-2').val() != null){
            fieldVals.push({ field: $('label[for="isbn-contributor--name-2"]').text() + ' 2', value: $('#isbn-contributor--name-2').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor--bio-2"]').text() + ' 2', value: $('#isbn-contributor--bio-2').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor-function-2"]').text() + ' 2', value: $('#isbn-contributor-function-2').val()});
        }
        if ($('#isbn-contributor--name-3').val() != '' && $('#isbn-contributor--name-3').val() != null){
            fieldVals.push({ field: $('label[for="isbn-contributor--name-3"]').text() + ' 3', value: $('#isbn-contributor--name-3').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor--bio-3"]').text() + ' 3', value: $('#isbn-contributor--bio-3').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor-function-3"]').text() + ' 3', value: $('#isbn-contributor-function-3').val()});
        }
        if ($('#isbn-contributor--name-4').val() != '' && $('#isbn-contributor--name-4').val() != null){
            fieldVals.push({ field: $('label[for="isbn-contributor--name-4"]').text(), value: $('#isbn-contributor--name-4').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor--bio-4"]').text(), value: $('#isbn-contributor--bio-4').val()});
            fieldVals.push({ field: $('label[for="isbn-contributor-function-4"]').text(), value: $('#isbn-contributor-function-4').val()});
        }
        fieldVals.push({ field: $('label[for="isbn-info--publication-date"]').text(), value: pubDate});
        fieldVals.push({ field: $('label[for="isbn-info--status"]').text(), value: status});
        fieldVals.push({ field: 'Target audience', value: $('#isbn-info--target-audience').val()})
        fieldVals.push({ field: $('label[for="isbn-info--price"]').text(), value: price});
        if ($('#isbn-info--language').val() != '' && $('#isbn-info--language').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--language"]').text(), value: $('#isbn-info--language').val()});
        }
        if ($('#isbn-info--copyright').val() != '' && $('#isbn-info--copyright').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--copyright"]').text(), value: $('#isbn-info--copyright').val()});
        }
        if ($('#isbn-info--control').val() != '' && $('#isbn-info--control').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--control"]').text(), value: $('#isbn-info--control').val()});
        }
        if ($('#isbn-info--translated-title').val() != '' && $('#isbn-info--translated-title').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--translated-title"]').text(), value: $('#isbn-info--translated-title').val()});
        }
        if ($('#isbn-info--book-size').val() != '' && $('#isbn-info--book-size').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--book-size"]').text(), value: $('#isbn-info--book-size').val()});
        }
        if ($('#isbn-info--number-pages').val() != '' && $('#isbn-info--number-pages').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--number-pages"]').text(), value: $('#isbn-info--number-pages').val()});
        }
        if ($('#isbn-info--number-illustrations').val() != '' && $('#isbn-info--number-illustrations').val() != null){
            fieldVals.push({ field: $('label[for="isbn-info--number-illustrations"]').text(), value: $('#isbn-info--number-illustrations').val()});
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: '/wp-json/tomcISBN/v1/saveFieldValues',
            type: 'POST',
            data: {
                'isbnid': this.isbnid,
                'productid': productId,
                'fieldVals': JSON.stringify(fieldVals)
            },
            success: (response) => {
                this.overlayCloseButton.removeClass('hidden');
                $(e.target).removeClass('contracting');
                location.reload(true);
            },
            error: (response) => {
                this.overlayCloseButton.removeClass('hidden');
            }
        })
    }

    unsubmit(e){
        let recordid = $(e.target).data('record');
        $(e.target).addClass('contracting');
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
            },
            url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/unsubmitRecord',
            type: 'POST',
            data: {
                'recordid' : recordid
            },
            success: (response) => {
                $(e.target).removeClass('contracting');
                location.reload(true);
            },
            failure: (response) => {
                // console.log(response);
            }
        })
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
                                    if(response[0]['format'] == 'E-Books'){
                                        console.log('ebooks');
                                        $('#isbn-info--book-medium--ebook').attr('selected', 'selected');
                                        this.ebookSection.removeClass('hidden');
                                        this.audioSection.addClass('hidden');
                                        this.printSection.addClass('hidden');
                                    } else if (response[0]['format'] == 'Audiobooks'){
                                        console.log('audiobook');
                                        $('#isbn-info--book-medium--audio').attr('selected', 'selected');
                                        this.ebookSection.addClass('hidden');
                                        this.audioSection.removeClass('hidden');
                                        this.printSection.addClass('hidden');
                                    } else {
                                        console.log('print');
                                        $('#isbn-info--book-medium--print').attr('selected', 'selected');
                                        this.ebookSection.addClass('hidden');
                                        this.audioSection.addClass('hidden');
                                        this.printSection.removeClass('hidden');
                                    }
                                    this.contributor0.val(response[0]['contributor']);
                                    this.biography0.val(response[0]['biography']);
                                    this.publicationDate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
                                    if (response[0]['islive'] == "1"){
                                        $('#isbn-info--status-active').attr('selected', 'selected');
                                    } else {
                                        $('#isbn-info--status-forthcoming').attr('selected', 'selected');
                                    }
                                    this.priceField.val('$' + response[0]['price']);
                                    this.languageField.val(response[0]['language']);
                                }
                            },
                            error: (response) => {
                                // console.log(response);
                            }
                        })
                    }
                },
                error: (response) => {
                    // console.log(response);
                }
            })
        }
    }

    showViewOnlyInfo(e){
        let isbn = $(e.target).closest('.tomc-isbn-field-section').data('isbn');
        this.viewOnlyOverlay.addClass('search-overlay--active');
        this.viewOnlyOverlay.find('h2').append(isbn);
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
                if (response.length > 0){
                    for (let i = 0; i < response.length; i++){
                        let p = $('<p />').addClass(i % 2 == 0 ? 'tomc-purple-paragraph' : 'tomc-plain-paragraph');
                        let strong = $('<strong />').text(response[i]['fieldlabel'] + ': ');
                        p.append(strong);
                        let span = $('<span />').text(response[i]['fieldvalue']);
                        p.append(span);
                        this.viewOnlyContainer.append(p);
                    }
                }
            },
            error: (response) => {
                // console.log(response);
            }
        })
    }

    showInfo(e){
        let isbn = $(e.target).closest('.tomc-isbn-field-section').data('isbn');
        this.isbnid = $(e.target).closest('.tomc-isbn-field-section').data('isbnid');
        this.isbnInfoOverlay.addClass('search-overlay--active');
        this.isbnInfoOverlay.find('h2').append(isbn);
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
                if (response.length > 0){
                    for (let i = 0; i < response.length; i++){
                        if (response[i]['fieldlabel'] == 'Assigned product'){
                            $('option[data-productid="'+ response[i]['fieldvalue'] +'"]').attr('selected', 'selected');
                        } else if (response[i]['fieldlabel'] == 'Book Title'){
                            this.titleField.val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Subtitle (optional)'){
                            this.subtitleField.val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Description (up to 350 words)'){
                            this.descriptionField.val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Medium'){
                            $('#isbn-info--book-medium option:contains("'+ response[i]['fieldvalue'] +'")').attr('selected', 'selected');
                        } else if (response[i]['fieldlabel'] == 'Format'){
                            $('.isbn-info--format-select').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'First genre'){
                            $('#isbn-info--first-genre option').filter(function(){
                                if ($(this).text() == response[i]['fieldvalue']){
                                    $(this).attr('selected', 'selected');
                                    return false;
                                }
                            })
                        } else if (response[i]['fieldlabel'] == 'Second genre (optional)'){
                            $('#isbn-info--second-genre option').filter(function(){
                                if ($(this).text() == response[i]['fieldvalue']){
                                    $(this).attr('selected', 'selected');
                                    return false;
                                }
                            })
                        } else if (response[i]['fieldlabel'] == 'Your name'){
                            $('#isbn-contributor--name-0').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Your bio'){
                            $('#isbn-contributor--bio-0').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Your function'){
                            $('#isbn-contributor-function-0 option').filter(function(){
                                if ($(this).text() == response[i]['fieldvalue']){
                                    $(this).attr('selected', 'selected');
                                    return false;
                                }
                            })
                        } else if (response[i]['fieldlabel'] == 'Contributor name 1'){
                            $('#isbn-contributors--yes-1').attr('checked', true);
                            $('#isbn-contributors-section-1').removeClass('hidden');
                            $('#isbn-contributor--name-1').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor bio 1'){
                            $('#isbn-contributor--bio-1').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor function 1'){
                            $('#isbn-contributor-function-1').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor name 2'){
                            $('#isbn-contributors--yes-2').attr('checked', true);
                            $('#isbn-contributors-section-2').removeClass('hidden');
                            $('#isbn-contributor--name-2').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor bio 2'){
                            $('#isbn-contributor--bio-2').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor function 2'){
                            $('#isbn-contributor-function-2').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor name 3'){
                            $('#isbn-contributors--yes-3').attr('checked', true);
                            $('#isbn-contributors-section-3').removeClass('hidden');
                            $('#isbn-contributor--name-3').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor bio 3'){
                            $('#isbn-contributor--bio-3').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor function 3'){
                            $('#isbn-contributor-function-3').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor name 4'){
                            $('#isbn-contributors--yes-4').attr('checked', true);
                            $('#isbn-contributors-section-4').removeClass('hidden');
                            $('#isbn-contributor--name-4').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor bio 4'){
                            $('#isbn-contributor--bio-4').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Contributor function 4'){
                            $('#isbn-contributor-function-4').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Publication date'){
                            $('#isbn-info--publication-date').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Title Status'){
                            $('#isbn-info--status option').filter(function(){
                                if ($(this).text() == response[i]['fieldvalue']){
                                    $(this).attr('selected', 'selected');
                                    return false;
                                }
                            })
                        } else if (response[i]['fieldlabel'] == 'Target audience'){
                            $('#isbn-info--target-audience').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Book price'){
                            $('#isbn-info--price').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Language (optional)'){
                            $('#isbn-info--language').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Copyright year (optional)'){
                            $('#isbn-info--copyright').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Library of Congress control number (optional)'){
                            $('#isbn-info--control').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Translated title (optional)'){
                            $('#isbn-info--translated-title').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Format details (book size) (optional)'){
                            $('#isbn-info--book-size').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Number of pages (optional)'){
                            $('#isbn-info--number-pages').val(response[i]['fieldvalue']);
                        } else if (response[i]['fieldlabel'] == 'Number of illustrations (optional)'){
                            $('#isbn-info--number-illustrations').val(response[i]['fieldvalue']);
                        }
                    }
                }
            },
            error: (response) => {
                // console.log(response);
            }
        })
    }

    closeViewOnlyOverlay(e){
        this.viewOnlyOverlay.find('h2').html('View Info for ISBN ');
        this.viewOnlyOverlay.children('#tomc-isbn-view-info-container').html('');
        this.viewOnlyOverlay.removeClass('search-overlay--active');
    }

    closeOverlay(e){
        this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
        this.assignedProductDropdown.find('option[data-productid="0"]').attr('selected', 'selected');
        this.titleField.val('');
        this.subtitleField.val('');
        this.descriptionField.val('');
        $('#isbn-info--book-medium--blank').attr('selected', 'selected');
        $('#isbn-info--section-E-book').removeClass('hidden');
        $('#isbn-info--section-Audio').addClass('hidden');
        $('#isbn-info--section-Print').addClass('hidden');
        $('#isbn-info--first-genre--fiction-general').attr('selected', 'selected');
        $('#isbn-info--second-genre--blank').attr('selected', 'selected');
        this.contributor0.val('');
        this.biography0.val('');
        $('#isbn-contributor-function-0--author').attr('selected', 'selected');
        $('#isbn-contributor--name-1').val('');
        $('#isbn-contributor--bio-1').val('');
        $('#isbn-contributor-function-1--author').attr('selected', 'selected');
        $('#isbn-contributors-section-1').addClass('hidden');
        $('#isbn-contributor--name-2').val('');
        $('#isbn-contributor--bio-2').val('');
        $('#isbn-contributor-function-2--author').attr('selected', 'selected');
        $('#isbn-contributors-section-2').addClass('hidden');
        $('#isbn-contributor--name-3').val('');
        $('#isbn-contributor--bio-3').val('');
        $('#isbn-contributor-function-3--author').attr('selected', 'selected');
        $('#isbn-contributors-section-3').addClass('hidden');
        $('#isbn-contributor--name-4').val('');
        $('#isbn-contributor--bio-4').val('');
        $('#isbn-contributor-function-4--author').attr('selected', 'selected');
        $('#isbn-contributors-section-4').addClass('hidden');
        this.radioNo.map(function(){
            $(this).prop('checked', true);
        });
        this.publicationDate.val('mm/dd/yyyy');
        $('#isbn-info--status-active').attr('selected', 'selected');
        $('#isbn-info--target-audience--trade').attr('selected', 'selected');
        this.priceField.val('');
        this.languageField.val('');
        $('#isbn-info--copyright').val('');
        $('#isbn-info--control').val('');
        $('#isbn-info--translated-title').val('');
        $('#isbn-info--book-size').val('');
        $('#isbn-info--number-pages').val('');
        $('#isbn-info--number-illustrations').val('');
        this.isbnInfoOverlay.removeClass('search-overlay--active');
    }
}

export default ISBNRegistrations;