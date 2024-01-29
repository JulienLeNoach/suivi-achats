import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'


const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const ctxdelayChart = document.getElementById('delayChart');
const ctxAntenne = document.getElementById('ctxAntenne');
const ctxBudget = document.getElementById('ctxBudget');
const ctxAppro = document.getElementById('ctxAppro');
const ctxFin = document.getElementById('ctxFin');
const ctxPFAF = document.getElementById('ctxPFAF');
const ctxChorus = document.getElementById('ctxChorus');
const ctxTotalDelay = document.getElementById('ctxTotalDelay');

export default class extends Controller {


    connect(){
      ctxdelayChart.width = 1200; // Définit la largeur du premier canvas
      ctxdelayChart.height = 800; // Définit la hauteur du premier canvas


      
        new Chart(ctxdelayChart, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                  label: 'Transmission',
                  data: transStat,
                  borderWidth: 1,
                  backgroundColor: 'rgb(77 104 188)', 
                  borderColor: 'rgb(77 104 188)'
              },
            {
              label: 'Notification',
              data: notStat,
              borderWidth: 1,
              backgroundColor: 'rgb(162 225 228)', 
              borderColor: 'rgb(162 225 228)' 
            }],
            options: {
                responsive: false,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              },

            },

          }
          });
          
          new Chart(ctxAntenne, {
            type: 'pie',
            data: {
              labels: [
                '<= 3 jours / ' +[achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]] + "%",
                '> 3 jours / '+[achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"]] + "%",
              ],
              datasets: [{
                label: 'Antenne GSBDD',
                data: [achats_delay_all[0]["CountAntInf3"],achats_delay_all[0]["CountAntSup3"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
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
              plugins: {
                title: {
                    display: true,
                    text: 'Custom Chart Title',
                    
                },
                
              },
            }
          }
          });

          new Chart(ctxBudget, {
            type: 'pie',
            data: {
              labels: [
                '<= 3 jours / ' +[achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"]] + "%",
                '> 3 jours / '+[achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"]] + "%",
              ],
              datasets: [{
                label: 'Budget',
                data: [achats_delay_all[1]["CountBudgetInf3"],achats_delay_all[1]["CountBudgetSup3"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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
          new Chart(ctxAppro, {
            type: 'pie',
            data: {
              labels: [
                '<= 7 jours / ' +[achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]] + "%",
                '> 7 jours / '+[achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]] + "%",
              ],
              datasets: [{
                label: 'PFAF Appro',
                data: [achats_delay_all[2]["CountApproInf7"],achats_delay_all[2]["CountApproSup7"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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
          new Chart(ctxFin, {
            type: 'pie',
            data: {
              labels: [
                '< 7 jours / ' +[achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"]] + "%",
                '> 7 jours / '+[achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"]] + "%",
              ],
              datasets: [{
                label: 'PFAF Fin',
                data: [achats_delay_all[3]["CountFinInf7"],achats_delay_all[3]["CountFinSup7"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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
          new Chart(ctxPFAF, {
            type: 'pie',
            data: {
              labels: [
                '<= 14 jours / ' +[achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"]] + "%",
                '> à 14 jours / '+[achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"]] + "%",
              ],
              datasets: [{
                label: 'PFAF Fin',
                data: [achats_delay_all[5]["CountPfafInf14"],achats_delay_all[5]["CountPfafSup14"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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
          new Chart(ctxChorus, {
            type: 'pie',
            data: {
              labels: [
                '<= 10 jours / ' +[achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"]] + "%",
                '> à 10 jours / '+[achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"]] + "%",
              ],
              datasets: [{
                label: 'Chorus Formul.',
                data: [achats_delay_all[4]["CountChorusFormInf10"],achats_delay_all[4]["CountChorusFormSup10"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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
          new Chart(ctxTotalDelay, {
            type: 'pie',
            data: {
              labels: [
                '<= 15 jours / ' +[achats_delay_all[6]["Pourcentage_Delai_Inf_15_Jours"]] + "%",
                '> à 15 jours / '+[achats_delay_all[6]["Pourcentage_Delai_Sup_15_Jours"]] + "%",
              ],
              datasets: [{
                label: 'Délai Total',
                data: [achats_delay_all[6]["CountDelaiTotalInf15"],achats_delay_all[6]["CountDelaiTotalSup15"]],
                backgroundColor: [
                  'rgb(77 104 188)',
                  'rgb(162 225 228)',
                ],
                hoverOffset: 4
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