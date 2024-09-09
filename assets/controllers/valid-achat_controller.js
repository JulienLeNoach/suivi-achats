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

            const pdfDoc = await PDFDocument.load(existingPdfBytes);

            const form = pdfDoc.getForm();

            // Récupérer les champs du PDF
            const numeroEjField = form.getTextField('N EJ');
            const montantHtField = form.getTextField('Montant HT');
            const computationField = form.getTextField('Dernière computation connue');
            const validationBaField = form.getTextField('Validation BA');
            const codeCpvField = form.getTextField('Code CPV');
            const chronoField = form.getTextField('Chrono');
            const notificationField = form.getTextField('Notification');
            const objetField = form.getTextField('OBJET 1'); 
            const validInterField = form.getTextField('undefined_2');
            const comChorField = form.getTextField('Commande CF');
            const uoField = form.getTextField('undefined_4');
            const triField = form.getTextField('ACHETEUR');
            const anneeField = form.getTextField('ANNEE'); // Champ pour l'année
            const serviceField = form.getTextField('SERVICE BENEFICIAIRE'); // Champ pour l'année
            const fournisseurField = form.getTextField('TITULAIRE'); // Champ pour l'année
            const MPPAField = form.getCheckBox('MPPA'); // Le champ case à cocher pour typeMarche
            const MABCField = form.getCheckBox('undefined'); // Le champ case à cocher pour typeMarche

            // Récupérer les valeurs des inputs dans la vue HTML
            const numeroEjValue = document.querySelector('input[name="ej"]').value;
            const montantHtValue = document.getElementById('mtn').value;
            const computationValue = document.querySelector('input[id="comp"]').value;
            const validationBaValue = document.getElementById('valbox').value;
            const codeCpvValue = document.querySelector('input[id="cpv"]').value;
            const chronoValue = document.getElementById('chrono').value.split('-')[1].trim();
            const notificationValue = document.getElementById('notbox').value;
            const objetValue = document.getElementById('objet').value; 
            const validInterValue = document.getElementById('valInt').value; 
            const comChorValue = document.getElementById('dateCho').value; 
            const uoValue = document.getElementById('uo2').value; 
            const triValue = document.getElementById('tri').value;
            const serviceValue = document.getElementById('uo').value; 
            const currentYear = new Date().getFullYear(); // Année en cours
            const fournisseurValue = document.getElementById('four').value;  // Année en cours
            const typeMarcheValue = document.getElementById('typem').value;  // Année en cours

            // Remplir les champs dans le PDF
            numeroEjField.setText(numeroEjValue);
            montantHtField.setText(montantHtValue);
            computationField.setText(computationValue);
            validationBaField.setText(validationBaValue);
            codeCpvField.setText(codeCpvValue);
            chronoField.setText(chronoValue);
            notificationField.setText(notificationValue);
            objetField.setText(objetValue); // Remplir le champ OBJET
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
                  // Cocher la première case
            } else if (typeMarcheValue === '0') {
                MPPAField.check();
                MABCField.uncheck();
                // Cocher la deuxième case (ou laisser décoché)
            }
 
            // Sauvegarder et télécharger le PDF
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
