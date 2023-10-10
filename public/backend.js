"use strict";
(self["webpackChunkbasilicom_ai_image_generator_bundle"] = self["webpackChunkbasilicom_ai_image_generator_bundle"] || []).push([["backend"],{

/***/ "./assets/backend.js":
/*!***************************!*\
  !*** ./assets/backend.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _app_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./app.scss */ "./assets/app.scss");

__webpack_require__(/*! ./js/asset/generate-background-button */ "./assets/js/asset/generate-background-button.js");
__webpack_require__(/*! ./js/asset/inpaint-button.js */ "./assets/js/asset/inpaint-button.js");
__webpack_require__(/*! ./js/asset/upscale-image-button.js */ "./assets/js/asset/upscale-image-button.js");
__webpack_require__(/*! ./js/asset/vary-image-button.js */ "./assets/js/asset/vary-image-button.js");
__webpack_require__(/*! ./js/object/tags/image.js */ "./assets/js/object/tags/image.js");
__webpack_require__(/*! ./js/object/tags/hotspotimage.js */ "./assets/js/object/tags/hotspotimage.js");

/***/ }),

/***/ "./assets/js/asset/generate-background-button.js":
/*!*******************************************************!*\
  !*** ./assets/js/asset/generate-background-button.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_ExtJs_SimpleImage2ImageWindow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/ExtJs/SimpleImage2ImageWindow */ "./assets/js/lib/ExtJs/SimpleImage2ImageWindow.js");
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
var _this = undefined;



document.addEventListener(pimcore.events.postOpenAsset, function (e) {
  if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].INPAINT_BACKGROUND)) {
    return;
  }
  var asset = e.detail.asset;
  var label = t('Generate Background');
  var progressLabel = t('Generating background ...');
  asset.toolbar.insert(3, {
    text: label,
    scale: 'medium',
    handler: function (asset, button) {
      var settingsWindows = new _lib_ExtJs_SimpleImage2ImageWindow__WEBPACK_IMPORTED_MODULE_0__["default"](asset, _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].INPAINT_BACKGROUND);
      settingsWindows.getWindow(function () {
        button.setText(progressLabel);
      }, function () {
        asset.reload();
      }, function () {
        button.setText(label);
      }).show();
    }.bind(_this, asset)
  });
  pimcore.layout.refresh();
});

/***/ }),

/***/ "./assets/js/asset/inpaint-button.js":
/*!*******************************************!*\
  !*** ./assets/js/asset/inpaint-button.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
/* harmony import */ var _lib_ExtJs_InpaintingWindow__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../lib/ExtJs/InpaintingWindow */ "./assets/js/lib/ExtJs/InpaintingWindow.js");
var _this = undefined;



document.addEventListener(pimcore.events.postOpenAsset, function (e) {
  if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_1__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_0__["default"].IMAGE_VARIATIONS)) {
    return;
  }
  var asset = e.detail.asset;
  var label = t('Inpaint');
  var progressLabel = t('Generating in progress ...');
  asset.toolbar.insert(3, {
    text: label,
    scale: 'medium',
    handler: function (asset, button) {
      var inpaintingWindow = new _lib_ExtJs_InpaintingWindow__WEBPACK_IMPORTED_MODULE_2__["default"](asset);
      inpaintingWindow.getWindow(function () {
        button.setText(progressLabel);
      }, function () {
        asset.reload();
      }, function () {
        button.setText(label);
      }).show();
    }.bind(_this, asset)
  });
  pimcore.layout.refresh();
});

/***/ }),

/***/ "./assets/js/asset/upscale-image-button.js":
/*!*************************************************!*\
  !*** ./assets/js/asset/upscale-image-button.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/AiImageGenerator */ "./assets/js/lib/AiImageGenerator.js");
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
var _this = undefined;



document.addEventListener(pimcore.events.postOpenAsset, function (e) {
  if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].UPSCALE)) {
    return;
  }
  var asset = e.detail.asset;
  var label = t('Upscale');
  var progressLabel = t('Upscaling in progress ...');
  var buttonEnabled = asset.data.customSettings.imageWidth < 4096 && asset.data.customSettings.imageHeight < 4096;
  asset.toolbar.insert(3, {
    text: label,
    scale: 'medium',
    disabled: !buttonEnabled,
    tooltip: buttonEnabled ? null : t('Upscaling is only possible up to 4096 pixels.'),
    handler: function (asset, button) {
      _lib_AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].upscaleImage({
        id: asset.id
      }, function () {
        button.setText(progressLabel);
        button.setDisabled(true);
      }, function (jsonData) {
        asset.reload();
      }, function (jsonData) {
        pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
      }, function () {
        button.setDisabled(!buttonEnabled);
        button.setText(label);
      });
    }.bind(_this, asset)
  });
  pimcore.layout.refresh();
});

