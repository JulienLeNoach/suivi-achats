// import Chart from 'chart.js/auto';
// function createBarChart(ctx, labels, datasets) {
//   new Chart(ctx, {
//     type: 'bar',
//     data: {
//       labels: labels,
//       datasets: datasets
//     },
//     options: {
//         responsive: false,
//       scales: {
//         y: {
//           beginAtZero: true
//         }
//       }
//     }
//   });
// }
// function createLineChart(ctx, labels, datasets) {
//     new Chart(ctx, {
//       type: 'line',
//       data: {
//         labels: labels,
//         datasets: datasets
//       },
//       options: {
//         scales: {
//           y: {
//             beginAtZero: true
//           }
//         }
//       }
//     });
//   }
// const ctx = document.getElementById('myChart')
// const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
// const datasets = [
//   {
//     label: 'MPPA',
//     data: datasets1, // Remplacez ces valeurs par celles de votre premier dataset
//     borderWidth: 1
//   },
//   {
//     label: 'MABC',
//     data: datasets2, // Remplacez ces valeurs par celles de votre deuxième dataset
//     borderWidth: 1
//   }
// ];
// createBarChart(ctx, labels, datasets);
// const ctx2 = document.getElementById('myChart2').getContext('2d');
// const labels2 = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
// const datasetsB = [
//   {
//     label: 'MPPA',
//     data: datasets3, // Remplacez ces valeurs par celles de votre premier dataset
//     borderWidth: 1
//   },
//   {
//     label: 'MABC',
//     data: datasets4, // Remplacez ces valeurs par celles de votre deuxième dataset
//     borderWidth: 1
//   }
// ];
// createBarChart(ctx2, labels2, datasetsB);
// // window.onload = createBarChart;
"use strict";