// import { Controller } from '@hotwired/stimulus';
// export default class extends Controller {
//     static values = {
//         nextPage: Number,
//         isLoading: Boolean,
//         hasMore: Boolean
//     }
//     connect() {
//         this.nextPageValue = 2; // Commence à la page 2 car la page 1 est déjà chargée
//         this.isLoadingValue = false;
//         this.hasMoreValue = true; // Supposer qu'il y a plus de données à charger initialement
//         window.addEventListener('scroll', this.handleScroll.bind(this));
//     }
//     handleScroll() {
//         const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
//         if (scrollTop + clientHeight >= scrollHeight - 10 && !this.isLoadingValue && this.hasMoreValue) {
//             this.loadMoreData();
//         }
//     }
//     async loadMoreData() {
//         this.isLoadingValue = true;
//         try {
//             const response = await fetch(`/search?page=${this.nextPageValue}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
//             if (response.ok) {
//                 const html = await response.text();
//                 console.log(html);
//                 document.getElementById('achat-list-tbody').insertAdjacentHTML('beforeend', html);
//                 this.nextPageValue++;
//             } else {
//                 console.error('Erreur réseau');
//                 this.hasMoreValue = false; // Arrête de charger si une erreur se produit
//             }
//         } catch (error) {
//             console.error('Erreur : ' + error);
//             this.hasMoreValue = false;
//         } finally {
//             this.isLoadingValue = false;
//         }
//     }
// }
"use strict";