/***/ }),

/***/ "./assets/js/asset/vary-image-button.js":
/*!**********************************************!*\
  !*** ./assets/js/asset/vary-image-button.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_ExtJs_SimpleImage2ImageWindow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../lib/ExtJs/SimpleImage2ImageWindow */ "./assets/js/lib/ExtJs/SimpleImage2ImageWindow.js");
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
var _this = undefined;



document.addEventListener(pimcore.events.postOpenAsset, function (e) {
  if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].IMAGE_VARIATIONS)) {
    return;
  }
  var asset = e.detail.asset;
  var label = t('Vary image');
  var progressLabel = t('Generating in progress ...');
  asset.toolbar.insert(3, {
    text: label,
    scale: 'medium',
    handler: function (asset, button) {
      var settingsWindows = new _lib_ExtJs_SimpleImage2ImageWindow__WEBPACK_IMPORTED_MODULE_0__["default"](asset, _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].IMAGE_VARIATIONS);
      settingsWindows.getWindow(function () {
        button.setText(progressLabel);
      }, function () {
        asset.reload();
      }, function () {
        button.setText(label);
      }).show();
    }.bind(_this, asset)
  });
  pimcore.layout.refresh();
});

/***/ }),

/***/ "./assets/js/lib/AiImageGenerator.js":
/*!*******************************************!*\
  !*** ./assets/js/lib/AiImageGenerator.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
var GET = /*#__PURE__*/function () {
  var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
    var url,
      data,
      params,
      response,
      _args = arguments;
    return _regeneratorRuntime().wrap(function _callee$(_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          url = _args.length > 0 && _args[0] !== undefined ? _args[0] : '';
          data = _args.length > 1 && _args[1] !== undefined ? _args[1] : {};
          params = new URLSearchParams(data);
          _context.next = 5;
          return fetch(url + '?' + params.toString(), {
            method: 'GET'
          });
        case 5:
          response = _context.sent;
          return _context.abrupt("return", response.json());
        case 7:
        case "end":
          return _context.stop();
      }
    }, _callee);
  }));
  return function GET() {
    return _ref.apply(this, arguments);
  };
}();
var POST = /*#__PURE__*/function () {
  var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
    var url,
      data,
      response,
      _args2 = arguments;
    return _regeneratorRuntime().wrap(function _callee2$(_context2) {
      while (1) switch (_context2.prev = _context2.next) {
        case 0:
          url = _args2.length > 0 && _args2[0] !== undefined ? _args2[0] : '';
          data = _args2.length > 1 && _args2[1] !== undefined ? _args2[1] : {};
          _context2.next = 4;
          return fetch(url, {
            method: 'POST',
            body: JSON.stringify(data)
          });
        case 4:
          response = _context2.sent;
          return _context2.abrupt("return", response.json());
        case 6:
        case "end":
          return _context2.stop();
      }
    }, _callee2);
  }));
  return function POST() {
    return _ref2.apply(this, arguments);
  };
}();
var FORMPOST = /*#__PURE__*/function () {
  var _ref3 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
    var url,
      data,
      formData,
      key,
      response,
      _args3 = arguments;
    return _regeneratorRuntime().wrap(function _callee3$(_context3) {
      while (1) switch (_context3.prev = _context3.next) {
        case 0:
          url = _args3.length > 0 && _args3[0] !== undefined ? _args3[0] : '';
          data = _args3.length > 1 && _args3[1] !== undefined ? _args3[1] : {};
          formData = new FormData();
          for (key in data) {
            formData.append(key, data[key]);
          }
          _context3.next = 6;
          return fetch(url, {
            method: 'POST',
            body: formData
          });
        case 6:
          response = _context3.sent;
          return _context3.abrupt("return", response.json());
        case 8:
        case "end":
          return _context3.stop();
      }
    }, _callee3);
  }));
  return function FORMPOST() {
    return _ref3.apply(this, arguments);
  };
}();
var AiImageGenerator = /*#__PURE__*/function () {
  function AiImageGenerator() {
    _classCallCheck(this, AiImageGenerator);
  }
  _createClass(AiImageGenerator, [{
    key: "generateAiImageByContext",
    value: function generateAiImageByContext(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_by_element_context', payload);
      onRequest();
      POST(url, payload).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }, {
    key: "upscaleImage",
    value: function upscaleImage(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_upscale', payload);
      onRequest();
      POST(url, payload).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }, {
    key: "inpaintImage",
    value: function inpaintImage(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_inpaint', {
        id: payload.id
      });
      onRequest();
      FORMPOST(url, payload).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }, {
    key: "save",
    value: function save(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_save', {
        id: payload.id
      });
      onRequest();
      FORMPOST(url, payload).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }, {
    key: "varyImage",
    value: function varyImage(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_vary', payload);
      onRequest();
      POST(url, payload).then(function (jsonData) {
        if (jsonData.success === true) {
          onSuccess(jsonData);
        } else {
          onError(jsonData);
        }
      })["finally"](function () {
        onDone();
      });
    }
  }, {
    key: "inpaintBackground",
    value: function inpaintBackground(payload, onRequest, onSuccess, onError, onDone) {
      var url = Routing.generate('ai_image_inpaint_background', payload);
      onRequest();
      POST(url, payload).then(function (jsonData) {
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

/***/ "./assets/js/lib/ConfigStorage.js":
/*!****************************************!*\
  !*** ./assets/js/lib/ConfigStorage.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var storage = function storage() {
  return pimcore.settings.AiImageGeneratorBundle || {};
};
var ConfigStorage = /*#__PURE__*/function () {
  function ConfigStorage() {
    _classCallCheck(this, ConfigStorage);
  }
  _createClass(ConfigStorage, [{
    key: "set",
    value: function set(key, value) {
      storage()[key] = value;
    }
  }, {
    key: "get",
    value: function get(key) {
      var defaultValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      return storage()[key] || defaultValue;
    }
  }]);
  return ConfigStorage;
}();
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (new ConfigStorage());

/***/ }),

/***/ "./assets/js/lib/ExtJs/AspectRatioStore.js":
/*!*************************************************!*\
  !*** ./assets/js/lib/ExtJs/AspectRatioStore.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   aspectRatioStore: () => (/* binding */ aspectRatioStore),
/* harmony export */   aspectRatioStoreDefault: () => (/* binding */ aspectRatioStoreDefault)
/* harmony export */ });
var aspectRatioStore = new Ext.data.Store({
  fields: ['key', 'value'],
  data: [{
    key: '16:9',
    value: '16:9'
  }, {
    key: '4:3',
    value: '4:3'
  }, {
    key: '3:2',
    value: '3:2'
  }, {
    key: '16:10',
    value: '16:10'
  }, {
    key: '5:4',
    value: '5:4'
  }, {
    key: '1:1',
    value: '1:1'
  }, {
    key: '21:9',
    value: '21:9'
  }]
});
var aspectRatioStoreDefault = '1:1';

/***/ }),

