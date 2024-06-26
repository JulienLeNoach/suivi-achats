import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto'

const ctxactAppro = document.getElementById('actAppro');
const ctxtopVal = document.getElementById('topVal');
const ctxtopVol = document.getElementById('topVol');

const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];


export default class extends Controller {

    connect(){
        const dataValues = [];
        const dataValues2 = [];
        const dataValues3 = [];

        result_achatsSum.forEach((achats) => {
            dataValues.push(achats["nombre_achats_pme_type_marche_1"]);
        });
        result_achatsSumVol.forEach((achats) => {

            dataValues2.push({
              key: achats["departement"],
              value: achats["total_nombre_achats"]
          });
        });
        result_achatsSumVal.forEach((achats) => {
            dataValues3.push({
                key: achats["departement"],
                value: achats["somme_montant_achat"]
            });
        });
        new Chart(ctxactAppro, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: 'NB PME',
                data: dataValues,
                backgroundColor: [
                  'rgb(206,5,0)',

                ],
                hoverOffset: 4
              }],
            options: {
                responsive: true,
                // maintainAspectRatio:false,
        
              scales: {
                y: {
                  beginAtZero: true
                }
              },

            }
          }
          });


          const labelsVal = [];
          const dataVal = [];
            dataValues3.forEach((item) => {
                labelsVal.push(item.key);
              dataVal.push(item.value);
          });

          new Chart(ctxtopVal, {
            type: 'bar',
            data: {
              labels: labelsVal,
              datasets: [{
                label: 'Valeur PME',
                data: dataVal,
                backgroundColor: [
                  'rgb(251,231,105)',

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
          console.log(dataValues2);

        const labelsVol = [];
        const dataVol = [];
          dataValues2.forEach((item) => {
            labelsVol.push(item.key);
            dataVol.push(item.value);
        });
        
        new Chart(ctxtopVol, {
            type: 'bar',
            data: {
                labels: labelsVol,
                datasets: [{
                    label: 'Volume PME',
                    data: dataVol,
                    backgroundColor: ['rgb(169,251,104)'],
                    hoverOffset: 4
                }],
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });

        }

}
    