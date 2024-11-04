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
      }

      document.getElementById('confirmValidation').addEventListener('click', this.confirmValidation.bind(this));
      document.getElementById('cancelValidation').addEventListener('click', this.hideValidationModal.bind(this));
      document.getElementById('closeValidationModal').addEventListener('click', this.hideValidationModal.bind(this));
      document.getElementById('nonConcurrenceCheckbox').addEventListener('change', this.toggleJustifNonConcurrenceSelect.bind(this));
    }
  }, {
    key: "checkMontantBeforeSubmit",
    value: function checkMontantBeforeSubmit(event) {
      var montantTtc = this.calculateTva(); // Champs obligatoires normaux et autocomplétion

      var requiredFields = [{
        field: this.dateCommandeChorusTarget,
        name: "Date Commande Chorus"
      }, {
        field: this.dateValidInterTarget,
        name: "Date Validation Intermédiaire"
      }, {
        field: this.montantAchatTarget,
        name: "Montant Achat"
      }, {
        field: this.element.querySelector('[name="add_achat[objet_achat]"]'),
        name: "Objet Achat"
      }, {
        field: this.element.querySelector('[name="add_achat[id_demande_achat]"]'),
        name: "ID Demande Achat"
      }, {
        field: this.element.querySelector('[name="add_achat[type_marche]"]:checked'),
        name: "Type de Marché"
      }, {
        field: this.element.querySelector('[name="add_achat[code_service]"]'),
        name: "Code Service"
      }, {
        field: this.element.querySelector('[name="add_achat[tva_ident]"]'),
        name: "TVA"
      }, // Champs d'autocomplétion : vérification avancée
      {
        field: this.element.querySelector('#add_achat_code_formation_autocomplete-ts-control'),
        name: "Code Formation",
        autocomplete: true
      }, {
        field: this.element.querySelector('#add_achat_num_siret_autocomplete-ts-control'),
        name: "Num SIRET",
        autocomplete: true
      }, {
        field: this.element.querySelector('#add_achat_code_uo_autocomplete-ts-control'),
        name: "Unité Organique",
        autocomplete: true
      }];
      var missingFields = requiredFields.filter(function (item) {
        if (!item.field) return true; // Pour les champs d'autocomplétion, vérifier la présence de `data-value` dans le parent direct

        if (item.autocomplete) {
          var selectedItem = item.field.closest(".ts-control").querySelector("[data-value]");
          return !selectedItem || selectedItem.getAttribute("data-value") === "";
        } // Pour les autres champs, vérifier la présence de contenu


        return item.field.value.trim() === "";
      }).map(function (item) {
        return item.name;
      });

      if (missingFields.length === 0) {
        // Tous les champs sont remplis
        if (montantTtc > 20000) {
          event.preventDefault();
          this.showValidationModal(true);
        } else if (montantTtc < 2000) {
          event.preventDefault();
          this.showValidationModal(false);
        }
      } else {
        // Afficher une alerte listant les champs manquants
        alert("Veuillez remplir les champs obligatoires suivants avant de valider :\n" + missingFields.join(", "));
        event.preventDefault();
      }
    }
  }, {
    key: "showValidationModal",
    value: function showValidationModal(showTable) {
      var modal = document.getElementById('validationModal');
      var validationSelect = document.getElementById('validationSelect');
      var customInput = document.getElementById('customValidationInput'); // Custom input < 2000 €

      var customInputSup = document.getElementById('customValidationInputSup'); // Custom input > 20000 €

      var justificationTable = document.getElementById('justificationTable');
      var nonConcurrenceContainer = document.getElementById('nonConcurrenceContainer');
      var justifNonConcurrenceSelectContainer = document.getElementById('justifNonConcurrenceSelectContainer');

      if (showTable) {
        // Pour montants > 20 000 €
        validationSelect.style.display = 'none';
        justificationTable.style.display = 'block';
        nonConcurrenceContainer.style.display = 'block';
        customInput.style.display = 'none';
        customInputSup.style.display = 'block'; // Affiche le champ personnalisé pour > 20000 €
      } else {
        // Pour montants < 2 000 €
        validationSelect.style.display = 'block';
        justificationTable.style.display = 'none';
        nonConcurrenceContainer.style.display = 'none';
        justifNonConcurrenceSelectContainer.style.display = 'none';
        customInput.style.display = 'block'; // Affiche le champ personnalisé pour < 2000 €

        customInputSup.style.display = 'none';
      }

      modal.style.display = 'block';
    }
  }, {
    key: "toggleJustifNonConcurrenceSelect",
    value: function toggleJustifNonConcurrenceSelect() {
      var justifNonConcurrenceSelectContainer = document.getElementById('justifNonConcurrenceSelectContainer');
      var isChecked = document.getElementById('nonConcurrenceCheckbox').checked;
      justifNonConcurrenceSelectContainer.style.display = isChecked ? 'block' : 'none';
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
      var customInputSup = document.getElementById('customValidationInputSup').value;
      var justificationTable = document.getElementById('justificationTable');
      var justifNonConcurrenceSelect = document.getElementById('justifNonConcurrenceSelect'); // Récupération et validation des valeurs de la table des devis

      var devisInputs = [{
        candidat: document.querySelector('input[name="candidat_devis1"]').value,
        montant: document.querySelector('input[name="montant_ht_devis1"]').value,
        observation: document.querySelector('input[name="observation_devis1"]').value
      }, {
        candidat: document.querySelector('input[name="candidat_devis2"]').value,
        montant: document.querySelector('input[name="montant_ht_devis2"]').value,
        observation: document.querySelector('input[name="observation_devis2"]').value
      }, {
        candidat: document.querySelector('input[name="candidat_devis3"]').value,
        montant: document.querySelector('input[name="montant_ht_devis3"]').value,
        observation: document.querySelector('input[name="observation_devis3"]').value
      }]; // Fonction de validation pour les champs de devis

      var validateDevisFields = function validateDevisFields(candidat, montant, observation) {
        if (candidat && candidat.length > 150) {
          alert("Le nom du candidat ne doit pas dépasser 150 caractères.");
          return false;
        }

        if (montant && !/^\d+(\.\d{1,2})?$/.test(montant)) {
          alert("Le montant HT doit être un nombre valide avec jusqu'à deux décimales.");
          return false;
        }

        if (observation && observation.length > 250) {
          alert("L'observation ne doit pas dépasser 250 caractères.");
          return false;
        }

        return true;
      }; // Vérifie si la table des devis est visible (montant > 20 000 €)


      if (justificationTable.style.display === 'block') {
        var validDevis = false; // Vérifie les devis et leurs champs pour la validation

        for (var i = 0; i < devisInputs.length; i++) {
          var _devisInputs$i = devisInputs[i],
              candidat = _devisInputs$i.candidat,
              montant = _devisInputs$i.montant,
              observation = _devisInputs$i.observation;

          if (candidat || montant || observation) {
            if (!validateDevisFields(candidat, montant, observation)) {
              return;
            }

            validDevis = true;
          }
        }

        if (!validDevis && !justifNonConcurrenceSelect.value && !customInputSup) {
          alert("Veuillez sélectionner une justification ou saisir une option avant de valider.");
          return;
        }

        var form = this.element.querySelector('form');

        if (justifNonConcurrenceSelect && justifNonConcurrenceSelect.value) {
          var justifNonConcurrenceInput = document.createElement('input');
          justifNonConcurrenceInput.type = 'hidden';
          justifNonConcurrenceInput.name = 'justif_non_concurrence';
          justifNonConcurrenceInput.value = justifNonConcurrenceSelect.value;
          form.appendChild(justifNonConcurrenceInput);
        }

        if (customInputSup) {
          if (customInputSup.length > 250) {
            alert("La justification personnalisée pour montants supérieurs à 20 000 € ne doit pas dépasser 250 caractères.");
            return;
          }

          var customJustifSupInput = document.createElement('input');
          customJustifSupInput.type = 'hidden';
          customJustifSupInput.name = 'custom_justif_sup';
          customJustifSupInput.value = customInputSup;
          form.appendChild(customJustifSupInput);
        } // Ajoute les valeurs de la table des devis


        devisInputs.forEach(function (devis, index) {
          if (devis.candidat || devis.montant || devis.observation) {
            ['candidat', 'montant_ht', 'observation'].forEach(function (field) {
              var input = document.createElement('input');
              input.type = 'hidden';
              input.name = "devis[".concat(index + 1, "][").concat(field, "]");
              input.value = devis[field === 'candidat' ? 'candidat' : field];
              form.appendChild(input);
            });
          }
        });
        this.hideValidationModal();
        this.submitActualForm();
        return;
      } else if (selectedOption || customInput) {
        // Logique pour les montants < 2 000 €
        var justifIdInput = document.createElement('input');
        justifIdInput.type = 'hidden';
        justifIdInput.name = 'justif_id';
        justifIdInput.value = selectedOption ? selectedOption : "new";
        this.element.querySelector('form').appendChild(justifIdInput);

        if (customInput) {
          if (customInput.length > 250) {
            alert("La justification personnalisée pour montants inférieurs à 2 000 € ne doit pas dépasser 250 caractères.");
            return;
          }

          var customJustifInput = document.createElement('input');
          customJustifInput.type = 'hidden';
          customJustifInput.name = 'custom_justif';
          customJustifInput.value = customInput;
          this.element.querySelector('form').appendChild(customJustifInput);
        }

        this.hideValidationModal();
        this.submitActualForm();
        return;
      }

      alert("Veuillez sélectionner ou entrer une option avant de valider.");
    }
  }, {
    key: "submitActualForm",
    value: function submitActualForm() {
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