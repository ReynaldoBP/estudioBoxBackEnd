(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["common"],{

/***/ "./src/app/_pipes/PipeCliente.ts":
/*!***************************************!*\
  !*** ./src/app/_pipes/PipeCliente.ts ***!
  \***************************************/
/*! exports provided: FilterCliente, PipeClienteTotalGenero */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "FilterCliente", function() { return FilterCliente; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PipeClienteTotalGenero", function() { return PipeClienteTotalGenero; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var FilterCliente = /** @class */ (function () {
    function FilterCliente() {
    }
    FilterCliente.prototype.transform = function (items, buscar) {
        if (!items || !buscar) {
            return items;
        }
        return items.filter(function (item) {
            return item['NOMBRE'].toUpperCase().includes(buscar.toUpperCase()) ||
                item['CORREO'].toUpperCase().includes(buscar.toUpperCase());
        });
    };
    FilterCliente = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Pipe"])({
            name: 'filterCliente',
            pure: false
        })
    ], FilterCliente);
    return FilterCliente;
}());

var PipeClienteTotalGenero = /** @class */ (function () {
    function PipeClienteTotalGenero() {
    }
    PipeClienteTotalGenero.prototype.transform = function (items, genero) {
        if (!items || !genero) {
            return items;
        }
        return items.filter(function (item) { return item.intGenero == genero; })
            .map(function (element) { return Number.parseInt(element.intCantidad); })
            .reduce(function (prev, current) {
            return (prev + current);
        }, 0);
    };
    PipeClienteTotalGenero = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Pipe"])({
            name: 'totalClienteGenero',
            pure: false
        })
    ], PipeClienteTotalGenero);
    return PipeClienteTotalGenero;
}());



/***/ }),

/***/ "./src/app/_pipes/SafePipe.ts":
/*!************************************!*\
  !*** ./src/app/_pipes/SafePipe.ts ***!
  \************************************/
/*! exports provided: SafePipe */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SafePipe", function() { return SafePipe; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var _angular_platform_browser__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @angular/platform-browser */ "./node_modules/@angular/platform-browser/fesm5/platform-browser.js");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (undefined && undefined.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};


var SafePipe = /** @class */ (function () {
    function SafePipe(sanitizer) {
        this.sanitizer = sanitizer;
    }
    SafePipe.prototype.transform = function (url) {
        return this.sanitizer.bypassSecurityTrustResourceUrl(url);
    };
    SafePipe = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Pipe"])({ name: 'sanitizeUrl' }),
        __metadata("design:paramtypes", [_angular_platform_browser__WEBPACK_IMPORTED_MODULE_1__["DomSanitizer"]])
    ], SafePipe);
    return SafePipe;
}());



/***/ }),

/***/ "./src/app/_pipes/SharingModule.ts":
/*!*****************************************!*\
  !*** ./src/app/_pipes/SharingModule.ts ***!
  \*****************************************/
/*! exports provided: SharingModule */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SharingModule", function() { return SharingModule; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var _SafePipe__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SafePipe */ "./src/app/_pipes/SafePipe.ts");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};


var SharingModule = /** @class */ (function () {
    function SharingModule() {
    }
    SharingModule_1 = SharingModule;
    SharingModule.forRoot = function () {
        return {
            ngModule: SharingModule_1,
            providers: [Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["forwardRef"])(function () { return _SafePipe__WEBPACK_IMPORTED_MODULE_1__["SafePipe"]; })]
        };
    };
    var SharingModule_1;
    SharingModule = SharingModule_1 = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["NgModule"])({
            declarations: [
                _SafePipe__WEBPACK_IMPORTED_MODULE_1__["SafePipe"]
            ],
            exports: [
                _SafePipe__WEBPACK_IMPORTED_MODULE_1__["SafePipe"]
            ]
        })
    ], SharingModule);
    return SharingModule;
}());



/***/ })

}]);
//# sourceMappingURL=common.js.map