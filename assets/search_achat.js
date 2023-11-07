// Recherchez tous les éléments avec la classe "clickable-row"
var rows = document.querySelectorAll('.clickable-row');
console.log(rows)
// Recherchez tous les éléments avec l'ID "btn"
var btnElements = document.querySelectorAll('#btn');

// Parcours de toutes les lignes et ajout d'un gestionnaire d'événement "click"
rows.forEach(function (row) {
    row.addEventListener('click', function () {

        // Suppression de la classe "selected" de toutes les autres lignes
        rows.forEach(function (otherRow) {
            otherRow.classList.remove('selected');
        });

        // Ajout de la classe "selected" à la ligne sélectionnée
        row.classList.add('selected');

        // Parcours de tous les boutons et ajout d'un gestionnaire d'événement "click" à chacun
        btnElements.forEach(function (btn) {
            btn.classList.remove('hidden');
            btn.addEventListener('click', function () {
                // Obtenez l'attribut "data-link" du bouton
                var link = btn.getAttribute('data-link');

                // Recherchez l'élément avec l'ID "detail"
                var detailLink = document.getElementById('detail');

                // Obtenez l'attribut "data-id" de la ligne
                var id = row.getAttribute('data-id');

                // Modifiez l'attribut "href" de l'élément "detailLink"
                detailLink.setAttribute('href', '/' + link + '/' + id);
            });
        });
    });
});