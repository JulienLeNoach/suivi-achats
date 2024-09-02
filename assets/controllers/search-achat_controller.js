// search-achat_controller.js
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

        // Garder la logique existante intacte
        table ? footer.scrollIntoView({ behavior: 'smooth', block: 'nearest' }) : 
        (noResult ? noResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' }) : null);

        let rows = document.querySelectorAll('.clickable-row');
        let btnElements = document.querySelectorAll('#btn');

        rows.forEach((row) => {
            row.addEventListener('click', function () {
                btnElements.forEach(function (btn) {
                    btn.removeAttribute('disabled');
                });
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });

                let etatCell = row.cells[7];
                let etatAchatText = etatCell.textContent.replace(/\s+/g, '');

                if (etatAchatText === 'Validé') {
                    document.querySelectorAll('.valid, .reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Encours') {
                    document.querySelectorAll('.reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                } else if (etatAchatText === 'Annulé') {
                    document.querySelectorAll('.annul, .valid, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                }

                row.classList.add('selected');
            });
        });

        // Ajouter l'écouteur d'événement pour les boutons, y compris l'alerte de confirmation
        this.setupAlertForAnnulButton(btnElements);
    }

    setupAlertForAnnulButton(btnElements) {
        btnElements.forEach((btn) => {
            // Vérifier si l'écouteur d'événement a déjà été ajouté
            if (!btn.hasListener) {
                btn.addEventListener('click', (event) => {
                    // Vérifier si le bouton cliqué est le bouton "Annuler un achat"
                    if (btn.getAttribute('data-action') === 'annuler') {
                        if (!confirm('Voulez-vous vraiment annuler cet achat?')) {
                            event.preventDefault(); // Annuler l'action si l'utilisateur ne confirme pas
                            return;
                        }
                    }

                    // Exécuter l'action si l'utilisateur confirme
                    let link = btn.getAttribute('data-link');
                    let id = document.querySelector('.selected').getAttribute('data-id');

                    let detailLink = document.getElementById('detail');
                    detailLink.setAttribute('href', '/' + link + '/' + id);
                    window.location.href = detailLink.getAttribute('href');
                });

                // Marquer le bouton comme ayant déjà un écouteur d'événement
                btn.hasListener = true;
            }
        });
    }
}
