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
    setTimeout(this.populate.bind(this), 100);
  }
  populate() {
    console.log('populate called!!!');
    var productId = this.product.val();
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
  }
  toggleHiddenFields(e) {
    jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('.tomc-isbn-record').children('.tomc-isbn-hidden-fields').toggleClass('hidden');
    console.log('hidden toggle called');
  }
  markCompleted(e) {
    var recordId = jquery__WEBPACK_IMPORTED_MODULE_0___default()(e.target).parent('.tomc-isbn-record').data('isbn-for');
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
        console.log(response);
        location.reload(true);
      },
      failure: response => {
        console.log(response);
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
        for (let i = 0; i < response.length; i++) {
          this.newDiv = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('tomc-isbn-record').attr('data-isbn-for', response[i]['isbn_for']);
          this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<h2 />').addClass('centered-text tomc-book-options--cursor-pointer blue-text').html('<strong>Title:</strong> ' + response[i]['title']).on('click', this.toggleHiddenFields.bind(this));
          this.newDiv.append(this.field);
          this.hiddenSection = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<div />').addClass('hidden tomc-isbn-hidden-fields');
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
          this.field = jquery__WEBPACK_IMPORTED_MODULE_0___default()('<p />').html('<strong>Publication Status:</strong> ' + response[i]['publication_status']).addClass('tomc-purple-isbn-field');
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
      },
      error: response => {
        console.log(response);
      }
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ISBNRecords);

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


const isbnForm = new _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__["default"]();
const isbnRecords = new _modules_ISBNRecords__WEBPACK_IMPORTED_MODULE_1__["default"]();
})();

/******/ })()
;
//# sourceMappingURL=index.js.map