/***/ "./assets/js/lib/ExtJs/InpaintingWindow.js":
/*!*************************************************!*\
  !*** ./assets/js/lib/ExtJs/InpaintingWindow.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ InpaintingWindow)
/* harmony export */ });
/* harmony import */ var _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../AiImageGenerator */ "./assets/js/lib/AiImageGenerator.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }


// todo => refactor!
var InpaintingWindow = /*#__PURE__*/function () {
  function InpaintingWindow(asset) {
    _classCallCheck(this, InpaintingWindow);
    _defineProperty(this, "asset", void 0);
    this.asset = asset;
  }
  _createClass(InpaintingWindow, [{
    key: "getWindow",
    value: function getWindow(onRequest, onSuccess, onDone) {
      var onError = function onError() {};
      return Ext.create('AiImageGeneratorBundle.view.CanvasWindow', {
        asset: this.asset,
        onRequest: onRequest,
        onSuccess: onSuccess,
        onError: onError,
        onDone: onDone
      });
    }
  }]);
  return InpaintingWindow;
}();

var asset;
var backgroundCanvas;
var backgroundImage;
var backgroundImageNewWidth;
var backgroundImageNewHeight;
var backgroundImageX;
var backgroundImageY;
var canvas;
var isDrawing = false;
var lineWidth = 5;
var x;
var y;
var drawLine = function drawLine(startX, startY, endX, endY) {
  var ctx = canvas.getContext('2d');
  ctx.beginPath();
  ctx.strokeStyle = '#000000';
  ctx.lineWidth = lineWidth;
  ctx.lineCap = 'round';
  ctx.moveTo(startX, startY);
  ctx.lineTo(endX, endY);
  ctx.stroke();
  ctx.closePath();
};
var receiveMaskImage = function receiveMaskImage() {
  var tempCanvas = document.createElement('canvas');
  tempCanvas.width = backgroundImageNewWidth;
  tempCanvas.height = backgroundImageNewHeight;
  tempCanvas.getContext('2d').fillStyle = "white";
  tempCanvas.getContext('2d').fillRect(0, 0, backgroundImageNewWidth, backgroundImageNewHeight);
  tempCanvas.getContext('2d').drawImage(canvas, backgroundImageX, backgroundImageY, backgroundImageNewWidth, backgroundImageNewHeight, 0, 0, backgroundImageNewWidth, backgroundImageNewHeight);
  return getImage(tempCanvas);
};
var getImage = function getImage(canvas) {
  var dataUrl = canvas.toDataURL('image/jpeg', 1);
  var byteString = atob(dataUrl.split(',')[1]);
  var mimeString = dataUrl.split(',')[0].split(':')[1].split(';')[0];
  var ab = new ArrayBuffer(byteString.length);
  var ia = new Uint8Array(ab);
  for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  return new Blob([ab], {
    type: mimeString
  });
};
var drawBackground = function drawBackground(img) {
  var aspectRatio = img.width / img.height;
  var canvasAspectRatio = canvas.width / canvas.height;
  var scaleFactor = aspectRatio > canvasAspectRatio ? canvas.width / img.width : canvas.height / img.height;
  var x = (canvas.width - img.width * scaleFactor) / 2;
  var y = (canvas.height - img.height * scaleFactor) / 2;
  backgroundImageNewWidth = img.width * scaleFactor;
  backgroundImageNewHeight = img.height * scaleFactor;
  backgroundImageX = x;
  backgroundImageY = y;
  backgroundCanvas.getContext('2d').drawImage(img, x, y, img.width * scaleFactor, img.height * scaleFactor);
  backgroundImage = img;
};
Ext.define('AiImageGeneratorBundle.view.CanvasWindow', {
  extend: 'Ext.Window',
  xtype: 'canvaswindow',
  // This is the xtype to use in your code

  title: 'Inpainting',
  width: 1024,
  height: 1024 + 220,
  layout: 'fit',
  modal: true,
  closable: true,
  // Allow closing the window

  config: {
    asset: null,
    onRequest: function onRequest() {},
    onSuccess: function onSuccess() {},
    onError: function onError() {},
    onDone: function onDone() {}
  },
  initComponent: function initComponent() {
    var _window$localStorage$;
    asset = this.getAsset();
    var prompt = (_window$localStorage$ = window.localStorage.getItem('prompt')) !== null && _window$localStorage$ !== void 0 ? _window$localStorage$ : '';
    var onRequest = this.getOnRequest();
    var onSuccess = this.getOnSuccess();
    var onError = this.getOnError();
    var onDone = this.getOnDone();
    var img = new Image();
    img.src = asset.data.url;
    this.items = [{
      xtype: 'container',
      items: [{
        xtype: 'component',
        itemId: 'canvasContainer',
        width: '100%',
        height: 1024,
        listeners: {
          afterrender: function afterrender() {
            canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            canvas.style.position = 'relative';
            canvas.style.opacity = .7;
            canvas.style.zIndex = 10;
            backgroundCanvas = canvas.cloneNode(true);
            backgroundCanvas.style.position = 'absolute';
            backgroundCanvas.style.top = 0;
            backgroundCanvas.style.left = 0;
            backgroundCanvas.style.zIndex = 5;
            backgroundCanvas.style.pointerEvents = 'none';
            backgroundCanvas.style.borderTop = '1px solid #000';
            backgroundCanvas.style.borderBottom = '1px solid #000';
            backgroundCanvas.style.opacity = 1;
            backgroundCanvas.style.background = '#fff';
            this.getEl().dom.appendChild(backgroundCanvas);
            this.getEl().dom.appendChild(canvas);
            canvas.addEventListener('mousedown', function (event) {
              isDrawing = true;
              lineWidth = Ext.getCmp('ai_image_generator_bundle_line_width_slider').getValue();
              x = event.offsetX;
              y = event.offsetY;
            });
            canvas.addEventListener('mousemove', function (event) {
              if (!isDrawing) return;
              var xM = event.offsetX;
              var yM = event.offsetY;
              drawLine(x, y, xM, yM);
              x = xM;
              y = yM;
            });
            canvas.addEventListener('mouseup', function () {
              var ctx = canvas.getContext('2d');
              isDrawing = false;
              ctx.beginPath();
            });
            img.onload = function () {
              drawBackground(img);
            };
          }
        }
      }, {
        xtype: 'textareafield',
        itemId: 'prompt',
        name: 'prompt',
        value: prompt,
        grow: true,
        width: 'calc(100% - 20px)',
        fieldLabel: t('Prompt'),
        padding: '10'
      }]
    }];
    this.buttons = [{
      text: t('cancel'),
      iconCls: 'pimcore_icon_cancel',
      handler: function handler() {
        this.up('window').close();
      }
    }, {
      text: t('Generate'),
      handler: function handler() {
        // Save or process the content of the temporary canvas as needed
        var prompt = this.up('window').down('#prompt').getValue();
        window.localStorage.setItem('prompt', prompt);
        var confirmButton = this.up('window').down('#confirmButton');
        var generateButton = this;

        // Create a FormData object to send the image data as a file
        _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].inpaintImage({
          id: asset.id,
          prompt: prompt,
          mask: receiveMaskImage(),
          draft: true
        }, function () {
          confirmButton.setDisabled(true);
          generateButton.setDisabled(true);
        }, function (response) {
          confirmButton.setDisabled(false);
          var img = new Image();
          img.src = 'data:image/png;base64,' + response.image;
          img.onload = function () {
            drawBackground(img);
          };
        }, function () {}, function () {
          generateButton.setDisabled(false);
        });
      }
    }, {
      text: t('apply'),
      iconCls: 'pimcore_icon_apply',
      id: 'confirmButton',
      disabled: true,
      handler: function handler() {
        var _this = this;
        // Save or process the content of the temporary canvas as needed
        var prompt = this.up('window').down('#prompt').getValue();
        window.localStorage.setItem('prompt', prompt);
        var tempCanvas = document.createElement('canvas');
        tempCanvas.width = backgroundImageNewWidth;
        tempCanvas.height = backgroundImageNewHeight;
        tempCanvas.getContext('2d').drawImage(backgroundCanvas, backgroundImageX, backgroundImageY, backgroundImageNewWidth, backgroundImageNewHeight, 0, 0, backgroundImageNewWidth, backgroundImageNewHeight);
        var finalAsset = getImage(tempCanvas);

        // Create a FormData object to send the image data as a file
        _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].save({
          id: asset.id,
          data: finalAsset
        }, onRequest, function () {
          asset.data.url = finalAsset;
          asset.reload();
          _this.up('window').close();
        }, onError, onDone);
      }
    }];
    this.tbar = [{
      text: 'Clear',
      handler: function handler() {
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBackground(backgroundImage);
      }
    }, {
      xtype: 'slider',
      fieldLabel: 'Line Width',
      width: 500,
      minValue: 5,
      maxValue: 50,
      value: 20,
      id: 'ai_image_generator_bundle_line_width_slider'
    }];
    this.callParent(arguments);
  }
});

