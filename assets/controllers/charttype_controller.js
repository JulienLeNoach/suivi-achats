import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'

const ctxMppa = document.getElementById('mppaMountChart');
const ctxMabc = document.getElementById('mabcMountChart');
const ctxallMount = document.getElementById('allMountChart');

export default class extends Controller {

    connect(){
        console.log(parameter0)
          new Chart(ctxMppa, {
            type: 'pie',
            data: {
              labels: [
                'X <=' + parameter0,
                parameter0+' < X <='+ parameter1,
                parameter1 + '< X <='+ parameter2,
                parameter2+' < X'
              ],
              datasets: [{
                label: 'Montant des MPPA',
                data: [result_achats_mounts[0]["nombre_achats_inf_four1"],
                result_achats_mounts[0]["nombre_achats_four1_four2"],
                result_achats_mounts[0]["nombre_achats_four2_four3"],
                result_achats_mounts[0]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(68 196 201)',
                  'rgb(128, 174, 190)',
                  'rgb(238 222 182)'
                ],
                hoverOffset: 4,
                datalabels: {
                  formatter: (value, context) => {
                      return ((value/ result_achats[0]["nombre_achats_type_1"] )* 100).toFixed(1) + '%';
                  },
                  color: 'black',  // Couleur du texte du pourcentage
                  align: 'start',   // Alignement du texte
                  offset: -10      // Décalage du texte par rapport au point
              }

              }],
              
            options: {
                responsive: true,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              },

            }
          }
          });
          new Chart(ctxMabc, {
            type: 'pie',
            data: {
              labels: [
                'X <=' + parameter0,
                parameter0+' < X <='+ parameter1,
                parameter1 + '< X <='+ parameter2,
                parameter2+' < X'
              ],
              datasets: [{
                label: 'Montant des MABC',
                data: [result_achats_mounts[1]["nombre_achats_inf_four1"],
                result_achats_mounts[1]["nombre_achats_four1_four2"],
                result_achats_mounts[1]["nombre_achats_four2_four3"],
                result_achats_mounts[1]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(68 196 201)',
                  'rgb(128, 174, 190)',
                  'rgb(238 222 182)'
                ],
                hoverOffset: 4,
                datalabels: {
                  formatter: (value, context) => {
                      return ((value/result_achats[1]["nombre_achats_type_0"])* 100).toFixed(1) + '%';
                  },
                  color: 'black',  // Couleur du texte du pourcentage
                  align: 'start',   // Alignement du texte
                  offset: -10      // Décalage du texte par rapport au point
              }
              }],
            options: {
                responsive: true,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              },

            }
          }
          });
          new Chart(ctxallMount, {
            type: 'pie',
            data: {
              labels: [
                'X <=' + parameter0,
                parameter0+' < X <='+ parameter1,
                parameter1 + '< X <='+ parameter2,
                parameter2+' < X'
              ],
              datasets: [{
                label: 'Montant des MPPA + MABC',
                data: [result_achats_mounts[1]["nombre_achats_inf_four1"]+result_achats_mounts[0]["nombre_achats_inf_four1"],
                result_achats_mounts[1]["nombre_achats_four1_four2"]+result_achats_mounts[0]["nombre_achats_four1_four2"],
                result_achats_mounts[1]["nombre_achats_four2_four3"]+result_achats_mounts[0]["nombre_achats_four2_four3"],
                result_achats_mounts[1]["nombre_achats_sup_four3"]+result_achats_mounts[0]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(68 196 201)',
                  'rgb(128, 174, 190)',
                  'rgb(238 222 182)'
                ],
                hoverOffset: 4,
                datalabels: {
                  formatter: (value, context) => {
                      return ((value/ (result_achats[0]["nombre_achats_type_1"] + result_achats[1]["nombre_achats_type_0"])  )* 100).toFixed(1) + '%';
                  },
                  color: 'black',  // Couleur du texte du pourcentage
                  align: 'start',   // Alignement du texte
                  offset: -10      // Décalage du texte par rapport au point
              }
              }],
            options: {
                responsive: true,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              },
            }
          }
          });
    }

}