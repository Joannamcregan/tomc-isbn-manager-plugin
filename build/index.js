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
          // console.log(response);
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
    this.getFiled = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-get-filed-records');
    this.filedSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-filed-records-container');
    this.showInfoButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.see-isbn-info-button');
    this.overlay = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-view-info-overlay');
    this.closeOverlayButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-view-overlay__close');
    this.overlayContainer = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-view--container');
    this.markFiledButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-mark-filed');
    this.markUpdatedButtons = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-updates--mark-processed');
    this.events();
    this.recordid;
  }
  events() {
    this.getFiled.on('click', this.getMoreFiledRecords.bind(this));
    this.showInfoButton.on('click', this.showInfo.bind(this));
    this.markFiledButton.on('click', this.markCompleted.bind(this));
    this.markUpdatedButtons.on('click', this.markUpdateProcessed.bind(this));
  }
  markUpdateProcessed(e) {
    let updateid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('updateid');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/markUpdateProcessed',
      type: 'POST',
      data: {
        'updateid': updateid
      },
      success: response => {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
        location.reload(true);
      },
      failure: response => {
        // console.log(response);
      }
    });
  }
  showInfo(e) {
    let isbn = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('div').data('isbn');
    this.recordid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('div').data('recordid');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFieldValues',
      type: 'GET',
      data: {
        'isbn': isbn
      },
      success: response => {
        this.overlay.find('h2').append(' ' + isbn);
        this.overlay.addClass('search-overlay--active');
        let img = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<img />').attr('src', jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('image')).attr('alt', 'cover for ' + jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('title'));
        this.overlayContainer.append(img);
        for (let i = 0; i < response.length; i++) {
          let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').addClass(i % 2 == 0 ? 'tomc-purple-paragraph' : 'tomc-plain-paragraph');
          let strong = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<strong />').text(response[i]['fieldlabel'] + ': ');
          p.append(strong);
          let span = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<span />').text(response[i]['fieldvalue']);
          p.append(span);
          this.overlayContainer.append(p);
        }
      },
      failure: response => {
        // console.log(response);
      }
    });
  }
  markCompleted(e) {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/markRecordFiled',
      type: 'POST',
      data: {
        'recordId': this.recordid
      },
      success: response => {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
        location.reload(true);
      },
      failure: response => {
        // console.log(response);
      }
    });
  }
  getMoreFiledRecords(e) {
    let shownCount = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('count');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getMoreFiledRecords',
      type: 'GET',
      data: {
        'shownCount': shownCount
      },
      success: response => {
        // console.log(response);
        shownCount += response.length;
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('count', shownCount);
      },
      error: response => {
        // console.log(response);
      }
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ISBNRecords);

/***/ }),

