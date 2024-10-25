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

        // Écouteurs pour la modale
        document.getElementById('confirmValidation').addEventListener('click', this.confirmValidation.bind(this));
        document.getElementById('cancelValidation').addEventListener('click', this.hideValidationModal.bind(this));
        document.getElementById('closeValidationModal').addEventListener('click', this.hideValidationModal.bind(this));
    }

    checkMontantBeforeSubmit(event) {
        // Calculer le montant TTC avant de vérifier
        const montantTtc = this.calculateTva();
        console.log(montantTtc);
        // Vérifier si le montant TTC est inférieur à 2000 €
        if (montantTtc < 2000) {
            event.preventDefault(); // Empêche la soumission du formulaire
            this.showValidationModal(); // Affiche la modale
        }
    }

    showValidationModal() {
        const modal = document.getElementById('validationModal');
        modal.style.display = 'block';
    }

    hideValidationModal() {
        const modal = document.getElementById('validationModal');
        modal.style.display = 'none';
    }

    confirmValidation() {
        const selectedOption = document.getElementById('validationSelect').value;
        const customInput = document.getElementById('customValidationInput').value;
    
        if (selectedOption || customInput) {
            const justifIdInput = document.createElement('input');
            justifIdInput.type = 'hidden';
            justifIdInput.name = 'justif_id';
            justifIdInput.value = selectedOption ? selectedOption : "new"; // Marque si c'est un nouveau justificatif
            this.element.querySelector('form').appendChild(justifIdInput);
    
            if (customInput) {
                const customJustifInput = document.createElement('input');
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
    
    
    

    submitActualForm() {
        // Code pour soumettre le formulaire, par exemple avec une requête AJAX ou un submit traditionnel
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
            this.disableInvalidOptions(); // Appel initial pour désactiver les options

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        this.disableInvalidOptions();
                    }
                });
            });

            const config = { childList: true, subtree: true };
            observer.observe(document.body, config); // Observer les changements dans le body

            this.disableInvalidOptions();
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
