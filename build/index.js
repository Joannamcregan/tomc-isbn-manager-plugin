/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/modules/ISBNInfoForm.js":
/*!*************************************!*\
  !*** ./src/modules/ISBNInfoForm.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

class ISBNForm {
  constructor() {
    this.formSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomcIsbnInfoFieldsDiv');
    this.product = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_product');
    this.title = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_title');
    this.subtitle = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_subtitle');
    this.description = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_description');
    this.format = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_format');
    this.contributor1 = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_contributor1');
    this.biography1 = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_biography1');
    this.publicationdate = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_publication_date');
    this.status = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_status');
    this.price = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_book_price');
    this.language = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc_isbn_book_language');
    this.events();
  }
  events() {
    this.product.on('change', this.populate.bind(this));
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(window).on("load", this.initialPopulate.bind(this));
  }
  initialPopulate() {
    if (window.location.href.match('cart') != null) {
      // console.log('match condition met');
      setTimeout(this.populate.bind(this), 100);
    }
    // else {
    //     console.log('match condition NOT met');
    // }
  }
  populate() {
    var productId = this.product.val();
    if (productId > 0) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
        },
        url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/populate',
        type: 'GET',
        data: {
          'productId': productId
        },
        success: response => {
          console.log(response);
          if (response.length > 0) {
            this.title.val(response[0]['title']);
            this.subtitle.val(response[0]['subtitle']);
            this.description.val(response[0]['description']);
            this.format.val(response[0]['format']);
            this.contributor1.val(response[0]['contributor']);
            this.biography1.val(response[0]['biography']);
            this.publicationdate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
            this.status.val(response[0]['islive'] === 1 ? 'status_active' : 'status_forthcoming');
            this.price.val('$' + response[0]['price']);
            this.language.val(response[0]['language']);
          }
        },
        error: response => {
          console.log(response);
        }
      });
    }
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ISBNForm);

/***/ }),

