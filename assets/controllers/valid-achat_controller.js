import { Controller } from '@hotwired/stimulus';
import { PDFDocument } from 'pdf-lib'; 

export default class extends Controller {
    connect() {
        this.setupEjValidation();
        this.setupFormSubmission();
        this.setupPdfGeneration(); // Configuration de la génération de PDF via le bouton
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

    // Configuration de la génération du PDF via le bouton
    setupPdfGeneration() {
        const generatePdfBtn = document.getElementById('generatePdfBtn');

        if (generatePdfBtn) {
            generatePdfBtn.addEventListener('click', () => {
                this.fillPdfWithNumeroEj();
            });
        }
    }

    // Fonction pour générer et télécharger le PDF après la soumission
    async fillPdfWithNumeroEj() {
        try {
            const url = '/POCHETTE_2024_vierge.pdf'; // URL de votre fichier PDF
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());

            const pdfDoc = await PDFDocument.load(existingPdfBytes);
            const form = pdfDoc.getForm();

            // Récupérer les champs du PDF
            const numeroEjField = form.getTextField('N EJ_2');
            const numMarcheField = form.getTextField('N marché');
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
            const tri2Field = form.getTextField('Acheteur');
            const anneeField = form.getTextField('ANNEE'); 
            const serviceField = form.getTextField('SERVICE BENEFICIAIRE'); 
            const fournisseurField = form.getTextField('TITULAIRE'); 
            const MPPAField = form.getCheckBox('MPPA'); 
            const MABCField = form.getCheckBox('undefined'); 

            // Récupérer les valeurs des inputs dans la vue HTML
            let numeroEjValue = document.querySelector('input[id="ej2"]').value;
            let numMarcheValue = document.getElementById('numM').value;

            // Si la valeur est vide ou nulle, on assigne 'Néant'
            if (!numeroEjValue || numeroEjValue.trim() === '') {
                numeroEjValue = 'Néant';
            }

            if (!numMarcheValue || numMarcheValue.trim() === '') {
                numMarcheValue = 'Néant';
            }

            const montantHtValue = parseFloat(document.getElementById('mtn').value); // Convertir en nombre
            const tvaIdentValue = document.getElementById('tva').value;
            
            // Vérifier si les valeurs sont valides pour le calcul
            let montantTTC;
            const tvaNumeric = parseFloat(tvaIdentValue);
            console.log(tvaIdentValue);
            if (isNaN(montantHtValue)) {
                montantTTC = 'Erreur montant HT'; // Gérer les cas où le montant HT n'est pas un nombre valide
            } else if (tvaIdentValue.toLowerCase() === 'exonéré') {
                montantTTC = montantHtValue.toFixed(2) + ' TTC'; // Si exonéré, pas de calcul
            } else if (!isNaN(tvaNumeric)) {
                montantTTC = (montantHtValue * (1 + tvaNumeric / 100)).toFixed(2) + ' TTC';
            } else {
                montantTTC = 'Erreur TVA'; // Gérer les cas où la TVA n'est pas valide
            }

            const computationValue = document.querySelector('input[id="comp"]').value;
            const validationBaValue = document.getElementById('valbox').value;
            // Récupérer la valeur du champ CPV, conserver tout jusqu'au dernier tiret
            const rawCodeCpvValue = document.querySelector('input[id="cpv"]').value;
            const lastDashIndex = rawCodeCpvValue.lastIndexOf(' - '); // Trouver l'index du dernier tiret
            const processedCodeCpvValue = (lastDashIndex !== -1) ? rawCodeCpvValue.slice(0, lastDashIndex) : rawCodeCpvValue; // Supprimer tout ce qui est après le dernier tiret
            const chronoValue = document.getElementById('chrono').value;
            const notificationValue = document.getElementById('notbox').value;
            const objetValue = document.getElementById('objet').value; 
            const validInterValue = document.getElementById('valInt').value; 
            const comChorValue = document.getElementById('dateCho').value; 
            const rawUoValue = document.querySelector('input[id="uo2"]').value;
            const lastDashIndexUo = rawUoValue.lastIndexOf(' - '); // Trouver l'index du dernier tiret
            const processedUoValue = (lastDashIndexUo !== -1) ? rawUoValue.slice(0, lastDashIndexUo) : rawUoValue; // Supprimer tout ce qui est après le dernier tiret 
            const triValue = document.getElementById('tri').value;
            const serviceValue = document.getElementById('uo').value; 
            const currentYear = new Date().getFullYear();
            const fournisseurValue = document.getElementById('four').value;  
            const typeMarcheValue = document.getElementById('typem').value;  

            // Remplir les champs dans le PDF
            numeroEjField.setText(numeroEjValue);
            numeroEjField.setFontSize(12);
            numMarcheField.setText(numMarcheValue);
            numMarcheField.setFontSize(12);
            montantHtField.setText(montantHtValue.toFixed(2) + ' / ' + montantTTC); // Ajout du montant TTC
            computationField.setText(computationValue);
            validationBaField.setText(validationBaValue);
            codeCpvField.setText(processedCodeCpvValue);
            chronoField.setText(chronoValue);
            chronoField.setFontSize(12);
            notificationField.setText(notificationValue);
            objetField.setText(objetValue); 
            objetField.setFontSize(12);
            validInterField.setText(validInterValue);
            comChorField.setText(comChorValue);
            uoField.setText(processedUoValue);
            triField.setText(triValue);
            tri2Field.setText(triValue);
            anneeField.setText(currentYear.toString()); 
            serviceField.setText(serviceValue);
            fournisseurField.setText(fournisseurValue);

            if (typeMarcheValue === '1') {
                MABCField.check();
                MPPAField.uncheck();
            } else if (typeMarcheValue === '0') {
                MPPAField.check();
                MABCField.uncheck();
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
