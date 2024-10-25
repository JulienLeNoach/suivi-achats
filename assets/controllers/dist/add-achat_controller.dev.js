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
      // Récupération des éléments du DOM
      this.dateCommandeChorusTarget = this.element.querySelector('[data-add-achat-target="dateCommandeChorus"]');
      this.dateValidInterTarget = this.element.querySelector('[data-add-achat-target="dateValidInter"]');
      this.submitButtonTarget = this.element.querySelector('[data-add-achat-target="submitButton"]');
      this.montantAchatTarget = this.element.querySelector('[data-add-achat-target="montantAchat"]');
      this.tvaIdentTarget = this.element.querySelector('[data-add-achat-target="tvaIdent"]');
      this.typeMarcheTarget = this.element.querySelector('[data-add-achat-target="typeMarche"]');
      this.numeroMarcheTarget = this.element.querySelector('[data-add-achat-target="numeroMarche"]');
      this.numeroEjMarcheTarget = this.element.querySelector('[data-add-achat-target="numeroEjMarche"]'); // Attache les écouteurs d'événements

      this.observeOptions();
      this.setupDateValidation();
      this.setupTvaCalculation();
      this.setupTypeMarcheVisibility();
      this.colorizeOptions();
      this.attachEventListeners();
    }
  }, {
    key: "attachEventListeners",
    value: function attachEventListeners() {
      if (this.submitButtonTarget) {
        this.submitButtonTarget.addEventListener('click', this.checkMontantBeforeSubmit.bind(this));
      } // Écouteurs pour la modale


      document.getElementById('confirmValidation').addEventListener('click', this.confirmValidation.bind(this));
      document.getElementById('cancelValidation').addEventListener('click', this.hideValidationModal.bind(this));
      document.getElementById('closeValidationModal').addEventListener('click', this.hideValidationModal.bind(this));
    }
  }, {
    key: "checkMontantBeforeSubmit",
    value: function checkMontantBeforeSubmit(event) {
      // Calculer le montant TTC avant de vérifier
      var montantTtc = this.calculateTva();
      console.log(montantTtc); // Vérifier si le montant TTC est inférieur à 2000 €

      if (montantTtc < 2000) {
        event.preventDefault(); // Empêche la soumission du formulaire

        this.showValidationModal(); // Affiche la modale
      }
    }
  }, {
    key: "showValidationModal",
    value: function showValidationModal() {
      var modal = document.getElementById('validationModal');
      modal.style.display = 'block';
    }
  }, {
    key: "hideValidationModal",
    value: function hideValidationModal() {
      var modal = document.getElementById('validationModal');
      modal.style.display = 'none';
    }
  }, {
    key: "confirmValidation",
    value: function confirmValidation() {
      var selectedOption = document.getElementById('validationSelect').value;
      var customInput = document.getElementById('customValidationInput').value;

      if (selectedOption || customInput) {
        var justifIdInput = document.createElement('input');
        justifIdInput.type = 'hidden';
        justifIdInput.name = 'justif_id';
        justifIdInput.value = selectedOption ? selectedOption : "new"; // Marque si c'est un nouveau justificatif

        this.element.querySelector('form').appendChild(justifIdInput);

        if (customInput) {
          var customJustifInput = document.createElement('input');
          customJustifInput.type = 'hidden';
          customJustifInput.name = 'custom_justif';
          customJustifInput.value = customInput;
          this.element.querySelector('form').appendChild(customJustifInput);
        }

        this.hideValidationModal();
        this.submitActualForm();
      } else {
        alert("Veuillez sélectionner ou entrer une option avant de valider.");
      }
    }
  }, {
    key: "submitActualForm",
    value: function submitActualForm() {
      // Code pour soumettre le formulaire, par exemple avec une requête AJAX ou un submit traditionnel
      this.element.querySelector('form').submit();
    }
  }, {
    key: "colorizeOptions",
    value: function colorizeOptions() {
      function colorizeOptions() {
        // Sélectionner tous les div avec le rôle option pour vérifier s'ils ont atteint le premier seuil
        var allDivs = document.querySelectorAll('div[role="option"]');
        allDivs.forEach(function (div) {
          var textContent = div.textContent || div.innerText; // Si le texte contient "Premier seuil atteint", coloriser en orange

          if (textContent.includes('Premier seuil atteint')) {
            div.style.color = 'orange'; // Coloriser en orange les éléments ayant atteint le premier seuil
          } else if (textContent.includes('Utilisation du CPV impossible')) {
            div.style.color = 'red';
          }
        });
      } // Observer les mutations dans le DOM pour détecter les changements


      var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
            colorizeOptions(); // Appeler la fonction lorsque des éléments sont ajoutés
          }
        });
      }); // Configurer l'observation du body pour suivre les changements dans l'arborescence DOM

      var config = {
        childList: true,
        subtree: true
      };
      observer.observe(document.body, config); // Appel initial pour coloriser les éléments déjà présents

      colorizeOptions();
    }
  }, {
    key: "setupTvaCalculation",
    value: function setupTvaCalculation() {
      if (this.montantAchatTarget) {
        this.montantAchatTarget.addEventListener('input', this.calculateTva.bind(this));
      }

      if (this.tvaIdentTarget) {
        this.tvaIdentTarget.addEventListener('change', this.calculateTva.bind(this));
      } // Calcul initial si une option est pré-sélectionnée


      this.calculateTva();
    }
  }, {
    key: "calculateTva",
    value: function calculateTva() {
      var montantAchat = parseFloat(this.montantAchatTarget.value) || 0;
      var selectedTvaOption = this.tvaIdentTarget.selectedOptions[0];
      var tvaText = selectedTvaOption ? selectedTvaOption.textContent : '';
      var tvaPercentageMatch = tvaText.match(/(\d+\.?\d*)/);
      var tvaPercentage = tvaPercentageMatch ? parseFloat(tvaPercentageMatch[0]) : 0; // Calcul du montant TTC

      var montantTtc = montantAchat + montantAchat * tvaPercentage / 100;
      document.getElementById('montant-tcc').innerText = " / ".concat(montantTtc.toFixed(2), " TTC"); // Retourne le montant TTC pour l'utiliser ailleurs

      return montantTtc;
    }
  }, {
    key: "observeOptions",
    value: function observeOptions() {
      var _this = this;

      var selectContainer = document.querySelector('#add_achat_code_cpv_autocomplete');

      if (selectContainer) {
        this.disableInvalidOptions(); // Appel initial pour désactiver les options

        var observer = new MutationObserver(function (mutations) {
          mutations.forEach(function (mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
              _this.disableInvalidOptions();
            }
          });
        });
        var config = {
          childList: true,
          subtree: true
        };
        observer.observe(document.body, config); // Observer les changements dans le body

        this.disableInvalidOptions();
      }
    }
  }, {
    key: "setupDateValidation",
    value: function setupDateValidation() {
      if (this.dateCommandeChorusTarget) {
        this.dateCommandeChorusTarget.addEventListener('change', this.validateDates.bind(this));
      }

      if (this.dateValidInterTarget) {
        this.dateValidInterTarget.addEventListener('change', this.validateDates.bind(this));
      }

      if (this.submitButtonTarget) {
        this.submitButtonTarget.addEventListener('click', this.validateFormOnSubmit.bind(this));
      }
    }
  }, {
    key: "validateDates",
    value: function validateDates() {
      var dateCommandeChorus = this.dateCommandeChorusTarget.value;
      var dateValidInter = this.dateValidInterTarget.value;

      if (dateCommandeChorus) {
        if (dateValidInter && dateCommandeChorus > dateValidInter) {
          alert("La date de création CF ne peut pas être postérieure à la date de dernier validateur.");
          this.dateCommandeChorusTarget.value = "";
        }
      }
    }
  }, {
    key: "validateFormOnSubmit",
    value: function validateFormOnSubmit(event) {
      var dateCommandeChorus = this.dateCommandeChorusTarget.value;
      var dateValidInter = this.dateValidInterTarget.value;

      if (dateValidInter && dateCommandeChorus > dateValidInter) {
        alert("Veuillez corriger les dates avant de soumettre le formulaire.");
        event.preventDefault();
      }
    }
  }, {
    key: "setupTypeMarcheVisibility",
    value: function setupTypeMarcheVisibility() {
      var _this2 = this;

      if (this.typeMarcheTarget) {
        this.typeMarcheTarget.querySelectorAll('input[type="radio"]').forEach(function (radio) {
          radio.addEventListener('change', _this2.toggleMarcheFieldsVisibility.bind(_this2));
        });
        this.toggleMarcheFieldsVisibility(); // Vérification initiale
      }
    }
  }, {
    key: "toggleMarcheFieldsVisibility",
    value: function toggleMarcheFieldsVisibility() {
      var selectedRadio = this.typeMarcheTarget.querySelector('input[type="radio"]:checked');

      if (selectedRadio) {
        var selectedValue = selectedRadio.value;

        if (selectedValue === '0') {
          // Si 'MABC' est sélectionné
          this.numeroMarcheTarget.closest('.form-group').classList.remove('hidden');
          this.numeroEjMarcheTarget.closest('.form-group').classList.remove('hidden');
        } else {
          this.numeroMarcheTarget.closest('.form-group').classList.add('hidden');
          this.numeroEjMarcheTarget.closest('.form-group').classList.add('hidden');
        }
      } else {
        // Aucune option n'est cochée, masquer les champs par défaut
        this.numeroMarcheTarget.closest('.form-group').classList.add('hidden');
        this.numeroEjMarcheTarget.closest('.form-group').classList.add('hidden');
      }
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;