/***/ "./src/modules/ISBNRecords.js":
/*!************************************!*\
  !*** ./src/modules/ISBNRecords.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

class ISBNRecords {
  constructor() {
    this.getUnfiled = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-get-unfiled-records');
    this.getFiled = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-get-filed-records');
    this.unfiledSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-unfiled-records-container');
    this.filedSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-filed-records-container');
    this.events();
  }
  events() {
    this.getUnfiled.on('click', this.getUnfiledRecords.bind(this));
    this.getFiled.on('click', this.getFiledRecords.bind(this));
  }
  toggleHiddenFields(e) {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('.tomc-isbn-record').children('.tomc-isbn-hidden-fields').toggleClass('hidden');
    // console.log('hidden toggle called');
  }
  markCompleted(e) {
    var recordId = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('.tomc-isbn-hidden-fields').parent('.tomc-isbn-record').data('isbn-product-id');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/markRecordFiled',
      type: 'POST',
      data: {
        'recordId': recordId
      },
      success: response => {
        // console.log(response);
        if (response > 0) {
          location.reload(true);
        }
      },
      failure: response => {
        // console.log(response);
      }
    });
  }
  getUnfiledRecords() {
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getUnfiledRecords',
      type: 'GET',
      success: response => {
        // console.log(response);
        if (response.length) {
          for (let i = 0; i < response.length; i++) {
            this.newDiv = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('tomc-isbn-record').attr('data-isbn-product-id', response[i]['isbn_product_id']);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<h2 />').addClass('centered-text tomc-book-options--cursor-pointer blue-text').html('<strong>Title:</strong> ' + response[i]['title']).on('click', this.toggleHiddenFields.bind(this));
            this.newDiv.append(this.field);
            this.hiddenSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('hidden tomc-isbn-hidden-fields');
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>ISBN:</strong> ' + response[i]['isbn']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Subtitle:</strong> ' + response[i]['subtitle']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Description:</strong> ' + response[i]['description']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Format:</strong> ' + response[i]['format']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>First Genre:</strong> ' + response[i]['first_genre']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Second Genre:</strong> ' + response[i]['second_genre']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Author Name:</strong> ' + response[i]['contributor1']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Author Biography:</strong> ' + response[i]['biography1']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor2']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function2']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography2']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor3']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function3']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography3']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Publication Date:</strong> ' + response[i]['publication_date']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Publication Status:</strong> ' + response[i]['status']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Target Audience:</strong> ' + response[i]['target_audience']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Current Price:</strong> ' + response[i]['book_price']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Language:</strong> ' + response[i]['book_language']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Copyright Year:</strong> ' + response[i]['copyright_year']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Library of Congress Control Number:</strong> ' + response[i]['control_number']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Translated Title:</strong> ' + response[i]['translated_title']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Number of Pages:</strong> ' + response[i]['number_of_pages']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Number of Illustrations:</strong> ' + response[i]['number_of_illustrations']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<span />').html('mark as submitted').addClass('tomc-isbn-submit').on('click', this.markCompleted.bind(this));
            this.hiddenSection.append(this.field);
            this.newDiv.append(this.hiddenSection);
            this.unfiledSection.append(this.newDiv);
          }
          this.getUnfiled.addClass('hidden');
          this.getUnfiled.removeClass('block');
        } else {
          this.newDiv = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('tomc-isbn-record');
          this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').addClass('centered-text tomc-book-options--cursor-pointer').html('No records found');
          this.newDiv.append(this.field);
          this.unfiledSection.append(this.newDiv);
        }
        this.getUnfiled.addClass('hidden');
        this.getUnfiled.removeClass('block');
      },
      error: response => {
        console.log(response);
      }
    });
  }
  getFiledRecords() {
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFiledRecords',
      type: 'GET',
      success: response => {
        console.log(response);
        if (response.length) {
          for (let i = 0; i < response.length; i++) {
            this.newDiv = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('tomc-isbn-record').attr('data-isbn-product-id', response[i]['isbn_product_id']);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<h2 />').addClass('centered-text tomc-book-options--cursor-pointer blue-text').html('<strong>Title:</strong> ' + response[i]['title']).on('click', this.toggleHiddenFields.bind(this));
            this.newDiv.append(this.field);
            this.hiddenSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('hidden tomc-isbn-hidden-fields');
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>ISBN:</strong> ' + response[i]['isbn']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Subtitle:</strong> ' + response[i]['subtitle']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Description:</strong> ' + response[i]['description']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Format:</strong> ' + response[i]['format']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>First Genre:</strong> ' + response[i]['first_genre']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Second Genre:</strong> ' + response[i]['second_genre']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Author Name:</strong> ' + response[i]['contributor1']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Author Biography:</strong> ' + response[i]['biography1']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor2']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function2']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography2']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Name:</strong> ' + response[i]['contributor3']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Function:</strong> ' + response[i]['function3']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Contributor Biography:</strong> ' + response[i]['biography3']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Publication Date:</strong> ' + response[i]['publication_date']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Publication Status:</strong> ' + response[i]['status']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Target Audience:</strong> ' + response[i]['target_audience']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Current Price:</strong> ' + response[i]['book_price']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Language:</strong> ' + response[i]['book_language']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Copyright Year:</strong> ' + response[i]['copyright_year']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Library of Congress Control Number:</strong> ' + response[i]['control_number']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Translated Title:</strong> ' + response[i]['translated_title']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Number of Pages:</strong> ' + response[i]['number_of_pages']).addClass('tomc-plain-isbn-field');
            this.hiddenSection.append(this.field);
            this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Number of Illustrations:</strong> ' + response[i]['number_of_illustrations']).addClass('tomc-purple-isbn-field');
            this.hiddenSection.append(this.field);
            this.newDiv.append(this.hiddenSection);
            this.filedSection.append(this.newDiv);
          }
        } else {
          this.newDiv = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('tomc-isbn-record');
          this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').addClass('centered-text tomc-book-options--cursor-pointer').html('No records found');
          this.newDiv.append(this.field);
          this.filedSection.append(this.newDiv);
        }
        this.getFiled.addClass('hidden');
        this.getFiled.removeClass('block');
      },
      error: response => {
        console.log(response);
      }
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ISBNRecords);

/***/ }),

