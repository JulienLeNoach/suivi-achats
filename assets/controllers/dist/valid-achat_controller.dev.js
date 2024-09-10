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
      this.setupPdfGeneration(); // Configuration de la génération de PDF via le bouton
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
    } // Configuration de la génération du PDF via le bouton

  }, {
    key: "setupPdfGeneration",
    value: function setupPdfGeneration() {
      var _this3 = this;

      var generatePdfBtn = document.getElementById('generatePdfBtn');

      if (generatePdfBtn) {
        generatePdfBtn.addEventListener('click', function () {
          _this3.fillPdfWithNumeroEj();
        });
      }
    } // Fonction pour générer et télécharger le PDF après la soumission

  }, {
    key: "fillPdfWithNumeroEj",
    value: function fillPdfWithNumeroEj() {
      var url, existingPdfBytes, pdfDoc, form, numeroEjField, numMarcheField, montantHtField, computationField, validationBaField, codeCpvField, chronoField, notificationField, objetField, validInterField, comChorField, uoField, triField, anneeField, serviceField, fournisseurField, MPPAField, MABCField, numeroEjValue, numMarcheValue, montantHtValue, computationValue, validationBaValue, codeCpvValue, chronoValue, notificationValue, objetValue, validInterValue, comChorValue, uoValue, triValue, serviceValue, currentYear, fournisseurValue, typeMarcheValue, pdfBytes, blob, link;
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
              _context.next = 7;
              return regeneratorRuntime.awrap(_pdfLib.PDFDocument.load(existingPdfBytes));

            case 7:
              pdfDoc = _context.sent;
              form = pdfDoc.getForm(); // Récupérer les champs du PDF

              numeroEjField = form.getTextField('N EJ_2');
              numMarcheField = form.getTextField('N marché');
              montantHtField = form.getTextField('Montant HT');
              computationField = form.getTextField('Dernière computation connue');
              validationBaField = form.getTextField('Validation BA');
              codeCpvField = form.getTextField('Code CPV');
              chronoField = form.getTextField('Chrono');
              notificationField = form.getTextField('Notification');
              objetField = form.getTextField('OBJET 1');
              validInterField = form.getTextField('undefined_2');
              comChorField = form.getTextField('Commande CF');
              uoField = form.getTextField('undefined_4');
              triField = form.getTextField('ACHETEUR');
              anneeField = form.getTextField('ANNEE');
              serviceField = form.getTextField('SERVICE BENEFICIAIRE');
              fournisseurField = form.getTextField('TITULAIRE');
              MPPAField = form.getCheckBox('MPPA');
              MABCField = form.getCheckBox('undefined'); // Récupérer les valeurs des inputs dans la vue HTML

              numeroEjValue = document.querySelector('input[id="ej2"]').value;
              numMarcheValue = document.getElementById('numM').value; // Si la valeur est vide ou nulle, on assigne 'Néant'

              if (!numeroEjValue || numeroEjValue.trim() === '') {
                numeroEjValue = 'Néant';
              }

              if (!numMarcheValue || numMarcheValue.trim() === '') {
                numMarcheValue = 'Néant';
              }

              montantHtValue = document.getElementById('mtn').value;
              computationValue = document.querySelector('input[id="comp"]').value;
              validationBaValue = document.getElementById('valbox').value;
              codeCpvValue = document.querySelector('input[id="cpv"]').value;
              chronoValue = document.getElementById('chrono').value.split('-')[1].trim();
              notificationValue = document.getElementById('notbox').value;
              objetValue = document.getElementById('objet').value;
              validInterValue = document.getElementById('valInt').value;
              comChorValue = document.getElementById('dateCho').value;
              uoValue = document.getElementById('uo2').value;
              triValue = document.getElementById('tri').value;
              serviceValue = document.getElementById('uo').value;
              currentYear = new Date().getFullYear();
              fournisseurValue = document.getElementById('four').value;
              typeMarcheValue = document.getElementById('typem').value; // Remplir les champs dans le PDF

              numeroEjField.setText(numeroEjValue);
              numeroEjField.setFontSize(12);
              numMarcheField.setText(numMarcheValue);
              numMarcheField.setFontSize(12);
              montantHtField.setText(montantHtValue);
              computationField.setText(computationValue);
              validationBaField.setText(validationBaValue);
              codeCpvField.setText(codeCpvValue);
              chronoField.setText(chronoValue);
              notificationField.setText(notificationValue);
              objetField.setText(objetValue);
              objetField.setFontSize(12);
              validInterField.setText(validInterValue);
              comChorField.setText(comChorValue);
              uoField.setText(uoValue);
              triField.setText(triValue);
              anneeField.setText(currentYear.toString());
              serviceField.setText(serviceValue);
              fournisseurField.setText(fournisseurValue);

              if (typeMarcheValue === '1') {
                MABCField.check();
                MPPAField.uncheck();
              } else if (typeMarcheValue === '0') {
                MPPAField.check();
                MABCField.uncheck();
              } // Sauvegarder et télécharger le PDF


              _context.next = 68;
              return regeneratorRuntime.awrap(pdfDoc.save());

            case 68:
              pdfBytes = _context.sent;
              blob = new Blob([pdfBytes], {
                type: 'application/pdf'
              });
              link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = 'achat_validé_numero_ej.pdf';
              link.click();
              _context.next = 79;
              break;

            case 76:
              _context.prev = 76;
              _context.t0 = _context["catch"](0);
              console.error('Erreur lors de la génération du PDF :', _context.t0);

            case 79:
            case "end":
              return _context.stop();
          }
        }
      }, null, null, [[0, 76]]);
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;