// import { Controller } from '@hotwired/stimulus';
// import Chart from 'chart.js/auto'
// const ctx = document.getElementById('myChart');
// const ctx2 = document.getElementById('myChart2')
// const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
// export default class extends Controller {
//   static targets = ['myChart'];
//   connect() {
//     this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
//     this.element.addEventListener('chartjs:connect', this._onConnect);
//     new Chart(this.canvasContext(), {
//       type: 'bar',
//       data: {
//         labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
//         datasets: [{
//             label: '# of Votes',
//             data: [12, 19, 3, 5, 2, 3],
//             backgroundColor: [
//                 'rgba(255, 99, 132, 0.2)',
//                 'rgba(54, 162, 235, 0.2)',
//                 'rgba(255, 206, 86, 0.2)',
//                 'rgba(75, 192, 192, 0.2)',
//                 'rgba(153, 102, 255, 0.2)',
//                 'rgba(255, 159, 64, 0.2)'
//             ],
//             borderColor: [
//                 'rgba(255, 99, 132, 1)',
//                 'rgba(54, 162, 235, 1)',
//                 'rgba(255, 206, 86, 1)',
//                 'rgba(75, 192, 192, 1)',
//                 'rgba(153, 102, 255, 1)',
//                 'rgba(255, 159, 64, 1)'
//             ],
//             borderWidth: 1
//         }],
//       options: {
//           responsive: false,
//         scales: {
//           y: {
//             beginAtZero: true
//           }
//         }
//       }
//     }
//     });
// }
// disconnect() {
//   // You should always remove listeners when the controller is disconnected to avoid side effects
//   this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
//   this.element.removeEventListener('chartjs:connect', this._onConnect);
// }
//   canvasContext() {
//       return this.myChartTarget.getContext('2d');
//   }
//   _onPreConnect(event) {
//     // The chart is not yet created
//     // You can access the config that will be passed to "new Chart()"
//     console.log(event.detail.config);
//     // For instance you can format Y axis
//     event.detail.config.options.scales = {
//         y: {
//             ticks: {
//                 callback: function (value, index, values) {
//                     /* ... */
//                 },
//             },
//         },
//     };
// }
// _onConnect(event) {
//     // The chart was just created
//     console.log(event.detail.chart); // You can access the chart instance using the event details
//     // For instance you can listen to additional events
//     event.detail.chart.options.onHover = (mouseEvent) => {
//         /* ... */
//     };
//     event.detail.chart.options.onClick = (mouseEvent) => {
//         /* ... */
//     };
// }
// }
"use strict";