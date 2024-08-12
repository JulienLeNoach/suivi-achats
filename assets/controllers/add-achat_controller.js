import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "dateCommandeChorus", "dateValidInter", "submitButton", "montantAchat", "tvaIdent"];

    connect() {
        this.observeOptions();
        this.setupDateValidation();
        this.setupTvaCalculation();
    }
    setupTvaCalculation() {
        if (this.hasMontantAchatTarget) {
            this.montantAchatTarget.addEventListener('input', this.calculateTva.bind(this));
        }

        if (this.hasTvaIdentTarget) {
            this.tvaIdentTarget.addEventListener('change', this.calculateTva.bind(this));
        }

        // Initial calculation if there is a preselected option
        this.calculateTva();
    }

    calculateTva() {
        const montantAchat = parseFloat(this.montantAchatTarget.value) || 0;
        const selectedTvaOption = this.tvaIdentTarget.selectedOptions[0];
        const tvaText = selectedTvaOption ? selectedTvaOption.textContent : '';
        const tvaPercentageMatch = tvaText.match(/(\d+\.?\d*)/);
        const tvaPercentage = tvaPercentageMatch ? parseFloat(tvaPercentageMatch[0]) : 0;
        const montantTtc = montantAchat + (montantAchat * tvaPercentage / 100);

        document.getElementById('montant-tcc').innerText = ` / ${montantTtc.toFixed(2)} TTC`;
    }

    observeOptions() {
        const selectContainer = document.querySelector('#add_achat_code_cpv_autocomplete');

        if (selectContainer) {
            this.disableInvalidOptions(); // Initial call to disable options

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        this.disableInvalidOptions();
                    }
                });
            });

            const config = { childList: true, subtree: true };
            observer.observe(document.body, config); // Observe changes in the body

            this.disableInvalidOptions();
        }
    }

    disableInvalidOptions() {
        const options = document.querySelectorAll('div[data-selectable][role="option"]');
        options.forEach((option) => {
            const textContent = option.textContent || option.innerText;
            if (textContent.includes('Utilisation du CPV concerné impossible')) {
                option.setAttribute('aria-disabled', 'true');
                option.style.pointerEvents = 'none'; // Prevent selection
                option.style.backgroundColor = '#f0f0f0'; // Indicate disabled option
                option.style.color = 'gray'; // Indicate disabled option
            }
        });
    }

    setupDateValidation() {

        if (this.hasDateCommandeChorusTarget) {
            this.dateCommandeChorusTarget.addEventListener('change', this.validateDates.bind(this));
        }
        if (this.hasDateValidInterTarget) {
            this.dateValidInterTarget.addEventListener('change', this.validateDates.bind(this));
        }
        if (this.hasSubmitButtonTarget) {
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
}
