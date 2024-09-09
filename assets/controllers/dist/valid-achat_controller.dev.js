"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

var _pdfLib = require("pdf-lib");

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
    } // Configuration de la validation du champ Numero EJ

  }, {
    key: "setupEjValidation",
    value: function setupEjValidation() {
      var _this = this;

      var ejTarget = this.element.querySelector('input[name="ej"]');
      var submitButton = this.element.querySelector('[data-valid-achat-target="submitButton"]');

      if (ejTarget) {
        ejTarget.addEventListener('input', function () {
          return _this.validateEjLength(ejTarget);
        });
      }

      if (submitButton) {
        submitButton.addEventListener('click', function (event) {
          return _this.validateFormOnSubmit(event, ejTarget);
        });
      }
    } // Fonction de validation de la longueur du champ Numero EJ

  }, {
    key: "validateEjLength",
    value: function validateEjLength(ejTarget) {
      var numeroEj = ejTarget.value;

      if (numeroEj.length > 10) {
        ejTarget.value = numeroEj.slice(0, 10); // Limite la saisie à 10 caractères
      }
    } // Validation avant la soumission du formulaire

  }, {
    key: "validateFormOnSubmit",
    value: function validateFormOnSubmit(event, ejTarget) {
      var numeroEj = ejTarget.value;

      if (numeroEj.length !== 10) {
        alert("Le champ 'Numero EJ' doit contenir exactement 10 caractères.");
        event.preventDefault(); // Empêche la soumission si la longueur n'est pas correcte
      }
    } // Configuration de la soumission du formulaire et génération du PDF

  }, {
    key: "setupFormSubmission",
    value: function setupFormSubmission() {
      var _this2 = this;

      var form = this.element.querySelector('form');
      var submitButton = form.querySelector('[data-valid-achat-target="submitButton"]');
      form.addEventListener('submit', function (event) {
        // event.preventDefault(); // Empêche la soumission immédiate du formulaire
        // Soumettre le formulaire via AJAX
        var formData = new FormData(form);
        fetch(form.action, {
          method: 'POST',
          body: formData
        }).then(function (response) {
          if (response.ok) {
            console.log('Formulaire soumis avec succès');

            _this2.fillPdfWithNumeroEj(); // Générer et télécharger le PDF après la soumission réussie

          } else {
            console.error('Erreur lors de la soumission du formulaire');
          }
        })["catch"](function (error) {
          console.error('Erreur lors de la soumission du formulaire :', error);
        });
      });
    } // Fonction pour générer et télécharger le PDF après la soumission

  }, {
    key: "fillPdfWithNumeroEj",
    value: function fillPdfWithNumeroEj() {
      var url, existingPdfBytes, _PDFLib, _PDFDocument, pdfDoc, form, numeroEjField, pdfBytes, blob, link;

      return regeneratorRuntime.async(function fillPdfWithNumeroEj$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              _context.prev = 0;
              url = '/POCHETTE_2024_vierge.pdf'; // URL de votre fichier PDF

              _context.next = 4;
              return regeneratorRuntime.awrap(fetch(url).then(function (res) {
                return res.arrayBuffer();
              }));

            case 4:
              existingPdfBytes = _context.sent;
              _PDFLib = PDFLib, _PDFDocument = _PDFLib.PDFDocument;
              _context.next = 8;
              return regeneratorRuntime.awrap(_PDFDocument.load(existingPdfBytes));

            case 8:
              pdfDoc = _context.sent;
              form = pdfDoc.getForm();
              numeroEjField = form.getTextField('N EJ'); // Nom exact du champ à remplir dans le PDF

              numeroEjField.setText(document.querySelector('input[name="ej"]').value);
              _context.next = 14;
              return regeneratorRuntime.awrap(pdfDoc.save());

            case 14:
              pdfBytes = _context.sent;
              blob = new Blob([pdfBytes], {
                type: 'application/pdf'
              });
              link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = 'achat_validé_numero_ej.pdf';
              link.click();
              _context.next = 25;
              break;

            case 22:
              _context.prev = 22;
              _context.t0 = _context["catch"](0);
              console.error('Erreur lors de la génération du PDF :', _context.t0);

            case 25:
            case "end":
              return _context.stop();
          }
        }
      }, null, null, [[0, 22]]);
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;