/***/ "./src/modules/MyISBNRegistrations.js":
/*!********************************************!*\
  !*** ./src/modules/MyISBNRegistrations.js ***!
  \********************************************/
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
    this.seeInfoButtons = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.view-isbn-info-button');
    this.unsubmitButtons = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.unsubmit-isbn-button');
    this.updateButtons = jquery__WEBPACK_IMPORTED_MODULE_0___default()('.update-isbn-button');
    this.isbnInfoOverlay = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-edit-info-overlay');
    this.viewOnlyOverlay = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-view-info-overlay');
    this.updateOverlay = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-update-info-overlay');
    this.updateOverlayCloseButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-update-info-overlay__close');
    this.viewOnlyContainer = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#tomc-isbn-view-info-container');
    this.overlayCloseButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info-overlay__close');
    this.viewOnlyCloseButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-view-info-overlay__close');
    this.audioSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--audio-section');
    this.ebookSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--ebook-section');
    this.printSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--print-section');
    this.mediumSelect = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium');
    this.assignedProductDropdown = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--assigned-product');
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
    this.saveButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--save');
    this.submitUpdateButton = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--send-update');
    this.events();
  }
  events() {
    this.addInfoButtons.on('click', this.showInfo.bind(this));
    this.overlayCloseButton.on('click', this.closeOverlay.bind(this));
    this.viewOnlyCloseButton.on('click', this.closeViewOnlyOverlay.bind(this));
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
    this.saveButton.on('click', this.saveClose.bind(this));
    this.unsubmitButtons.on('click', this.unsubmit.bind(this));
    this.seeInfoButtons.on('click', this.showViewOnlyInfo.bind(this));
    this.updateButtons.on('click', this.openUpdateOverlay.bind(this));
    this.submitUpdateButton.on('click', this.updateInfo.bind(this));
  }
  updateInfo(e) {
    let isbnid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('isbnid');
    let updatenote = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--update-note').val();
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/updateISBNInfo',
      type: 'POST',
      data: {
        'isbnid': isbnid,
        'updatenote': updatenote
      },
      success: response => {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
        location.reload(true);
      },
      error: response => {
        // console.log(response);
      }
    });
  }
  openUpdateOverlay(e) {
    let isbn = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('isbn');
    let isbnid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('isbnid');
    this.updateOverlay.addClass('search-overlay--active');
    this.updateOverlay.find('h2').append(isbn);
    this.submitUpdateButton.data('isbnid', isbnid);
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
    let bookFormat = jquery__WEBPACK_IMPORTED_MODULE_0___default()('select.isbn-info--format-select:visible option:selected').text();
    let pubDate = this.publicationDate.val();
    let status = this.statusSelect.val();
    let price = this.priceField.val();
    this.submissionErrorSection.html('');
    if (assignedProduct != '' && title != '' && description != '' && name0 != '' && bio0 != '' && bookMedium != '' && bookFormat != '' && pubDate != '' && pubDate != 'mm/dd/yyyy' && status != '' && price != '' && assignedProduct != null && title != null && description != null && name0 != null && bio0 != null && bookMedium != null && bookFormat != null && pubDate != null && status != null && price != null && price != '$null' && price != 'null') {
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
      if (this.subtitleField.val() != '' && this.subtitleField.val() != null) {
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
        field: "format",
        value: bookFormat
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--first-genre"]').text(),
        value: this.firstGenreDropdown.val()
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--second-genre"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val()
        });
      }
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-0"]').text(),
        value: name0
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-0"]').text(),
        value: bio0
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-0"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-0').val()
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-1"]').text() + ' 1',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-1"]').text() + ' 1',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-1').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-1"]').text() + ' 1',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-1').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-2"]').text() + ' 2',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-2"]').text() + ' 2',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-2').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-2"]').text() + ' 2',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-2').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-3"]').text() + ' 3',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-3"]').text() + ' 3',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-3').val()
        });
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-3"]').text() + ' 3',
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-3').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val() != null) {
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
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--publication-date"]').text(),
        value: pubDate
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--status"]').text(),
        value: status
      });
      fieldVals.push({
        field: 'Target audience',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--target-audience').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--price"]').text(),
        value: price
      });
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--language"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--copyright"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--control"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--translated-title"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-size"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-pages"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val()
        });
      }
      if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val() != null) {
        fieldVals.push({
          field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-illustrations"]').text(),
          value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val()
        });
      }
      jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
        },
        url: '/wp-json/tomcISBN/v1/saveAndSubmitRecord',
        type: 'POST',
        data: {
          'isbnid': this.isbnid,
          'productid': productId,
          'fieldVals': JSON.stringify(fieldVals)
        },
        success: response => {
          this.overlayCloseButton.removeClass('hidden');
          jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
          location.reload(true);
        },
        error: response => {
          this.overlayCloseButton.removeClass('hidden');
        }
      });
    } else {
      this.submissionErrorSection.addClass('hidden');
      if (assignedProduct == '' || assignedProduct == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Choose a product to assign your ISBN.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (title == '' || title == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a title.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (description == '' || description == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a description.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (name0 == '' || name0 == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter your name.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bio0 == '' || bio0 == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter your biography.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bookMedium == '' || bookMedium == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a medium.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (bookFormat == '' || bookFormat == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a format.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (pubDate == '' || pubDate == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a publication date.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (status == '' || status == null) {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Select a status.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
      if (price == '' || price == null || price == '$null' || price == 'null') {
        let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').text('Enter a price.').addClass('red-text centered-text');
        this.submissionErrorSection.append(p);
        this.submissionErrorSection.removeClass('hidden');
      }
    }
  }
  saveClose(e) {
    let fieldVals = [];
    let productId = this.assignedProductDropdown.find(':selected').data('productid');
    let title = this.titleField.val();
    let description = this.descriptionField.val();
    let name0 = this.contributor0.val();
    let bio0 = this.biography0.val();
    let bookMedium = this.mediumSelect.val();
    let bookFormat = jquery__WEBPACK_IMPORTED_MODULE_0___default()('select.isbn-info--format-select:visible option:selected').text();
    let pubDate = this.publicationDate.val();
    let status = this.statusSelect.val();
    let price = this.priceField.val();
    this.submissionErrorSection.html('');
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
    if (this.subtitleField.val() != '' && this.subtitleField.val() != null) {
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
      field: "format",
      value: bookFormat
    });
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--first-genre"]').text(),
      value: this.firstGenreDropdown.val()
    });
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--second-genre"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre').val()
      });
    }
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-0"]').text(),
      value: name0
    });
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-0"]').text(),
      value: bio0
    });
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-0"]').text(),
      value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-0').val()
    });
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-1"]').text() + ' 1',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-1"]').text() + ' 1',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-1').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-1"]').text() + ' 1',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-1').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-2"]').text() + ' 2',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-2"]').text() + ' 2',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-2').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-2"]').text() + ' 2',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-2').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--name-3"]').text() + ' 3',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor--bio-3"]').text() + ' 3',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-3').val()
      });
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-contributor-function-3"]').text() + ' 3',
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-3').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val() != null) {
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
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--publication-date"]').text(),
      value: pubDate
    });
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--status"]').text(),
      value: status
    });
    fieldVals.push({
      field: 'Target audience',
      value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--target-audience').val()
    });
    fieldVals.push({
      field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--price"]').text(),
      value: price
    });
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--language"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--copyright"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--control"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--translated-title"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--book-size"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-pages"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val()
      });
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val() != '' && jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val() != null) {
      fieldVals.push({
        field: jquery__WEBPACK_IMPORTED_MODULE_0___default()('label[for="isbn-info--number-illustrations"]').text(),
        value: jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val()
      });
    }
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: '/wp-json/tomcISBN/v1/saveFieldValues',
      type: 'POST',
      data: {
        'isbnid': this.isbnid,
        'productid': productId,
        'fieldVals': JSON.stringify(fieldVals)
      },
      success: response => {
        this.overlayCloseButton.removeClass('hidden');
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
        location.reload(true);
      },
      error: response => {
        this.overlayCloseButton.removeClass('hidden');
      }
    });
  }
  unsubmit(e) {
    let recordid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).data('record');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).addClass('contracting');
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/unsubmitRecord',
      type: 'POST',
      data: {
        'recordid': recordid
      },
      success: response => {
        jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).removeClass('contracting');
        location.reload(true);
      },
      failure: response => {
        // console.log(response);
      }
    });
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
                  if (response[0]['format'] == 'E-Books') {
                    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium--ebook').attr('selected', 'selected');
                    this.ebookSection.removeClass('hidden');
                    this.audioSection.addClass('hidden');
                    this.printSection.addClass('hidden');
                  } else if (response[0]['format'] == 'Audiobooks') {
                    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium--audio').attr('selected', 'selected');
                    this.ebookSection.addClass('hidden');
                    this.audioSection.removeClass('hidden');
                    this.printSection.addClass('hidden');
                  } else {
                    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium--print').attr('selected', 'selected');
                    this.ebookSection.addClass('hidden');
                    this.audioSection.addClass('hidden');
                    this.printSection.removeClass('hidden');
                  }
                  this.contributor0.val(response[0]['contributor']);
                  this.biography0.val(response[0]['biography']);
                  this.publicationDate.val(response[0]['publicationdate0'] ? response[0]['publicationdate0'] : response[0]['publicationdate1']);
                  if (response[0]['islive'] == "1") {
                    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--status-active').attr('selected', 'selected');
                  } else {
                    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--status-forthcoming').attr('selected', 'selected');
                  }
                  this.priceField.val('$' + response[0]['price']);
                  this.languageField.val(response[0]['language']);
                }
              },
              error: response => {
                // console.log(response);
              }
            });
          }
        },
        error: response => {
          // console.log(response);
        }
      });
    }
  }
  showViewOnlyInfo(e) {
    let isbn = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).closest('.tomc-isbn-field-section').data('isbn');
    this.viewOnlyOverlay.addClass('search-overlay--active');
    this.viewOnlyOverlay.find('h2').append(isbn);
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFieldValues',
      type: 'GET',
      data: {
        'isbn': isbn
      },
      success: response => {
        if (response.length > 0) {
          for (let i = 0; i < response.length; i++) {
            let p = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').addClass(i % 2 == 0 ? 'tomc-purple-paragraph' : 'tomc-plain-paragraph');
            let strong = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<strong />').text(response[i]['fieldlabel'] + ': ');
            p.append(strong);
            let span = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<span />').text(response[i]['fieldvalue']);
            p.append(span);
            this.viewOnlyContainer.append(p);
          }
        }
      },
      error: response => {
        // console.log(response);
      }
    });
  }
  showInfo(e) {
    let isbn = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).closest('.tomc-isbn-field-section').data('isbn');
    this.isbnid = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).closest('.tomc-isbn-field-section').data('isbnid');
    this.isbnInfoOverlay.addClass('search-overlay--active');
    this.isbnInfoOverlay.find('h2').append(isbn);
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/getFieldValues',
      type: 'GET',
      data: {
        'isbn': isbn
      },
      success: response => {
        if (response.length > 0) {
          for (let i = 0; i < response.length; i++) {
            if (response[i]['fieldlabel'] == 'Assigned product') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('option[data-productid="' + response[i]['fieldvalue'] + '"]').attr('selected', 'selected');
            } else if (response[i]['fieldlabel'] == 'Book Title') {
              this.titleField.val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Subtitle (optional)') {
              this.subtitleField.val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Description (up to 350 words)') {
              this.descriptionField.val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Medium') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium option:contains("' + response[i]['fieldvalue'] + '")').attr('selected', 'selected');
            } else if (response[i]['fieldlabel'] == 'Format') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('.isbn-info--format-select').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'First genre') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--first-genre option').filter(function () {
                if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).text() == response[i]['fieldvalue']) {
                  jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('selected', 'selected');
                  return false;
                }
              });
            } else if (response[i]['fieldlabel'] == 'Second genre (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre option').filter(function () {
                if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).text() == response[i]['fieldvalue']) {
                  jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('selected', 'selected');
                  return false;
                }
              });
            } else if (response[i]['fieldlabel'] == 'Your name') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-0').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Your bio') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-0').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Your function') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-0 option').filter(function () {
                if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).text() == response[i]['fieldvalue']) {
                  jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('selected', 'selected');
                  return false;
                }
              });
            } else if (response[i]['fieldlabel'] == 'Contributor name 1') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors--yes-1').attr('checked', true);
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-1').removeClass('hidden');
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor bio 1') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-1').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor function 1') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-1').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor name 2') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors--yes-2').attr('checked', true);
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-2').removeClass('hidden');
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor bio 2') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-2').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor function 2') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-2').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor name 3') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors--yes-3').attr('checked', true);
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-3').removeClass('hidden');
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor bio 3') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-3').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor function 3') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-3').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor name 4') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors--yes-4').attr('checked', true);
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-4').removeClass('hidden');
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor bio 4') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-4').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Contributor function 4') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-4').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Publication date') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--publication-date').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Title Status') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--status option').filter(function () {
                if (jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).text() == response[i]['fieldvalue']) {
                  jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).attr('selected', 'selected');
                  return false;
                }
              });
            } else if (response[i]['fieldlabel'] == 'Target audience') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--target-audience').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Book price') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--price').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Language (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--language').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Copyright year (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Library of Congress control number (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Translated title (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Format details (book size) (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Number of pages (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val(response[i]['fieldvalue']);
            } else if (response[i]['fieldlabel'] == 'Number of illustrations (optional)') {
              jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val(response[i]['fieldvalue']);
            }
          }
        }
      },
      error: response => {
        // console.log(response);
      }
    });
  }
  closeViewOnlyOverlay(e) {
    this.viewOnlyOverlay.find('h2').html('View Info for ISBN ');
    this.viewOnlyOverlay.children('#tomc-isbn-view-info-container').html('');
    this.viewOnlyOverlay.removeClass('search-overlay--active');
  }
  closeOverlay(e) {
    this.isbnInfoOverlay.find('h2').html('Add Info for ISBN ');
    this.assignedProductDropdown.find('option[data-productid="0"]').attr('selected', 'selected');
    this.titleField.val('');
    this.subtitleField.val('');
    this.descriptionField.val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-medium--blank').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--section-E-book').removeClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--section-Audio').addClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--section-Print').addClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--first-genre--fiction-general').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--second-genre--blank').attr('selected', 'selected');
    this.contributor0.val('');
    this.biography0.val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-0--author').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-1').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-1').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-1--author').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-1').addClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-2').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-2').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-2--author').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-2').addClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-3').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-3').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-3--author').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-3').addClass('hidden');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--name-4').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor--bio-4').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributor-function-4--author').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-contributors-section-4').addClass('hidden');
    this.radioNo.map(function () {
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).prop('checked', true);
    });
    this.publicationDate.val('mm/dd/yyyy');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--status-active').attr('selected', 'selected');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--target-audience--trade').attr('selected', 'selected');
    this.priceField.val('');
    this.languageField.val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--copyright').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--control').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--translated-title').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--book-size').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-pages').val('');
    jquery__WEBPACK_IMPORTED_MODULE_0___default()('#isbn-info--number-illustrations').val('');
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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/ISBNInfoForm */ "./src/modules/ISBNInfoForm.js");
/* harmony import */ var _modules_ISBNRecords__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/ISBNRecords */ "./src/modules/ISBNRecords.js");
/* harmony import */ var _modules_MyISBNRegistrations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/MyISBNRegistrations */ "./src/modules/MyISBNRegistrations.js");



const isbnForm = new _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__["default"]();
const isbnRecords = new _modules_ISBNRecords__WEBPACK_IMPORTED_MODULE_1__["default"]();
const isbnRegistrations = new _modules_MyISBNRegistrations__WEBPACK_IMPORTED_MODULE_2__["default"]();
})();

/******/ })()
;
//# sourceMappingURL=index.js.map