import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'

const ctxMppa = document.getElementById('mppaMountChart');
const ctxMabc = document.getElementById('mabcMountChart');
const ctxallMount = document.getElementById('allMountChart');

export default class extends Controller {

    connect(){
        console.log(parameter1)
          new Chart(ctxMppa, {
            type: 'pie',
            data: {
              labels: [
                'X <=' + parameter1,
                parameter1+' < X <='+ parameter2,
                parameter2 + '< X <='+ parameter3,
                parameter3+' < X'
              ],
              datasets: [{
                label: 'Montant des MPPA',
                data: [result_achats_mounts[0]["nombre_achats_inf_four1"],
                result_achats_mounts[0]["nombre_achats_four1_four2"],
                result_achats_mounts[0]["nombre_achats_four2_four3"],
                result_achats_mounts[0]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(0,99,203)',
                  'rgb(188,205,255)',
                  'rgb(184,254,201)',
                  'rgb(206,97,74)'
                ],
                hoverOffset: 4
              }],
            options: {
                responsive: false,
        
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
                'X <=' + parameter1,
                parameter1+' < X <='+ parameter2,
                parameter2 + '< X <='+ parameter3,
                parameter3+' < X'
              ],
              datasets: [{
                label: 'Montant des MABC',
                data: [result_achats_mounts[1]["nombre_achats_inf_four1"],
                result_achats_mounts[1]["nombre_achats_four1_four2"],
                result_achats_mounts[1]["nombre_achats_four2_four3"],
                result_achats_mounts[1]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(0,99,203)',
                  'rgb(188,205,255)',
                  'rgb(184,254,201)',
                  'rgb(206,97,74)'
                ],
                hoverOffset: 4
              }],
            options: {
                responsive: false,
        
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
                'X <=' + parameter1,
                parameter1+' < X <='+ parameter2,
                parameter2 + '< X <='+ parameter3,
                parameter3+' < X'
              ],
              datasets: [{
                label: 'Montant des MPPA + MABC',
                data: [result_achats_mounts[1]["nombre_achats_inf_four1"]+result_achats_mounts[0]["nombre_achats_inf_four1"],
                result_achats_mounts[1]["nombre_achats_four1_four2"]+result_achats_mounts[0]["nombre_achats_four1_four2"],
                result_achats_mounts[1]["nombre_achats_four2_four3"]+result_achats_mounts[0]["nombre_achats_four2_four3"],
                result_achats_mounts[1]["nombre_achats_sup_four3"]+result_achats_mounts[1]["nombre_achats_sup_four3"]],
                backgroundColor: [
                  'rgb(0,99,203)',
                  'rgb(188,205,255)',
                  'rgb(184,254,201)',
                  'rgb(206,97,74)'
                ],
                hoverOffset: 4
              }],
            options: {
                responsive: false,
        
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