/***/ "./src/modules/my-isbn-registations.js":
/*!*********************************************!*\
  !*** ./src/modules/my-isbn-registations.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

class ISBNRegistrations {
  constructor() {
    this.isbnid;
    this.addInfoButtons = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.add-isbn-info-button');
    this.isbnInfoOverlay = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-edit-info-overlay');
    this.overlayCloseButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info-overlay__close');
    this.audioSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--audio-section');
    this.ebookSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--ebook-section');
    this.printSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--print-section');
    this.mediumSelect = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium');
    this.assignedProductDropdown = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--assigned-product');
    this.assignedProductError = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--assigned-book-error');
    this.titleField = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-title');
    this.subtitleField = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-subtitle');
    this.descriptionField = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-description');
    this.firstGenreDropdown = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--first-genre');
    this.contributorsSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-contributors-section');
    this.radioYes = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-radio-yes');
    this.radioNo = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-radio-no');
    this.contributor0 = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-0');
    this.biography0 = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-0');
    this.publicationDate = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--publication-date');
    this.statusSelect = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--status');
    this.priceField = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--price');
    this.languageField = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language');
    this.submissionErrorSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-info--submission-errors');
    this.submitButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--submit');
    this.events();
  }
  events() {
    this.addInfoButtons.on('click', this.showInfo.bind(this));
    this.overlayCloseButton.on('click', this.closeOverlay.bind(this));
    this.mediumSelect.on('change', e => {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-info--format-section').addClass('hidden');
      jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--section-' + jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).val()).removeClass('hidden');
    });
    this.radioYes.on('change', e => {
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).prop("checked", true)) {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-' + jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('section')).removeClass('hidden');
      }
    });
    this.radioNo.on('change', e => {
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).prop("checked", true)) {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-' + jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('section')).addClass('hidden');
      }
    });
    this.assignedProductDropdown.on('change', this.autofill.bind(this));
    this.submitButton.on('click', this.submit.bind(this));
  }
  submit(e) {
    let fieldVals = [];
    let productId = this.assignedProductDropdown.find(':selected').data('productid');
    let assignedProduct = this.assignedProductDropdown.val();
    let title = this.titleField.val();
    let description = this.descriptionField.val();
    let name0 = this.contributor0.val();
    let bio0 = this.biography0.val();
    let bookMedium = this.mediumSelect.val();
    let bookFormat = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-info--format-select').val();
    let pubDate = this.publicationDate.val();
    let status = this.statusSelect.val();
    let price = this.priceField.val();
    if (assignedProduct != '' && title != '' && description != '' && name0 != '' && bio0 != '' && bookMedium != '' && bookFormat != '' && pubDate != '' && pubDate != 'mm/dd/yyyy' && status != '' && price != '' && this.assignedProductError.hasClass('hidden')) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
      this.overlayCloseButton.addClass('hidden'); //keep people from closing overlay while saving/submitting
      this.submissionErrorSection.addClass('hidden');
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--assigned-product"]').text(),
        value: productId
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-title"]').text(),
        value: title
      });
      if (this.subtitleField.val() != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-subtitle"]').text(),
          value: this.subtitleField.val()
        });
      }
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-description"]').text(),
        value: description
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-medium"]').text(),
        value: bookMedium
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-info--format-label').text(),
        value: bookFormat
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--first-genre"]').text(),
        value: this.firstGenreDropdown.val()
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val() != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--second-genre"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val()
        });
      }
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-0"]').text(),
        value: this.name0.val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-0"]').text(),
        value: this.contributor0.val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-0"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-0').val()
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-1"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-1"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-1').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-1"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-1').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-2"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-2"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-2').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-2"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-2').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-3"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-3"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-3').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-3"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-3').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-4"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-4"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-4').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-4"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-4').val()
        });
      }
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--price"]').text(),
        value: price
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--language"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--copyright"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--control"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--translated-title"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-size"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-pages"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustations') != '') {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-illustations"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustations').val()
        });
      }
      jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
        },
        url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/saveFieldValues',
        type: 'GET',
        data: {
          'isbnid': this.isbnid,
          'fieldVals': JSON.stringify(fieldVals)
        },
        success: response => {
          this.overlayCloseButton.removeClass('hidden');
          jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
          console.log(response);
          //update duplicates then save new field values, then mark submitted, then close
        },
        error: response => {
          this.overlayCloseButton.removeClass('hidden');
          // console.log(response);
        }
      });
    } else {
      this.submissionErrorSection.addClass('hidden');
      if (assignedProduct == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Choose a product to assign your ISBN.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (title == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a title.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (description == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a description.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (name0 == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter your name.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bio0 == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter your biography.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bookMedium == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a medium.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bookFormat == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a format.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (pubDate == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a publication date.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (status == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a status.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (price == '') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a price.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
    }
  }
  autofill(e) {
    let productId = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).find(":selected").data('productid');
    if (productId > 0) {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
      jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
        },
        url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/checkAssignedProduct',
        type: 'GET',
        data: {
          'productId': productId
        },
        success: response => {
          jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
          if (response.length > 0) {
            jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
            this.assignedProductError.append(',' + response[0]['isbn'] + '.');
            this.assignedProductError.removeClass('hidden');
          } else {
            jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
              beforeSend: xhr => {
                xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
              },
              url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/populateByProduct',
              type: 'GET',
              data: {
                'productId': productId
              },
              success: response => {
                jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
                if (response.length > 0) {
                  this.titleField.val(response[0]['title']);
                  this.subtitleField.val(response[0]['subtitle']);
                  this.descriptionField.val(response[0]['description']);
                  jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-info--format-select').val(response[0]['format']);
                  this.contributor0.val(response[0]['contributor']);
                  this.biography0.val(response[0]['biography']);
                  this.publicationDate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
                  this.statusSelect.val(response[0]['islive'] === 1 ? 'status_active' : 'status_forthcoming');
                  this.priceField.val('$' + response[0]['price']);
                  this.languageField.val(response[0]['language']);
                }
              },
              error: response => {
                console.log(response);
              }
            });
          }
        },
        error: response => {
          console.log(response);
        }
      });
    }
  }
  showInfo(e) {
    let isbn = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).closest('.tomc-isbn-field-section').data('isbn');
    this.isbnid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).closest('.tomc-isbn-field-section').data('isbnid');
    this.isbnInfoOverlay.addClass('search-overlay--active');
    this.isbnInfoOverlay.find('h2').append(isbn);
  }
  closeOverlay(e) {
    this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
    this.isbnInfoOverlay.removeClass('search-overlay--active');
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ISBNRegistrations);

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

module.exports = window["jQuery"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/ISBNInfoForm */ "./src/modules/ISBNInfoForm.js");
/* harmony import */ var _modules_ISBNRecords__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/ISBNRecords */ "./src/modules/ISBNRecords.js");
/* harmony import */ var _modules_my_isbn_registations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/my-isbn-registations */ "./src/modules/my-isbn-registations.js");



const isbnForm = new _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__["default"]();
const isbnRecords = new _modules_ISBNRecords__WEBPACK_IMPORTED_MODULE_1__["default"]();
const isbnRegistrations = new _modules_my_isbn_registations__WEBPACK_IMPORTED_MODULE_2__["default"]();
})();

/******/ })()
;
//# sourceMappingURL=index.js.map