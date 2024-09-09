// valid-achat_controller.js
import { Controller } from '@hotwired/stimulus';
import { PDFDocument } from 'pdf-lib'; 

export default class extends Controller {
    connect() {
        this.setupEjValidation();
        this.setupFormSubmission();
    }

    // Configuration de la validation du champ Numero EJ
    setupEjValidation() {
        const ejTarget = this.element.querySelector('input[name="ej"]');
        const submitButton = this.element.querySelector('[data-valid-achat-target="submitButton"]');

        if (ejTarget) {
            ejTarget.addEventListener('input', () => this.validateEjLength(ejTarget));
        }

        if (submitButton) {
            submitButton.addEventListener('click', (event) => this.validateFormOnSubmit(event, ejTarget));
        }
    }

    // Fonction de validation de la longueur du champ Numero EJ
    validateEjLength(ejTarget) {
        const numeroEj = ejTarget.value;
        if (numeroEj.length > 10) {
            ejTarget.value = numeroEj.slice(0, 10); // Limite la saisie à 10 caractères
        }
    }

    // Validation avant la soumission du formulaire
    validateFormOnSubmit(event, ejTarget) {
        const numeroEj = ejTarget.value;

        if (numeroEj.length !== 10) {
            alert("Le champ 'Numero EJ' doit contenir exactement 10 caractères.");
            event.preventDefault(); // Empêche la soumission si la longueur n'est pas correcte
        }
    }

    // Configuration de la soumission du formulaire et génération du PDF
    setupFormSubmission() {
        const form = this.element.querySelector('form');
        const submitButton = form.querySelector('[data-valid-achat-target="submitButton"]');

        form.addEventListener('submit', (event) => {
            // event.preventDefault(); // Empêche la soumission immédiate du formulaire

            // Soumettre le formulaire via AJAX
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    console.log('Formulaire soumis avec succès');
                    this.fillPdfWithNumeroEj();  // Générer et télécharger le PDF après la soumission réussie
                } else {
                    console.error('Erreur lors de la soumission du formulaire');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la soumission du formulaire :', error);
            });
        });
    }

    // Fonction pour générer et télécharger le PDF après la soumission
    async fillPdfWithNumeroEj() {
        try {
            const url = '/POCHETTE_2024_vierge.pdf'; // URL de votre fichier PDF
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());

            const { PDFDocument } = PDFLib;
            const pdfDoc = await PDFDocument.load(existingPdfBytes);

            const form = pdfDoc.getForm();
            const numeroEjField = form.getTextField('N EJ'); // Nom exact du champ à remplir dans le PDF

            numeroEjField.setText(document.querySelector('input[name="ej"]').value);

            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'achat_validé_numero_ej.pdf';
            link.click();

        } catch (error) {
            console.error('Erreur lors de la génération du PDF :', error);
        }
    }
}
