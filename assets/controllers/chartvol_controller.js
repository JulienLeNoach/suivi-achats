import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'

const ctx = document.getElementById('myChart');
const ctx2 = document.getElementById('myChart2')
const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

export default class extends Controller {

    connect(){

      ctx.width = 1200; // Définit la largeur du premier canvas
      ctx.height = 800; // Définit la hauteur du premier canvas
  
      ctx2.width = 1200; // Définit la largeur du deuxième canvas
      ctx2.height = 800;
        new Chart(ctx, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                  label: 'MPPA',
                  data: datasets1,
                  borderWidth: 1
              },
            {
              label: 'MABC',
              data: datasets2,
              borderWidth: 1
            }],
            options: {
                responsive: false,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          }
          });
          new Chart(ctx2, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                  label: 'MPPA',
                  data: datasets3,
                  borderWidth: 1
              },
            {
              label: 'MABC',
              data: datasets4,
              borderWidth: 1
            }],
            options: {
                responsive: false,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          }
          });
    }


}