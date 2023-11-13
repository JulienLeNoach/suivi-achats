import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'

const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const ctx = document.getElementById('delayChart');

export default class extends Controller {


    connect(){
      // ctx.width = 1200; // Définit la largeur du premier canvas
      // ctx.height = 800; // Définit la hauteur du premier canvas

      console.log(ctx);

      
        new Chart(ctx, {
            
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                  label: 'Transmission',
                  data: transStat,
                  borderWidth: 1
              },
            {
              label: 'Notification',
              data: notStat,
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
        //   new Chart(ctx2, {
        //     type: 'bar',
        //     data: {
        //       labels: labels,
        //       datasets: [{
        //           label: 'MPPA',
        //           data: datasets3,
        //           borderWidth: 1
        //       },
        //     {
        //       label: 'MABC',
        //       data: datasets4,
        //       borderWidth: 1
        //     }],
        //     options: {
        //         responsive: false,
        
        //       scales: {
        //         y: {
        //           beginAtZero: true
        //         }
        //       }
        //     }
        //   }
        //   });
    }


}