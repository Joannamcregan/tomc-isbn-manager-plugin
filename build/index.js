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
    this.formSection.on('load', setTimeout(this.populate.bind(this), 300));
    this.product.on('change', this.populate.bind(this));
  }
  populate() {
    var productId = this.product.val();
    jquery__WEBPACK_IMPORTED_MODULE_0___default().ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', marketplaceData.nonce);
      },
      url: tomcBookorgData.root_url + '/wp-json/tomcISBN/v1/populate',
      type: 'POST',
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

const isbnForm = new _modules_ISBNInfoForm__WEBPACK_IMPORTED_MODULE_0__["default"]();
})();

/******/ })()
;
//# sourceMappingURL=index.js.map