/***/ }),

/***/ "./assets/js/lib/ExtJs/SimpleImage2ImageWindow.js":
/*!********************************************************!*\
  !*** ./assets/js/lib/ExtJs/SimpleImage2ImageWindow.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ SimpleImage2ImageWindow)
/* harmony export */ });
/* harmony import */ var _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../AiImageGenerator */ "./assets/js/lib/AiImageGenerator.js");
/* harmony import */ var _FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }



var SimpleImage2ImageWindow = /*#__PURE__*/function () {
  function SimpleImage2ImageWindow(asset, context) {
    _classCallCheck(this, SimpleImage2ImageWindow);
    _defineProperty(this, "asset", void 0);
    _defineProperty(this, "context", void 0);
    this.asset = asset;
    this.context = context;
  }
  _createClass(SimpleImage2ImageWindow, [{
    key: "getWindow",
    value: function getWindow(onRequest, onSuccess, onDone) {
      var _window$localStorage$;
      var previousPrompt = (_window$localStorage$ = window.localStorage.getItem('prompt')) !== null && _window$localStorage$ !== void 0 ? _window$localStorage$ : '';
      var prompt = this.asset.data.metadata.hasOwnProperty('prompt~') ? this.asset.data.metadata['prompt~'].data : previousPrompt;
      var seed = this.asset.data.metadata.hasOwnProperty('seed~') ? this.asset.data.metadata['seed~'].data : -1;
      var items = [{
        xtype: 'textareafield',
        itemId: 'prompt',
        name: 'prompt',
        value: prompt,
        grow: true,
        width: '100%',
        fieldLabel: t('Prompt'),
        tooltip: '<i>' + t('Leave empty to create prompt from various properties.') + '</i>'
      }, {
        xtype: 'tbtext',
        fieldLabel: '',
        text: '<i>' + t('Leave empty to create prompt automatically.') + '</i>',
        scale: 'small',
        padding: '0 0 20 105',
        width: '100%'
      }];
      if (_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isSeedingSupported(this.context)) {
        items = [].concat(_toConsumableArray(items), [{
          xtype: 'numberfield',
          name: 'seed',
          value: seed,
          itemId: 'seed',
          width: '100%',
          fieldLabel: t('Seed')
        }]);
      }
      var settingsWindow = new Ext.Window({
        title: t('Generate image'),
        width: 400,
        bodyStyle: 'padding: 10px;',
        closable: false,
        plain: true,
        items: items,
        buttons: [{
          text: t('cancel'),
          iconCls: 'pimcore_icon_cancel',
          handler: function handler() {
            settingsWindow.close();
          }
        }, {
          text: t('apply'),
          iconCls: 'pimcore_icon_apply',
          handler: function () {
            var prompt = settingsWindow.getComponent("prompt").getValue();
            var seed = settingsWindow.getComponent("seed") ? settingsWindow.getComponent("seed").getValue() : -1;
            window.localStorage.setItem('prompt', prompt);
            window.localStorage.setItem('seed', seed);
            var payload = {
              id: this.asset.id,
              prompt: prompt,
              seed: seed
            };
            var extendedOnRequest = function extendedOnRequest() {
              onRequest();
              settingsWindow.close();
            };
            var onError = function onError(jsonData) {
              pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
            };
            if (this.context === _FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].IMAGE_VARIATIONS) {
              _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].varyImage(payload, extendedOnRequest, onSuccess, onError, onDone);
            } else if (this.context === _FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].INPAINT_BACKGROUND) {
              _AiImageGenerator__WEBPACK_IMPORTED_MODULE_0__["default"].inpaintBackground(payload, extendedOnRequest, onSuccess, onError, onDone);
            }
          }.bind(this)
        }]
      });
      return settingsWindow;
    }
  }]);
  return SimpleImage2ImageWindow;
}();


