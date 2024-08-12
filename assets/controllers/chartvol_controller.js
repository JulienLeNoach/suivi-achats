import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
  connect() {
    const ctx = document.getElementById('myChart');
    const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    // Données existantes
    const chartDataCountCurrentMppa = JSON.parse(document.querySelector('input[name="chartDataCountCurrent"]').value).mppa;
    const chartDataCountCurrentMabc = JSON.parse(document.querySelector('input[name="chartDataCountCurrent"]').value).mabc;
    const chartDataTotalCurrentMppa = JSON.parse(document.querySelector('input[name="chartDataTotalCurrent"]').value).mppa;
    const chartDataTotalCurrentMabc = JSON.parse(document.querySelector('input[name="chartDataTotalCurrent"]').value).mabc;

    const chartDataCountPreviousMppa = JSON.parse(document.querySelector('input[name="chartDataCountPrevious"]').value).mppa;
    const chartDataCountPreviousMabc = JSON.parse(document.querySelector('input[name="chartDataCountPrevious"]').value).mabc;
    const chartDataTotalPreviousMppa = JSON.parse(document.querySelector('input[name="chartDataTotalPrevious"]').value).mppa;
    const chartDataTotalPreviousMabc = JSON.parse(document.querySelector('input[name="chartDataTotalPrevious"]').value).mabc;

    // Calcul du tableau cumulatif pour l'année en cours
    const cumulativeTotalsCurrent = [];
    let cumulativeTotalCurrent = 0;
    chartDataTotalCurrentMppa.forEach((valueMppa, index) => {
      const valueMabc = chartDataTotalCurrentMabc[index];
      cumulativeTotalCurrent += valueMppa + valueMabc;
      cumulativeTotalsCurrent.push(parseFloat(cumulativeTotalCurrent.toFixed(2))); // Limiter à deux chiffres après la virgule
    });

    // Calcul du tableau cumulatif pour l'année précédente
    const cumulativeTotalsPrevious = [];
    let cumulativeTotalPrevious = 0;
    chartDataTotalPreviousMppa.forEach((valueMppa, index) => {
      const valueMabc = chartDataTotalPreviousMabc[index];
      cumulativeTotalPrevious += valueMppa + valueMabc;
      cumulativeTotalsPrevious.push(parseFloat(cumulativeTotalPrevious.toFixed(2))); // Limiter à deux chiffres après la virgule
    });

    new Chart(ctx, {
      data: {
        labels: labels,
        datasets: [
          {
            type: 'bar',
            label: 'Volume MPPA (année en cours)',
            data: chartDataCountCurrentMppa,
            yAxisID: 'y1',
            order: 4, // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgb(77, 104, 188)',
            borderColor: 'rgb(77, 104, 188)'
          },
          {
            type: 'bar',
            label: 'Volume MABC (année en cours)',
            data: chartDataCountCurrentMabc,
            yAxisID: 'y1',
            order: 4, // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgb(162, 225, 228)',
            borderColor: 'rgb(162, 225, 228)'
          },
          {
            type: 'bar',
            label: 'Volume MPPA (année précédente)',
            data: chartDataCountPreviousMppa,
            yAxisID: 'y1',
            order: 3, // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgba(77, 104, 188, 0.5)',
            borderColor: 'rgba(77, 104, 188, 0.5)'
          },
          {
            type: 'bar',
            label: 'Volume MABC (année précédente)',
            data: chartDataCountPreviousMabc,
            yAxisID: 'y1',
            order: 3, // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgba(162, 225, 228, 0.5)',
            borderColor: 'rgba(162, 225, 228, 0.5)'
          },
          {
            type: 'line',
            label: 'Total (année en cours)',
            data: cumulativeTotalsCurrent,
            yAxisID: 'y',
            order: 2, // Lignes seront au-dessus
            borderWidth: 2,
            borderColor: 'rgb(54, 74, 137)',
            backgroundColor: 'rgba(54, 74, 137, 0.2)',
            fill: false
          },
          {
            type: 'line',
            label: 'Total (année précédente)',
            data: cumulativeTotalsPrevious,
            yAxisID: 'y',
            order: 1, // Lignes seront au-dessus
            borderWidth: 2,
            borderColor: 'rgba(54, 74, 137, 0.5)',
            backgroundColor: 'rgba(54, 74, 137, 0.2)',
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            position: 'left',
            title: {
              display: true,
              text: 'Total Amount'
            }
          },
          y1: {
            beginAtZero: true,
            position: 'right',
            grid: {
              drawOnChartArea: false
            },
            title: {
              display: true,
              text: 'Count'
            }
          }
        },
        plugins: {
          legend: {
            position: 'top',
            align: 'end',
            labels: {
              boxWidth: 20
            }
          }
        }
      }
    });
  }
}
