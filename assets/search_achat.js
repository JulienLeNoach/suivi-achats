import jsPDF from 'jspdf';

// function attachEventListeners() {
//     var rows = document.querySelectorAll('.clickable-row');
//     var btnElements = document.querySelectorAll('#btn');
//     console.log(rows);

//     // Parcours de toutes les lignes et ajout d'un événement "click"
//     rows.forEach(function (row) {
//         row.addEventListener('click', function () {

//             // Suppression de la classe "selected" d    e toutes les autres lignes
//             rows.forEach(function (otherRow) {
//                 otherRow.classList.remove('selected');
//             });
//             // Ajout de la classe "selected" à la ligne sélectionnée
//             row.classList.add('selected');
//             btnElements.forEach(function (btn) {
//                 btn.classList.remove('hidden');
//                 btn.addEventListener('click', function () {
//                     var link = btn.getAttribute('data-link');
//                     var detailLink = document.getElementById('detail');
//                     var id = row.getAttribute('data-id');
//                     detailLink.setAttribute('href', '/' + link + '/' + id);
//                 });
//             });
//         });
//     });
// }
// const backButton = document.getElementById('go-back');
//     backButton.addEventListener('click', function() {
//     window.history.back();
//     });
    

    
// function updatePagination(pageNumber) {
// // Mettez à jour la classe active de la pagination
// var paginationItems = document.querySelectorAll('.pagination li.page-item');

// paginationItems.forEach(function (item) {
//     item.classList.remove('active');
// });

// var activePageItem = document.querySelector('.pagination li.page-item:nth-child(' + (pageNumber+1) + ')');
// console.log(pageNumber);
// if (activePageItem) {
//     activePageItem.classList.add('active');

// }
// } 

// document.addEventListener('DOMContentLoaded', function () {
//     attachEventListeners();

// });