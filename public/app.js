(self["webpackChunkbasilicom_ai_image_generator_bundle"] = self["webpackChunkbasilicom_ai_image_generator_bundle"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _app_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./app.scss */ "./assets/app.scss");

__webpack_require__(/*! ./js/editable/image.js */ "./assets/js/editable/image.js");
__webpack_require__(/*! ./js/object/tags/image.js */ "./assets/js/object/tags/image.js");

/***/ }),

/***/ "./assets/js/editable/image.js":
/*!*************************************!*\
  !*** ./assets/js/editable/image.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/AiImageGenerator */ "./assets/js/lib/AiImageGenerator.js");

pimcore.registerNS("pimcore.document.editables.image");
pimcore.document.editables.image = Class.create(pimcore.document.editables.image, {
  label: "Generate Image",
  button: null,
  initialize: function initialize($super, id, name, config, data, inherited) {
    $super(id, name, config, data, inherited);
    this.element = Ext.get(this.id);
    this.element.insertHtml("beforeEnd", "<div class=\"ai-image-generator-button\"><button>" + t(this.label) + "</button></div>");
    this.button = this.element.dom.querySelector(".ai-image-generator-button button");
    this.button.onclick = this.generateAiImage.bind(this);
  },
  generateAiImage: function generateAiImage() {
    var _this = this;
    _lib_AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].generateAiImage({
      context: 'document',
      id: window.editWindow.document.id,
      width: this.element.getWidth(),
      height: this.element.getHeight()
    }, function () {
      _this.button.innerHTML = 'Loading...';
    }, function (jsonData) {
      _this.resetData();
      _this.datax.id = jsonData.id;
      _this.updateImage();
      _this.checkValue(true);
    }, function (jsonData) {
      pimcore.helpers.showNotification(t("error"), jsonData.message, "error");
    }, function () {
      _this.button.innerHTML = _this.label;
    });
  }
});

/***/ }),

/***/ "./assets/js/lib/AiImageGenerator.js":
/*!*******************************************!*\
  !*** ./assets/js/lib/AiImageGenerator.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AiImageGenerator: () => (/* binding */ AiImageGenerator),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var AiImageGenerator = /*#__PURE__*/function () {
  function AiImageGenerator() {
    _classCallCheck(this, AiImageGenerator);
  }
  _createClass(AiImageGenerator, [{
    key: "generateAiImage",
    value: function generateAiImage(payload, onRequest, onSuccess, onError, onDone) {
      var params = new URLSearchParams(payload);
      fetch("/ai-images?" + params.toString()).then(function (response) {
        return response.json();
      }).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }]);
  return AiImageGenerator;
}();
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (new AiImageGenerator());

/***/ }),

/***/ "./assets/js/object/tags/image.js":
/*!****************************************!*\
  !*** ./assets/js/object/tags/image.js ***!
  \****************************************/
/***/ (() => {

pimcore.registerNS("pimcore.object.tags.image");
pimcore.object.tags.image = Class.create(pimcore.object.tags.image, {});

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
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/app.js"));
/******/ }
]);