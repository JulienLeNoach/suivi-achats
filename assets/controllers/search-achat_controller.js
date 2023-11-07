import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.attachEventListeners();
    }
    
    attachEventListeners() {
        var rows = document.querySelectorAll('.clickable-row');
        var btnElements = document.querySelectorAll('#btn');
        
        // Parcours de toutes les lignes et ajout d'un événement "click"
        rows.forEach(function (row) {
            row.addEventListener('click', function () {
                console.log(rows);

                // Suppression de la classe "selected" de toutes les autres lignes
                rows.forEach(function (otherRow) {
                    otherRow.classList.remove('selected');
                });
                // Ajout de la classe "selected" à la ligne sélectionnée
                row.classList.add('selected');
                btnElements.forEach(function (btn) {
                    btn.classList.remove('hidden');
                    btn.addEventListener('click', function () {
                        var link = btn.getAttribute('data-link');
                        var detailLink = document.getElementById('detail');
                        var id = row.getAttribute('data-id');
                        detailLink.setAttribute('href', '/' + link + '/' + id);
                    });
                });
            });
        });
    }

}