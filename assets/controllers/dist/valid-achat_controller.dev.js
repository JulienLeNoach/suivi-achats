"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var _default =
/*#__PURE__*/
function (_Controller) {
  _inherits(_default, _Controller);

  function _default() {
    _classCallCheck(this, _default);

    return _possibleConstructorReturn(this, _getPrototypeOf(_default).apply(this, arguments));
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      this.setupEjValidation();
      this.setupFormSubmission();
    }
  }, {
    key: "setupEjValidation",
    value: function setupEjValidation() {
      var _this = this;

      var ejTarget = this.element.querySelector('input[name="ej"]');

      if (ejTarget) {
        ejTarget.addEventListener('input', function () {
          return _this.limitEjLength(ejTarget);
        });
      }
    }
  }, {
    key: "setupFormSubmission",
    value: function setupFormSubmission() {
      var _this2 = this;

      var form = this.element.querySelector('#valid-form');
      var submitButton = this.element.querySelector('[data-valid-achat-target="submitButton"]');

      if (form && submitButton) {
        submitButton.addEventListener('click', function (event) {
          event.preventDefault(); // Empêche la soumission automatique

          if (_this2.validateFormOnSubmit()) {
            // Exécute les vérifications
            form.submit();
            setTimeout(function () {
              window.location.href = '/search';
            }, 3000);
          }
        });
      }
    }
  }, {
    key: "validateFormOnSubmit",
    value: function validateFormOnSubmit() {
      var ejTarget = this.element.querySelector('input[name="ej"]');
      var dateValidation = this.element.querySelector('input[name="val"]');
      var dateNotification = this.element.querySelector('input[name="not"]');
      var isValid = true;
      var errorMessage = ''; // Vérifie le champ EJ

      if (ejTarget && ejTarget.value.length !== 10) {
        errorMessage += "Le champ 'Numero EJ' doit contenir exactement 10 caractères.\n";
        isValid = false;
      } // Vérifie le champ de date de validation


      if (dateValidation && !dateValidation.value) {
        errorMessage += "Le champ 'Date de validation' est requis.\n";
        isValid = false;
      } // Vérifie le champ de date de notification


      if (dateNotification && !dateNotification.value) {
        errorMessage += "Le champ 'Date de notification' est requis.\n";
        isValid = false;
      } // Affiche un message d'erreur unique si nécessaire


      if (!isValid) {
        alert(errorMessage.trim());
      }

      return isValid;
    }
  }, {
    key: "limitEjLength",
    value: function limitEjLength(ejTarget) {
      if (ejTarget.value.length > 10) {
        ejTarget.value = ejTarget.value.slice(0, 10); // Limite la saisie à 10 caractères
      }
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;