/***/ }),

/***/ "./assets/js/lib/ExtJs/SimpleText2ImageWindow.js":
/*!*******************************************************!*\
  !*** ./assets/js/lib/ExtJs/SimpleText2ImageWindow.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ SimpleText2ImageWindow)
/* harmony export */ });
/* harmony import */ var _AspectRatioStore__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AspectRatioStore */ "./assets/js/lib/ExtJs/AspectRatioStore.js");
/* harmony import */ var _AiImageGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../AiImageGenerator */ "./assets/js/lib/AiImageGenerator.js");
/* harmony import */ var _FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../FeatureHelper */ "./assets/js/lib/FeatureHelper.js");
/* harmony import */ var _FeatureEnum__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }




var SimpleText2ImageWindow = /*#__PURE__*/function () {
  function SimpleText2ImageWindow(id, context) {
    _classCallCheck(this, SimpleText2ImageWindow);
    _defineProperty(this, "id", void 0);
    _defineProperty(this, "context", void 0);
    this.id = id;
    this.context = context;
  }
  _createClass(SimpleText2ImageWindow, [{
    key: "getWindow",
    value: function getWindow(onRequest, onSuccess, onDone) {
      var _window$localStorage$;
      var prompt = (_window$localStorage$ = window.localStorage.getItem('prompt')) !== null && _window$localStorage$ !== void 0 ? _window$localStorage$ : '';
      var items = [{
        xtype: 'textareafield',
        itemId: 'prompt',
        name: 'prompt',
        value: prompt,
        grow: true,
        width: '100%',
        fieldLabel: t('Prompt'),
        tooltip: '<i>' + t('Leave empty to create prompt from various properties.') + '</i>'
      }, {
        xtype: 'tbtext',
        fieldLabel: '',
        text: '<i>' + t('Leave empty to create prompt automatically.') + '</i>',
        scale: 'small',
        padding: '0 0 20 105',
        width: '100%'
      }];
      if (_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isAspectRatioSupported(_FeatureEnum__WEBPACK_IMPORTED_MODULE_3__["default"].TXT2IMG)) {
        var _window$localStorage$2;
        _AspectRatioStore__WEBPACK_IMPORTED_MODULE_0__.aspectRatioStore.load();
        var aspectRatio = (_window$localStorage$2 = window.localStorage.getItem('aspectRatio')) !== null && _window$localStorage$2 !== void 0 ? _window$localStorage$2 : _AspectRatioStore__WEBPACK_IMPORTED_MODULE_0__.aspectRatioStoreDefault;
        items = [{
          xtype: 'combobox',
          itemId: 'aspectRatio',
          name: 'aspectRatio',
          triggerAction: 'all',
          selectOnFocus: true,
          fieldLabel: t('Aspect Ratio'),
          store: _AspectRatioStore__WEBPACK_IMPORTED_MODULE_0__.aspectRatioStore,
          value: aspectRatio,
          displayField: 'key',
          valueField: 'value',
          width: '100%'
        }].concat(_toConsumableArray(items));
      }
      if (_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isSeedingSupported(_FeatureEnum__WEBPACK_IMPORTED_MODULE_3__["default"].TXT2IMG)) {
        var _window$localStorage$3;
        var seed = (_window$localStorage$3 = window.localStorage.getItem('seed')) !== null && _window$localStorage$3 !== void 0 ? _window$localStorage$3 : -1;
        items = [].concat(_toConsumableArray(items), [{
          xtype: 'numberfield',
          name: 'seed',
          value: seed,
          itemId: 'seed',
          width: '100%',
          fieldLabel: t('Seed')
        }]);
      }
      var settingsWindow = new Ext.Window({
        title: t('Generate image'),
        width: 400,
        bodyStyle: 'padding: 10px;',
        closable: false,
        plain: true,
        items: items,
        buttons: [{
          text: t('cancel'),
          iconCls: 'pimcore_icon_cancel',
          handler: function handler() {
            settingsWindow.close();
          }
        }, {
          text: t('apply'),
          iconCls: 'pimcore_icon_apply',
          handler: function () {
            var prompt = settingsWindow.getComponent("prompt").getValue();
            window.localStorage.setItem('prompt', prompt);
            var payload = {
              context: this.context,
              id: this.id,
              prompt: prompt
            };
            if (_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isAspectRatioSupported(_FeatureEnum__WEBPACK_IMPORTED_MODULE_3__["default"].TXT2IMG)) {
              var _aspectRatio = settingsWindow.getComponent("aspectRatio").getValue();
              window.localStorage.setItem('aspectRatio', _aspectRatio);
              payload.aspectRatio = _aspectRatio;
            }
            if (_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isSeedingSupported(_FeatureEnum__WEBPACK_IMPORTED_MODULE_3__["default"].TXT2IMG)) {
              var _seed = settingsWindow.getComponent("seed") ? settingsWindow.getComponent("seed").getValue() : -1;
              window.localStorage.setItem('seed', _seed);
              payload.seed = _seed;
            }
            _AiImageGenerator__WEBPACK_IMPORTED_MODULE_1__["default"].generateAiImageByContext(payload, function () {
              onRequest();
              settingsWindow.close();
            }, function (jsonData) {
              onSuccess(jsonData);
            }, function (jsonData) {
              pimcore.helpers.showNotification(t('error'), jsonData.message, 'error');
            }, function () {
              onDone();
            });
          }.bind(this)
        }]
      });
      return settingsWindow;
    }
  }]);
  return SimpleText2ImageWindow;
}();


