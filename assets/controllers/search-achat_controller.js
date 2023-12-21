import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {

        this.attachEventListeners();

    }
    collapse() {

        let formContainer = document.querySelector('.form-container');
        let toggleButton = document.getElementById('toggleFormBtn');
        formContainer.classList.add('collapsed');
        toggleButton.innerHTML = '<span class="fr-icon-arrow-down-fill" aria-hidden="true"></span>';


            toggleButton.addEventListener('click', function() {
                if (formContainer.classList.contains('collapsed')) {
                    formContainer.classList.remove('collapsed');
                    toggleButton.innerHTML = '<span class="fr-icon-arrow-up-fill" aria-hidden="true"></span>';
                } else {
                    formContainer.classList.add('collapsed');
                    toggleButton.innerHTML = '<span class="fr-icon-arrow-down-fill" aria-hidden="true"></span>';
                }
            });
        
    }
    
    attachEventListeners() {
        let table = document.querySelector('table');
        let footer = document.querySelector('footer');
        let noResult = document.querySelector('#noResult');
        // Vérifier si la table existe
        table ? footer.scrollIntoView({ behavior: 'smooth', block: 'nearest' }) : 
        (noResult ? noResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' }) : null);

        let rows = document.querySelectorAll('.clickable-row');
        let btnElements = document.querySelectorAll('#btn');

        rows.forEach(function (row) {
            row.addEventListener('click', function () {
                btnElements.forEach(function (btn) {
                    btn.removeAttribute('disabled');
                });
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });
                let etatCell = row.cells[7];
                let etatAchatText = etatCell.textContent.replace(/\s+/g, '');

                let selectedRow = document.querySelector('.selected');
                        if (etatAchatText =='Validé') {

                            document.querySelectorAll('.valid, .reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                        } else if (etatAchatText == 'Encours') {

                            document.querySelectorAll('.reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                        } else if (etatAchatText = 'Annulé') {

                            document.querySelectorAll('.annul, .valid, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                        }

                row.classList.add('selected');
                btnElements.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        // Récupérer le lien et l'ID
                        let link = btn.getAttribute('data-link');
                        let action = btn.getAttribute('data-action');
                        let id = document.querySelector('.selected').getAttribute('data-id');
                
                        // Construire le message d'alerte
                        // let confirmationMessage = `Voulez-vous vraiment ${action} cet achat?`;
                
                        // Afficher l'alerte et rediriger si l'utilisateur confirme
                        // if (confirm(confirmationMessage)) {
                            console.log("confirm");
                            let detailLink = document.getElementById('detail');
                            detailLink.setAttribute('href', '/' + link + '/' + id);
                            window.location.href = detailLink.getAttribute('href');
                        // }
                    });
                });

            });
        });
    }

}