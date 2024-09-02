// valid-achat_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.setupEjValidation();
    }

    setupEjValidation() {
        // Récupérer les cibles des éléments
        const ejTarget = this.element.querySelector('input[name="ej"]');
        const submitButton = this.element.querySelector('[data-valid-achat-target="submitButton"]');

        if (ejTarget) {
            ejTarget.addEventListener('input', () => this.validateEjLength(ejTarget));
        }

        if (submitButton) {
            submitButton.addEventListener('click', (event) => this.validateFormOnSubmit(event, ejTarget));
        }
    }

    validateEjLength(ejTarget) {
        const numeroEj = ejTarget.value;
        if (numeroEj.length > 10) {
            ejTarget.value = numeroEj.slice(0, 10); // Limite la saisie à 10 caractères
        }
    }

    validateFormOnSubmit(event, ejTarget) {
        const numeroEj = ejTarget.value;

        // Vérification de la longueur du numéro EJ
        if (numeroEj.length !== 10) {
            alert("Le champ 'Numero EJ' doit contenir exactement 10 caractères.");
            event.preventDefault(); // Empêche la soumission du formulaire si la longueur n'est pas correcte
        }
    }
}
