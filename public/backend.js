(self["webpackChunkbasilicom_ai_image_generator_bundle"] = self["webpackChunkbasilicom_ai_image_generator_bundle"] || []).push([["backend"],{

/***/ "./assets/backend.js":
/*!***************************!*\
  !*** ./assets/backend.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _app_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./app.scss */ "./assets/app.scss");

__webpack_require__(/*! ./js/object/tags/image.js */ "./assets/js/object/tags/image.js");

/***/ }),

/***/ "./assets/js/object/tags/image.js":
/*!****************************************!*\
  !*** ./assets/js/object/tags/image.js ***!
  \****************************************/
/***/ (() => {

pimcore.registerNS("pimcore.object.tags.image");
pimcore.object.tags.image = Class.create(pimcore.object.tags.image, {
  type: "image",
  dirty: false,
  initialize: function initialize(data, fieldConfig) {
    if (data) {
      this.data = data;
    } else {
      this.data = {};
    }
    this.fieldConfig = fieldConfig;
    console.log("whuhuhU");
  }
});

/***/ }),

/***/ "./assets/app.scss":
/*!*************************!*\
  !*** ./assets/app.scss ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/backend.js"));
/******/ }
]);