/***/ }),

/***/ "./assets/js/lib/FeatureEnum.js":
/*!**************************************!*\
  !*** ./assets/js/lib/FeatureEnum.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  TXT2IMG: 'txt2img',
  UPSCALE: 'upscale',
  INPAINT_BACKGROUND: 'inpaint_background',
  IMAGE_VARIATIONS: 'image_variations'
});

/***/ }),

/***/ "./assets/js/lib/FeatureHelper.js":
/*!****************************************!*\
  !*** ./assets/js/lib/FeatureHelper.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _ConfigStorage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ConfigStorage */ "./assets/js/lib/ConfigStorage.js");
/* harmony import */ var _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ServiceEnum */ "./assets/js/lib/ServiceEnum.js");
/* harmony import */ var _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }



var FeatureHelper = /*#__PURE__*/function () {
  function FeatureHelper() {
    _classCallCheck(this, FeatureHelper);
  }
  _createClass(FeatureHelper, [{
    key: "isFeatureEnabled",
    value: function isFeatureEnabled(feature) {
      var adapter = _ConfigStorage__WEBPACK_IMPORTED_MODULE_0__["default"].get('adapter', null);
      var featureService = adapter ? adapter[feature] : null;
      switch (featureService) {
        case _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].STABLE_DIFFUSION:
          switch (feature) {
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].TXT2IMG:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].UPSCALE:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].INPAINT_BACKGROUND:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].IMAGE_VARIATIONS:
              return true;
            default:
              return false;
          }
        case _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].DREAM_STUDIO:
          switch (feature) {
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].TXT2IMG:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].UPSCALE:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].IMAGE_VARIATIONS:
              return true;
            default:
              return false;
          }
        case _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].OPEN_AI:
          switch (feature) {
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].TXT2IMG:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].IMAGE_VARIATIONS:
              return true;
            default:
              return false;
          }
        case _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].CLIP_DROP:
          switch (feature) {
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].TXT2IMG:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].UPSCALE:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].INPAINT_BACKGROUND:
            case _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].IMAGE_VARIATIONS:
              return true;
            default:
              return false;
          }
        default:
          return false;
      }
    }
  }, {
    key: "isSeedingSupported",
    value: function isSeedingSupported(feature) {
      var adapter = _ConfigStorage__WEBPACK_IMPORTED_MODULE_0__["default"].get('adapter', null);
      var featureService = adapter ? adapter[feature] : null;
      return featureService === _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].STABLE_DIFFUSION || featureService === _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].DREAM_STUDIO;
    }
  }, {
    key: "isAspectRatioSupported",
    value: function isAspectRatioSupported(feature) {
      var adapter = _ConfigStorage__WEBPACK_IMPORTED_MODULE_0__["default"].get('adapter', null);
      var featureService = adapter ? adapter[feature] : null;
      return feature === _FeatureEnum__WEBPACK_IMPORTED_MODULE_2__["default"].TXT2IMG && featureService !== _ServiceEnum__WEBPACK_IMPORTED_MODULE_1__["default"].CLIP_DROP;
    }
  }]);
  return FeatureHelper;
}();
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (new FeatureHelper());

