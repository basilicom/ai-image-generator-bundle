/*! For license information please see editmode.js.LICENSE.txt */
"use strict";(self.webpackChunkbasilicom_ai_image_generator_bundle=self.webpackChunkbasilicom_ai_image_generator_bundle||[]).push([[288],{366:(e,t,r)=>{r(72)},72:(e,r,n)=>{n.r(r);var o=n(778),i=n(844),a=n(695);pimcore.registerNS("pimcore.document.editables.image"),pimcore.document.editables.image=Class.create(pimcore.document.editables.image,{label:"Generate Image",button:null,initialize:function initialize($super,e,r,n,o,u){$super(e,r,n,o,u),a.Z.isFeatureEnabled(i.Z.TXT2IMG)&&(this.element=Ext.get(this.id),this.element.insertHtml("beforeEnd",'<div class="ai-image-generator-button"><button>'+t(this.label)+"</button></div>"),this.button=this.element.dom.querySelector(".ai-image-generator-button button"),this.button.onclick=this.generateAiImage.bind(this))},generateAiImage:function generateAiImage(){var e=this;new o.Z(window.editWindow.document.id,"document").getWindow((function(){e.button.innerHTML=t("Loading...")}),(function(t){e.resetData(),e.datax.id=t.id,e.updateImage(),e.checkValue(!0)}),(function(){e.button.innerHTML=t(e.label)})).show()}})},63:(e,t,r)=>{function _typeof(e){return _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},_typeof(e)}function _defineProperties(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,(o=n.key,i=void 0,i=function _toPrimitive(e,t){if("object"!==_typeof(e)||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var n=r.call(e,t||"default");if("object"!==_typeof(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(o,"string"),"symbol"===_typeof(i)?i:String(i)),n)}var o,i}function _regeneratorRuntime(){_regeneratorRuntime=function _regeneratorRuntime(){return e};var e={},t=Object.prototype,r=t.hasOwnProperty,n=Object.defineProperty||function(e,t,r){e[t]=r.value},o="function"==typeof Symbol?Symbol:{},i=o.iterator||"@@iterator",a=o.asyncIterator||"@@asyncIterator",u=o.toStringTag||"@@toStringTag";function define(e,t,r){return Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{define({},"")}catch(e){define=function define(e,t,r){return e[t]=r}}function wrap(e,t,r,o){var i=t&&t.prototype instanceof Generator?t:Generator,a=Object.create(i.prototype),u=new Context(o||[]);return n(a,"_invoke",{value:makeInvokeMethod(e,r,u)}),a}function tryCatch(e,t,r){try{return{type:"normal",arg:e.call(t,r)}}catch(e){return{type:"throw",arg:e}}}e.wrap=wrap;var c={};function Generator(){}function GeneratorFunction(){}function GeneratorFunctionPrototype(){}var l={};define(l,i,(function(){return this}));var s=Object.getPrototypeOf,f=s&&s(s(values([])));f&&f!==t&&r.call(f,i)&&(l=f);var p=GeneratorFunctionPrototype.prototype=Generator.prototype=Object.create(l);function defineIteratorMethods(e){["next","throw","return"].forEach((function(t){define(e,t,(function(e){return this._invoke(t,e)}))}))}function AsyncIterator(e,t){function invoke(n,o,i,a){var u=tryCatch(e[n],e,o);if("throw"!==u.type){var c=u.arg,l=c.value;return l&&"object"==_typeof(l)&&r.call(l,"__await")?t.resolve(l.__await).then((function(e){invoke("next",e,i,a)}),(function(e){invoke("throw",e,i,a)})):t.resolve(l).then((function(e){c.value=e,i(c)}),(function(e){return invoke("throw",e,i,a)}))}a(u.arg)}var o;n(this,"_invoke",{value:function value(e,r){function callInvokeWithMethodAndArg(){return new t((function(t,n){invoke(e,r,t,n)}))}return o=o?o.then(callInvokeWithMethodAndArg,callInvokeWithMethodAndArg):callInvokeWithMethodAndArg()}})}function makeInvokeMethod(e,t,r){var n="suspendedStart";return function(o,i){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===o)throw i;return doneResult()}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var u=maybeInvokeDelegate(a,r);if(u){if(u===c)continue;return u}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var l=tryCatch(e,t,r);if("normal"===l.type){if(n=r.done?"completed":"suspendedYield",l.arg===c)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(n="completed",r.method="throw",r.arg=l.arg)}}}function maybeInvokeDelegate(e,t){var r=t.method,n=e.iterator[r];if(void 0===n)return t.delegate=null,"throw"===r&&e.iterator.return&&(t.method="return",t.arg=void 0,maybeInvokeDelegate(e,t),"throw"===t.method)||"return"!==r&&(t.method="throw",t.arg=new TypeError("The iterator does not provide a '"+r+"' method")),c;var o=tryCatch(n,e.iterator,t.arg);if("throw"===o.type)return t.method="throw",t.arg=o.arg,t.delegate=null,c;var i=o.arg;return i?i.done?(t[e.resultName]=i.value,t.next=e.nextLoc,"return"!==t.method&&(t.method="next",t.arg=void 0),t.delegate=null,c):i:(t.method="throw",t.arg=new TypeError("iterator result is not an object"),t.delegate=null,c)}function pushTryEntry(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function resetTryEntry(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function Context(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(pushTryEntry,this),this.reset(!0)}function values(e){if(e){var t=e[i];if(t)return t.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var n=-1,o=function next(){for(;++n<e.length;)if(r.call(e,n))return next.value=e[n],next.done=!1,next;return next.value=void 0,next.done=!0,next};return o.next=o}}return{next:doneResult}}function doneResult(){return{value:void 0,done:!0}}return GeneratorFunction.prototype=GeneratorFunctionPrototype,n(p,"constructor",{value:GeneratorFunctionPrototype,configurable:!0}),n(GeneratorFunctionPrototype,"constructor",{value:GeneratorFunction,configurable:!0}),GeneratorFunction.displayName=define(GeneratorFunctionPrototype,u,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===GeneratorFunction||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,GeneratorFunctionPrototype):(e.__proto__=GeneratorFunctionPrototype,define(e,u,"GeneratorFunction")),e.prototype=Object.create(p),e},e.awrap=function(e){return{__await:e}},defineIteratorMethods(AsyncIterator.prototype),define(AsyncIterator.prototype,a,(function(){return this})),e.AsyncIterator=AsyncIterator,e.async=function(t,r,n,o,i){void 0===i&&(i=Promise);var a=new AsyncIterator(wrap(t,r,n,o),i);return e.isGeneratorFunction(r)?a:a.next().then((function(e){return e.done?e.value:a.next()}))},defineIteratorMethods(p),define(p,u,"Generator"),define(p,i,(function(){return this})),define(p,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=Object(e),r=[];for(var n in t)r.push(n);return r.reverse(),function next(){for(;r.length;){var e=r.pop();if(e in t)return next.value=e,next.done=!1,next}return next.done=!0,next}},e.values=values,Context.prototype={constructor:Context,reset:function reset(e){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(resetTryEntry),!e)for(var t in this)"t"===t.charAt(0)&&r.call(this,t)&&!isNaN(+t.slice(1))&&(this[t]=void 0)},stop:function stop(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function dispatchException(e){if(this.done)throw e;var t=this;function handle(r,n){return i.type="throw",i.arg=e,t.next=r,n&&(t.method="next",t.arg=void 0),!!n}for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n],i=o.completion;if("root"===o.tryLoc)return handle("end");if(o.tryLoc<=this.prev){var a=r.call(o,"catchLoc"),u=r.call(o,"finallyLoc");if(a&&u){if(this.prev<o.catchLoc)return handle(o.catchLoc,!0);if(this.prev<o.finallyLoc)return handle(o.finallyLoc)}else if(a){if(this.prev<o.catchLoc)return handle(o.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<o.finallyLoc)return handle(o.finallyLoc)}}}},abrupt:function abrupt(e,t){for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n];if(o.tryLoc<=this.prev&&r.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===e||"continue"===e)&&i.tryLoc<=t&&t<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=e,a.arg=t,i?(this.method="next",this.next=i.finallyLoc,c):this.complete(a)},complete:function complete(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),c},finish:function finish(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.finallyLoc===e)return this.complete(r.completion,r.afterLoc),resetTryEntry(r),c}},catch:function _catch(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.tryLoc===e){var n=r.completion;if("throw"===n.type){var o=n.arg;resetTryEntry(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function delegateYield(e,t,r){return this.delegate={iterator:values(e),resultName:t,nextLoc:r},"next"===this.method&&(this.arg=void 0),c}},e}function asyncGeneratorStep(e,t,r,n,o,i,a){try{var u=e[i](a),c=u.value}catch(e){return void r(e)}u.done?t(c):Promise.resolve(c).then(n,o)}function _asyncToGenerator(e){return function(){var t=this,r=arguments;return new Promise((function(n,o){var i=e.apply(t,r);function _next(e){asyncGeneratorStep(i,n,o,_next,_throw,"next",e)}function _throw(e){asyncGeneratorStep(i,n,o,_next,_throw,"throw",e)}_next(void 0)}))}}r.d(t,{Z:()=>o});var n=function(){var e=_asyncToGenerator(_regeneratorRuntime().mark((function _callee2(){var e,t,r,n=arguments;return _regeneratorRuntime().wrap((function _callee2$(o){for(;;)switch(o.prev=o.next){case 0:return e=n.length>0&&void 0!==n[0]?n[0]:"",t=n.length>1&&void 0!==n[1]?n[1]:{},o.next=4,fetch(e,{method:"POST",body:JSON.stringify(t)});case 4:return r=o.sent,o.abrupt("return",r.json());case 6:case"end":return o.stop()}}),_callee2)})));return function POST(){return e.apply(this,arguments)}}();const o=new(function(){function AiImageGenerator(){!function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,AiImageGenerator)}return function _createClass(e,t,r){return t&&_defineProperties(e.prototype,t),r&&_defineProperties(e,r),Object.defineProperty(e,"prototype",{writable:!1}),e}(AiImageGenerator,[{key:"generateAiImageByContext",value:function generateAiImageByContext(e,t,r,o,i){var a=Routing.generate("ai_image_by_element_context",e);t(),n(a,e).then((function(e){!0===e.success?r(e):o(e)})).finally((function(){i()}))}},{key:"upscaleImage",value:function upscaleImage(e,t,r,o,i){var a=Routing.generate("ai_image_upscale",e);t(),n(a,e).then((function(e){!0===e.success?r(e):o(e)})).finally((function(){i()}))}},{key:"varyImage",value:function varyImage(e,t,r,o,i){var a=Routing.generate("ai_image_vary",e);t(),n(a,e).then((function(e){!0===e.success?r(e):o(e)})).finally((function(){i()}))}},{key:"inpaintBackground",value:function inpaintBackground(e,t,r,o,i){var a=Routing.generate("ai_image_inpaint_background",e);t(),n(a,e).then((function(e){!0===e.success?r(e):o(e)})).finally((function(){i()}))}}]),AiImageGenerator}())},778:(e,r,n)=>{n.d(r,{Z:()=>c});var o=new Ext.data.Store({fields:["key","value"],data:[{key:"16:9",value:"16:9"},{key:"4:3",value:"4:3"},{key:"3:2",value:"3:2"},{key:"16:10",value:"16:10"},{key:"5:4",value:"5:4"},{key:"1:1",value:"1:1"},{key:"21:9",value:"21:9"}]}),i=n(63),a=n(695),u=n(844);function _typeof(e){return _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},_typeof(e)}function _toConsumableArray(e){return function _arrayWithoutHoles(e){if(Array.isArray(e))return _arrayLikeToArray(e)}(e)||function _iterableToArray(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function _unsupportedIterableToArray(e,t){if(!e)return;if("string"==typeof e)return _arrayLikeToArray(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return _arrayLikeToArray(e,t)}(e)||function _nonIterableSpread(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function _defineProperties(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,_toPropertyKey(n.key),n)}}function _defineProperty(e,t,r){return(t=_toPropertyKey(t))in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function _toPropertyKey(e){var t=function _toPrimitive(e,t){if("object"!==_typeof(e)||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var n=r.call(e,t||"default");if("object"!==_typeof(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"===_typeof(t)?t:String(t)}var c=function(){function SimpleText2ImageWindow(e,t){!function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,SimpleText2ImageWindow),_defineProperty(this,"id",void 0),_defineProperty(this,"context",void 0),this.id=e,this.context=t}return function _createClass(e,t,r){return t&&_defineProperties(e.prototype,t),r&&_defineProperties(e,r),Object.defineProperty(e,"prototype",{writable:!1}),e}(SimpleText2ImageWindow,[{key:"getWindow",value:function getWindow(e,r,n){var c,l=[{xtype:"textareafield",itemId:"prompt",name:"prompt",value:null!==(c=window.localStorage.getItem("prompt"))&&void 0!==c?c:"",grow:!0,width:"100%",fieldLabel:t("Prompt"),tooltip:"<i>"+t("Leave empty to create prompt from various properties.")+"</i>"},{xtype:"tbtext",fieldLabel:"",text:"<i>"+t("Leave empty to create prompt automatically.")+"</i>",scale:"small",padding:"0 0 20 105",width:"100%"}];if(a.Z.isAspectRatioSupported(u.Z.TXT2IMG)){var s;o.load();var f=null!==(s=window.localStorage.getItem("aspectRatio"))&&void 0!==s?s:"1:1";l=[{xtype:"combobox",itemId:"aspectRatio",name:"aspectRatio",triggerAction:"all",selectOnFocus:!0,fieldLabel:t("Aspect Ratio"),store:o,value:f,displayField:"key",valueField:"value",width:"100%"}].concat(_toConsumableArray(l))}if(a.Z.isSeedingSupported(u.Z.TXT2IMG)){var p,y=null!==(p=window.localStorage.getItem("seed"))&&void 0!==p?p:-1;l=[].concat(_toConsumableArray(l),[{xtype:"numberfield",name:"seed",value:y,itemId:"seed",width:"100%",fieldLabel:t("Seed")}])}var d=new Ext.Window({title:t("Generate image"),width:400,bodyStyle:"padding: 10px;",closable:!1,plain:!0,items:l,buttons:[{text:t("cancel"),iconCls:"pimcore_icon_cancel",handler:function handler(){d.close()}},{text:t("apply"),iconCls:"pimcore_icon_apply",handler:function(){var o=d.getComponent("prompt").getValue();window.localStorage.setItem("prompt",o);var c={context:this.context,id:this.id,prompt:o};if(a.Z.isAspectRatioSupported(u.Z.TXT2IMG)){var l=d.getComponent("aspectRatio").getValue();window.localStorage.setItem("aspectRatio",l),c.aspectRatio=l}if(a.Z.isSeedingSupported(u.Z.TXT2IMG)){var s=d.getComponent("seed")?d.getComponent("seed").getValue():-1;window.localStorage.setItem("seed",s),c.seed=s}i.Z.generateAiImageByContext(c,(function(){e(),d.close()}),(function(e){r(e)}),(function(e){pimcore.helpers.showNotification(t("error"),e.message,"error")}),(function(){n()}))}.bind(this)}]});return d}}]),SimpleText2ImageWindow}()},844:(e,t,r)=>{r.d(t,{Z:()=>n});const n={TXT2IMG:"txt2img",UPSCALE:"upscale",INPAINT_BACKGROUND:"inpaint_background",IMAGE_VARIATIONS:"image_variations"}},695:(e,t,r)=>{function _typeof(e){return _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},_typeof(e)}function _defineProperties(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,(o=n.key,i=void 0,i=function _toPrimitive(e,t){if("object"!==_typeof(e)||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var n=r.call(e,t||"default");if("object"!==_typeof(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(o,"string"),"symbol"===_typeof(i)?i:String(i)),n)}var o,i}r.d(t,{Z:()=>s});var n=function storage(){return pimcore.settings.AiImageGeneratorBundle||{}};const o=new(function(){function ConfigStorage(){!function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,ConfigStorage)}return function _createClass(e,t,r){return t&&_defineProperties(e.prototype,t),r&&_defineProperties(e,r),Object.defineProperty(e,"prototype",{writable:!1}),e}(ConfigStorage,[{key:"set",value:function set(e,t){n()[e]=t}},{key:"get",value:function get(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;return n()[e]||t}}]),ConfigStorage}()),i="OpenAi",a="DreamStudio",u="StableDiffusion",c="ClipDrop";var l=r(844);function FeatureHelper_typeof(e){return FeatureHelper_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},FeatureHelper_typeof(e)}function FeatureHelper_defineProperties(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,(o=n.key,i=void 0,i=function FeatureHelper_toPrimitive(e,t){if("object"!==FeatureHelper_typeof(e)||null===e)return e;var r=e[Symbol.toPrimitive];if(void 0!==r){var n=r.call(e,t||"default");if("object"!==FeatureHelper_typeof(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(o,"string"),"symbol"===FeatureHelper_typeof(i)?i:String(i)),n)}var o,i}const s=new(function(){function FeatureHelper(){!function FeatureHelper_classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,FeatureHelper)}return function FeatureHelper_createClass(e,t,r){return t&&FeatureHelper_defineProperties(e.prototype,t),r&&FeatureHelper_defineProperties(e,r),Object.defineProperty(e,"prototype",{writable:!1}),e}(FeatureHelper,[{key:"isFeatureEnabled",value:function isFeatureEnabled(e){var t=o.get("adapter",null);switch(t?t[e]:null){case u:switch(e){case l.Z.TXT2IMG:case l.Z.UPSCALE:case l.Z.INPAINT_BACKGROUND:case l.Z.IMAGE_VARIATIONS:return!0;default:return!1}case a:switch(e){case l.Z.TXT2IMG:case l.Z.UPSCALE:case l.Z.IMAGE_VARIATIONS:return!0;default:return!1}case i:switch(e){case l.Z.TXT2IMG:case l.Z.IMAGE_VARIATIONS:return!0;default:return!1}case c:switch(e){case l.Z.TXT2IMG:case l.Z.UPSCALE:case l.Z.INPAINT_BACKGROUND:case l.Z.IMAGE_VARIATIONS:return!0;default:return!1}default:return!1}}},{key:"isSeedingSupported",value:function isSeedingSupported(e){var t=o.get("adapter",null),r=t?t[e]:null;return r===u||r===a}},{key:"isAspectRatioSupported",value:function isAspectRatioSupported(e){var t=o.get("adapter",null),r=t?t[e]:null;return e===l.Z.TXT2IMG&&r!==c}}]),FeatureHelper}())}},e=>{var t;t=366,e(e.s=t)}]);