import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

const labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const ctxAntenne = document.getElementById('ctxAntenne');
const ctxBudget = document.getElementById('ctxBudget');
const ctxAppro = document.getElementById('ctxAppro');
const ctxTotalDelay = document.getElementById('ctxTotalDelay');

export default class extends Controller {
    connect(){
        new Chart(ctxAntenne, {
            type: 'pie',
            data: {
                labels: [
                    `<= ${delaiTransmissions} jours / ` + achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"] + "%",
                    `> ${delaiTransmissions} jours / ` + achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] + "%"
                ],
                datasets: [{
                    label: 'Transmission',
                    data: [achats_delay_all[0]["CountAntInf3"], achats_delay_all[0]["CountAntSup3"]],
                    backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        display: false  // Désactive l'affichage de l'axe des ordonnées
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Custom Chart Title',
                    }
                }
            }
        });

        new Chart(ctxBudget, {
            type: 'pie',
            data: {
                labels: [
                    `<= ${delaiTraitement} jours / ` + achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] + "%",
                    `> ${delaiTraitement} jours / ` + achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] + "%"
                ],
                datasets: [{
                    label: 'Traitement',
                    data: [achats_delay_all[1]["CountBudgetInf3"], achats_delay_all[1]["CountBudgetSup3"]],
                    backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        display: false  // Désactive l'affichage de l'axe des ordonnées
                    }
                }
            }
        });

        new Chart(ctxAppro, {
            type: 'pie',
            data: {
                labels: [
                    `<= ${delaiNotifications} jours / ` + achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"] + "%",
                    `> ${delaiNotifications} jours / ` + achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"] + "%"
                ],
                datasets: [{
                    label: 'Notification',
                    data: [achats_delay_all[2]["CountApproInf7"], achats_delay_all[2]["CountApproSup7"]],
                    backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        display: false  // Désactive l'affichage de l'axe des ordonnées
                    }
                }
            }
        });

        new Chart(ctxTotalDelay, {
            type: 'pie',
            data: {
                labels: [
                    `<= ${delaiTotal} jours / ` + achats_delay_all[3]["Pourcentage_Delai_Inf_15_Jours"] + "%",
                    `> ${delaiTotal} jours / ` + achats_delay_all[3]["Pourcentage_Delai_Sup_15_Jours"] + "%"
                ],
                datasets: [{
                    label: 'Délai Total',
                    data: [achats_delay_all[3]["CountDelaiTotalInf15"], achats_delay_all[3]["CountDelaiTotalSup15"]],
                    backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        display: false  // Désactive l'affichage de l'axe des ordonnées
                    }
                }
            }
        });
    }
}