/***/ }),

/***/ "./assets/js/lib/ServiceEnum.js":
/*!**************************************!*\
  !*** ./assets/js/lib/ServiceEnum.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  OPEN_AI: 'OpenAi',
  DREAM_STUDIO: 'DreamStudio',
  STABLE_DIFFUSION: 'StableDiffusion',
  CLIP_DROP: 'ClipDrop'
});

/***/ }),

/***/ "./assets/js/object/tags/hotspotimage.js":
/*!***********************************************!*\
  !*** ./assets/js/object/tags/hotspotimage.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_ExtJs_SimpleText2ImageWindow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../lib/ExtJs/SimpleText2ImageWindow */ "./assets/js/lib/ExtJs/SimpleText2ImageWindow.js");
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");



pimcore.registerNS('pimcore.object.tags.hotspotimage');
pimcore.object.tags.hotspotimage = Class.create(pimcore.object.tags.hotspotimage, {
  label: 'Generate Image',
  button: null,
  getLayoutEdit: function getLayoutEdit($super) {
    var component = $super();
    if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].TXT2IMG)) {
      return component;
    }
    var toolbar = component.getDockedItems('toolbar')[0];
    this.button = new Ext.button.Button({
      text: t(this.label),
      handler: this.generateAiImage.bind(this)
    });
    toolbar.add(this.button);
    return component;
  },
  generateAiImage: function generateAiImage() {
    var _this = this;
    var container = this.component.body.dom;
    var simpleText2ImageWindow = new _lib_ExtJs_SimpleText2ImageWindow__WEBPACK_IMPORTED_MODULE_0__["default"](this.context.objectId, 'object');
    simpleText2ImageWindow.getWindow(function () {
      container.classList.add('ai-image-loader');
      _this.button.innerHTML = t('Loading...');
    }, function (jsonData) {
      _this.empty(true);
      if (_this.data.id !== jsonData.id) {
        _this.dirty = true;
      }
      _this.data.id = jsonData.id;
      _this.updateImage();
    }, function () {
      container.classList.remove('ai-image-loader');
      _this.button.innerHTML = t(_this.label);
    }).show();
  }
});

