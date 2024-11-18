import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.setupEjValidation();
        this.setupFormSubmission();
    }

    setupEjValidation() {
        const ejTarget = this.element.querySelector('input[name="ej"]');
        if (ejTarget) {
            ejTarget.addEventListener('input', () => this.limitEjLength(ejTarget));
        }
    }

    setupFormSubmission() {
        const form = this.element.querySelector('#valid-form');
        const submitButton = this.element.querySelector('[data-valid-achat-target="submitButton"]');

        if (form && submitButton) {
            submitButton.addEventListener('click', (event) => {
                event.preventDefault(); // Empêche la soumission automatique
                if (this.validateFormOnSubmit()) { // Exécute les vérifications
                    form.submit();

                    setTimeout(() => {
                        window.location.href = '/search';
                    }, 3000);
                }
            });
        }
    }

    validateFormOnSubmit() {
        const ejTarget = this.element.querySelector('input[name="ej"]');
        const dateValidation = this.element.querySelector('input[name="val"]');
        const dateNotification = this.element.querySelector('input[name="not"]');

        let isValid = true;
        let errorMessage = '';

        // Vérifie le champ EJ
        if (ejTarget && ejTarget.value.length !== 10) {
            errorMessage += "Le champ 'Numero EJ' doit contenir exactement 10 caractères.\n";
            isValid = false;
        }

        // Vérifie le champ de date de validation
        if (dateValidation && !dateValidation.value) {
            errorMessage += "Le champ 'Date de validation' est requis.\n";
            isValid = false;
        }

        // Vérifie le champ de date de notification
        if (dateNotification && !dateNotification.value) {
            errorMessage += "Le champ 'Date de notification' est requis.\n";
            isValid = false;
        }

        // Affiche un message d'erreur unique si nécessaire
        if (!isValid) {
            alert(errorMessage.trim());
        }

        return isValid;
    }

    limitEjLength(ejTarget) {
        if (ejTarget.value.length > 10) {
            ejTarget.value = ejTarget.value.slice(0, 10); // Limite la saisie à 10 caractères
        }
    }
}
