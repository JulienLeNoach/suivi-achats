import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {

        this.attachEventListeners();

    }
    collapse() {

        var formContainer = document.querySelector('.form-container');
        var toggleButton = document.getElementById('toggleFormBtn');
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

        var rows = document.querySelectorAll('.clickable-row');
        var btnElements = document.querySelectorAll('#btn');

        rows.forEach(function (row) {
            row.addEventListener('click', function () {
                btnElements.forEach(function (btn) {
                    btn.removeAttribute('disabled');
                });
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });
                var etatCell = row.cells[7];
                var etatAchatText = etatCell.textContent.replace(/\s+/g, '');

                    var selectedRow = document.querySelector('.selected');
                        if (etatAchatText =='Validé') {

                            document.querySelectorAll('.valid, .reint, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                        } else if (etatAchatText == 'Encours') {

                            document.querySelectorAll('.reint').forEach(el => el.setAttribute('disabled', 'disabled'));
                        } else if (etatAchatText = 'Annulé') {

                            document.querySelectorAll('.annul, .valid, .edit').forEach(el => el.setAttribute('disabled', 'disabled'));
                        }

                row.classList.add('selected');
                btnElements.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        var link = btn.getAttribute('data-link');
                        var detailLink = document.getElementById('detail');
                        var id = document.querySelector('.selected').getAttribute('data-id');
                        detailLink.setAttribute('href', '/' + link + '/' + id);
        

                    });
                });

            });
        });
    }

}