import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        // Récupération des éléments du DOM
        this.dateCommandeChorusTarget = this.element.querySelector('[data-add-achat-target="dateCommandeChorus"]');
        this.dateValidInterTarget = this.element.querySelector('[data-add-achat-target="dateValidInter"]');
        this.submitButtonTarget = this.element.querySelector('[data-add-achat-target="submitButton"]');
        this.montantAchatTarget = this.element.querySelector('[data-add-achat-target="montantAchat"]');
        this.tvaIdentTarget = this.element.querySelector('[data-add-achat-target="tvaIdent"]');
        this.typeMarcheTarget = this.element.querySelector('[data-add-achat-target="typeMarche"]');
        this.numeroMarcheTarget = this.element.querySelector('[data-add-achat-target="numeroMarche"]');
        this.numeroEjMarcheTarget = this.element.querySelector('[data-add-achat-target="numeroEjMarche"]');

        // Attache les écouteurs d'événements
        this.observeOptions();
        this.setupDateValidation();
        this.setupTvaCalculation();
        this.setupTypeMarcheVisibility();
        this.colorizeOptions();
        this.attachEventListeners();
    }

    attachEventListeners() {
        if (this.submitButtonTarget) {
            this.submitButtonTarget.addEventListener('click', this.checkMontantBeforeSubmit.bind(this));
        }

        document.getElementById('confirmValidation').addEventListener('click', this.confirmValidation.bind(this));
        document.getElementById('cancelValidation').addEventListener('click', this.hideValidationModal.bind(this));
        document.getElementById('closeValidationModal').addEventListener('click', this.hideValidationModal.bind(this));
        document.getElementById('nonConcurrenceCheckbox').addEventListener('change', this.toggleJustifNonConcurrenceSelect.bind(this));
    }

    checkMontantBeforeSubmit(event) {
        const montantTtc = this.calculateTva();
        
        // Vérifie la valeur du champ type de marché
        const typeMarcheElement = this.element.querySelector('[name="add_achat[type_marche]"]:checked');
        const typeMarcheValue = typeMarcheElement ? typeMarcheElement.value : null;        
        // Champs obligatoires normaux et autocomplétion
        const requiredFields = [
            { field: this.dateCommandeChorusTarget, name: "Date Commande Chorus" },
            { field: this.dateValidInterTarget, name: "Date Validation Intermédiaire" },
            { field: this.montantAchatTarget, name: "Montant Achat" },
            { field: this.element.querySelector('[name="add_achat[objet_achat]"]'), name: "Objet Achat" },
            { field: this.element.querySelector('[name="add_achat[id_demande_achat]"]'), name: "ID Demande Achat" },
            { field: this.element.querySelector('[name="add_achat[type_marche]"]:checked'), name: "Type de Marché" },
            { field: this.element.querySelector('[name="add_achat[code_service]"]'), name: "Code Service" },
            { field: this.element.querySelector('[name="add_achat[tva_ident]"]'), name: "TVA" },
    
            // Champs d'autocomplétion : vérification avancée
            { field: this.element.querySelector('#add_achat_code_formation_autocomplete-ts-control'), name: "Code Formation", autocomplete: true },
            { field: this.element.querySelector('#add_achat_num_siret_autocomplete-ts-control'), name: "Num SIRET", autocomplete: true },
            { field: this.element.querySelector('#add_achat_code_uo_autocomplete-ts-control'), name: "Unité Organique", autocomplete: true }
        ];
    
        const missingFields = requiredFields
            .filter(item => {
                if (!item.field) return true;
                
                // Pour les champs d'autocomplétion, vérifier la présence de `data-value` dans le parent direct
                if (item.autocomplete) {
                    const selectedItem = item.field.closest(".ts-control").querySelector("[data-value]");
                    return !selectedItem || selectedItem.getAttribute("data-value") === "";
                }
                
                // Pour les autres champs, vérifier la présence de contenu
                return item.field.value.trim() === "";
            })
            .map(item => item.name);
    
        if (missingFields.length === 0) {
            // Vérifie si type_marche est égal à "1" avant d'ouvrir la modale
            if (typeMarcheValue === "1") {
                if (montantTtc > 20000) {
                    event.preventDefault();
                    this.showValidationModal(true);
                } else if (montantTtc < 2000) {
                    event.preventDefault();
                    this.showValidationModal(false);
                }
            } else {
                // Si type_marche n'est pas égal à "1", soumettre le formulaire sans la modale
                this.submitActualForm();
            }
        } else {
            // Afficher une alerte listant les champs manquants
            alert("Veuillez remplir les champs obligatoires suivants avant de valider :\n" + missingFields.join(", "));
            event.preventDefault();
        }
    }
    
    
    
    

    showValidationModal(showTable) {
        const modal = document.getElementById('validationModal');
        const validationSelect = document.getElementById('validationSelect');
        const customInput = document.getElementById('customValidationInput'); // Custom input < 2000 €
        const customInputSup = document.getElementById('customValidationInputSup'); // Custom input > 20000 €
        const justificationTable = document.getElementById('justificationTable');
        const nonConcurrenceContainer = document.getElementById('nonConcurrenceContainer');
        const justifNonConcurrenceSelectContainer = document.getElementById('justifNonConcurrenceSelectContainer');

        if (showTable) {
            // Pour montants > 20 000 €
            validationSelect.style.display = 'none';
            justificationTable.style.display = 'block';
            nonConcurrenceContainer.style.display = 'block';
            customInput.style.display = 'none';
            customInputSup.style.display = 'block';  // Affiche le champ personnalisé pour > 20000 €
        } else {
            // Pour montants < 2 000 €
            validationSelect.style.display = 'block';
            justificationTable.style.display = 'none';
            nonConcurrenceContainer.style.display = 'none';
            justifNonConcurrenceSelectContainer.style.display = 'none';
            customInput.style.display = 'block';  // Affiche le champ personnalisé pour < 2000 €
            customInputSup.style.display = 'none';
        }

        modal.style.display = 'block';
    }

    toggleJustifNonConcurrenceSelect() {
        const justifNonConcurrenceSelectContainer = document.getElementById('justifNonConcurrenceSelectContainer');
        const isChecked = document.getElementById('nonConcurrenceCheckbox').checked;
        justifNonConcurrenceSelectContainer.style.display = isChecked ? 'block' : 'none';
    }

    hideValidationModal() {
        const modal = document.getElementById('validationModal');
        modal.style.display = 'none';
    }

    confirmValidation() {
        const selectedOption = document.getElementById('validationSelect').value;
        const customInput = document.getElementById('customValidationInput').value;
        const customInputSup = document.getElementById('customValidationInputSup').value;
        const justificationTable = document.getElementById('justificationTable');
        const justifNonConcurrenceSelect = document.getElementById('justifNonConcurrenceSelect');
    
        // Récupération et validation des valeurs de la table des devis
        const devisInputs = [
            {
                candidat: document.querySelector('input[name="candidat_devis1"]').value,
                montant: document.querySelector('input[name="montantht_devis1"]').value,
                observation: document.querySelector('input[name="observation_devis1"]').value
            },
            {
                candidat: document.querySelector('input[name="candidat_devis2"]').value,
                montant: document.querySelector('input[name="montantht_devis2"]').value,
                observation: document.querySelector('input[name="observation_devis2"]').value
            },
            {
                candidat: document.querySelector('input[name="candidat_devis3"]').value,
                montant: document.querySelector('input[name="montantht_devis3"]').value,
                observation: document.querySelector('input[name="observation_devis3"]').value
            }
        ];
    console.log(devisInputs);
        // Fonction de validation pour les champs de devis
        const validateDevisFields = (candidat, montant, observation) => {
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
        };
    
        // Vérifie si la table des devis est visible (montant > 20 000 €)
        if (justificationTable.style.display === 'block') {
            let validDevis = false;
    
            // Vérifie les devis et leurs champs pour la validation
            for (let i = 0; i < devisInputs.length; i++) {
                const { candidat, montant, observation } = devisInputs[i];
    
                if (candidat || montant || observation) {
                    if (!validateDevisFields(candidat, montant, observation)) {
                        return;
                    }
                    validDevis = true;
                }
            }
    
            if (
                !validDevis &&
                !justifNonConcurrenceSelect.value &&
                !customInputSup
            ) {
                alert("Veuillez sélectionner une justification ou saisir une option avant de valider.");
                return;
            }
    
            const form = this.element.querySelector('form');
    
            if (justifNonConcurrenceSelect && justifNonConcurrenceSelect.value) {
                const justifNonConcurrenceInput = document.createElement('input');
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
                const customJustifSupInput = document.createElement('input');
                customJustifSupInput.type = 'hidden';
                customJustifSupInput.name = 'custom_justif_sup';
                customJustifSupInput.value = customInputSup;
                form.appendChild(customJustifSupInput);
            }
    
            // Ajoute les valeurs de la table des devis
            devisInputs.forEach((devis, index) => {
                if (devis.candidat || devis.montant || devis.observation) {
                    ['candidat', 'montantht', 'observation'].forEach(field => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `devis[${index + 1}][${field}]`;
                        input.value = devis[field === 'candidat' ? 'candidat' : field === 'montantht' ? 'montant' : 'observation'];
                        form.appendChild(input);
                    });
                }
            });
    
            this.hideValidationModal();
            this.submitActualForm();
            return;
        } else if (selectedOption || customInput) {
            // Logique pour les montants < 2 000 €
            const justifIdInput = document.createElement('input');
            justifIdInput.type = 'hidden';
            justifIdInput.name = 'justif_id';
            justifIdInput.value = selectedOption ? selectedOption : "new";
            this.element.querySelector('form').appendChild(justifIdInput);
    
            if (customInput) {
                if (customInput.length > 250) {
                    alert("La justification personnalisée pour montants inférieurs à 2 000 € ne doit pas dépasser 250 caractères.");
                    return;
                }
                const customJustifInput = document.createElement('input');
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

    submitActualForm() {
        this.element.querySelector('form').submit();
    }

    colorizeOptions() {
        function colorizeOptions() {
            // Sélectionner tous les div avec le rôle option pour vérifier s'ils ont atteint le premier seuil
            const allDivs = document.querySelectorAll('div[role="option"]');

            allDivs.forEach((div) => {
                const textContent = div.textContent || div.innerText;
                // Si le texte contient "Premier seuil atteint", coloriser en orange
                if (textContent.includes('Premier seuil atteint')) {
                    div.style.color = 'orange'; // Coloriser en orange les éléments ayant atteint le premier seuil
                } else if (textContent.includes('Utilisation du CPV impossible')) {
                    div.style.color = 'red';
                }
            });
        }

        // Observer les mutations dans le DOM pour détecter les changements
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    colorizeOptions(); // Appeler la fonction lorsque des éléments sont ajoutés
                }
            });
        });

        // Configurer l'observation du body pour suivre les changements dans l'arborescence DOM
        const config = { childList: true, subtree: true };
        observer.observe(document.body, config);

        // Appel initial pour coloriser les éléments déjà présents
        colorizeOptions();
    }

    setupTvaCalculation() {
        if (this.montantAchatTarget) {
            this.montantAchatTarget.addEventListener('input', this.calculateTva.bind(this));
        }

        if (this.tvaIdentTarget) {
            this.tvaIdentTarget.addEventListener('change', this.calculateTva.bind(this));
        }

        // Calcul initial si une option est pré-sélectionnée
        this.calculateTva();
    }

    calculateTva() {
        const montantAchat = parseFloat(this.montantAchatTarget.value) || 0;
        const selectedTvaOption = this.tvaIdentTarget.selectedOptions[0];
        const tvaText = selectedTvaOption ? selectedTvaOption.textContent : '';
        const tvaPercentageMatch = tvaText.match(/(\d+\.?\d*)/);
        const tvaPercentage = tvaPercentageMatch ? parseFloat(tvaPercentageMatch[0]) : 0;

        // Calcul du montant TTC
        const montantTtc = montantAchat + (montantAchat * tvaPercentage / 100);
        document.getElementById('montant-tcc').innerText = ` / ${montantTtc.toFixed(2)} TTC`;

        // Retourne le montant TTC pour l'utiliser ailleurs
        return montantTtc;
    }

    observeOptions() {
        const selectContainer = document.querySelector('#add_achat_code_cpv_autocomplete');

        if (selectContainer) {
            // this.disableInvalidOptions(); // Appel initial pour désactiver les options

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        // this.disableInvalidOptions();
                    }
                });
            });

            const config = { childList: true, subtree: true };
            observer.observe(document.body, config); // Observer les changements dans le body

            // this.disableInvalidOptions();
        }
    }

    setupDateValidation() {
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

    validateDates() {
        const dateCommandeChorus = this.dateCommandeChorusTarget.value;
        const dateValidInter = this.dateValidInterTarget.value;

        if (dateCommandeChorus) {
            if (dateValidInter && dateCommandeChorus > dateValidInter) {
                alert("La date de création CF ne peut pas être postérieure à la date de dernier validateur.");
                this.dateCommandeChorusTarget.value = "";
            }
        }
    }

    validateFormOnSubmit(event) {
        const dateCommandeChorus = this.dateCommandeChorusTarget.value;
        const dateValidInter = this.dateValidInterTarget.value;

        if ((dateValidInter && dateCommandeChorus > dateValidInter)) {
            alert("Veuillez corriger les dates avant de soumettre le formulaire.");
            event.preventDefault();
        }
    }

    setupTypeMarcheVisibility() {
        if (this.typeMarcheTarget) {
            this.typeMarcheTarget.querySelectorAll('input[type="radio"]').forEach((radio) => {
                radio.addEventListener('change', this.toggleMarcheFieldsVisibility.bind(this));
            });

            this.toggleMarcheFieldsVisibility(); // Vérification initiale
        }
    }

    toggleMarcheFieldsVisibility() {
        const selectedRadio = this.typeMarcheTarget.querySelector('input[type="radio"]:checked');
    
        if (selectedRadio) {
            const selectedValue = selectedRadio.value;
    
            if (selectedValue === '0') { // Si 'MABC' est sélectionné
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
}