/***/ }),

/***/ "./assets/js/object/tags/image.js":
/*!****************************************!*\
  !*** ./assets/js/object/tags/image.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_ExtJs_SimpleText2ImageWindow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../lib/ExtJs/SimpleText2ImageWindow */ "./assets/js/lib/ExtJs/SimpleText2ImageWindow.js");
/* harmony import */ var _lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../lib/FeatureEnum */ "./assets/js/lib/FeatureEnum.js");
/* harmony import */ var _lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../lib/FeatureHelper */ "./assets/js/lib/FeatureHelper.js");



pimcore.registerNS('pimcore.object.tags.image');
pimcore.object.tags.image = Class.create(pimcore.object.tags.image, {
  label: 'Generate Image',
  button: null,
  getLayoutEdit: function getLayoutEdit($super) {
    var component = $super();
    if (!_lib_FeatureHelper__WEBPACK_IMPORTED_MODULE_2__["default"].isFeatureEnabled(_lib_FeatureEnum__WEBPACK_IMPORTED_MODULE_1__["default"].TXT2IMG)) {
      return component;
    }
    var toolbar = component.getDockedItems('toolbar')[0];
    this.button = new Ext.button.Button({
      text: t(this.label),
      handler: this.generateAiImage.bind(this)
    });
    toolbar.add(this.button);
    return component;
  },
  generateAiImage: function generateAiImage() {
    var _this = this;
    var container = this.component.body.dom;
    var simpleText2ImageWindow = new _lib_ExtJs_SimpleText2ImageWindow__WEBPACK_IMPORTED_MODULE_0__["default"](this.context.objectId, 'object');
    simpleText2ImageWindow.getWindow(function () {
      container.classList.add('ai-image-loader');
      _this.button.innerHTML = t('Loading...');
    }, function (jsonData) {
      _this.empty(true);
      if (_this.data.id !== jsonData.id) {
        _this.dirty = true;
      }
      _this.data.id = jsonData.id;
      _this.updateImage();
    }, function () {
      container.classList.remove('ai-image-loader');
      _this.button.innerHTML = t(_this.label);
    }).show();
  }
});

/***/ }),

/***/ "./assets/app.scss":
/*!*************************!*\
  !*** ./assets/app.scss ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/backend.js"));
/******/ }
]);