import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

export default class extends Controller {
  
  // downloadgraphBar() {
  //   const canvas = document.getElementById('delayChart');
  //   const criteriaForm = criteria; 

  //   canvas.fillStyle = "white";
  //   const canvasImage = canvas.toDataURL('image/png', 1.0);

  //   const values = Object.entries(criteriaForm)
  //   .filter(([key, value]) => value !== null && value !== undefined)
  //   .map(([key, value]) => `${key}: ${value}`);
  //   const criteriaText = values.join(', ');

  //   let pdf = new jsPDF('p', 'mm', 'a4');
  //   pdf.setFontSize(8);
  //   pdf.text("Critères de sélection : " + criteriaText, 15, 5);
  //   pdf.setFontSize(15);
  //   pdf.text('Délai d\'activité annuelle', 15, 15);

  //   pdf.addImage(canvasImage, 'png', 15, 20, 180, 150);
  //   pdf.setFillColor(106, 106, 244, 1);
  //   const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
  //   const pageCount = pdf.internal.getNumberOfPages();
  //   pdf.setFontSize(8);
  //   for (let i = 1; i <= pageCount; i++) {
  //       pdf.setPage(i);
  //       pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 5);
  //   }
  //   pdf.save(`Graphique en bar d'activité annuelle ${dateEdited} .pdf`);
  // }
  downloadgraphPie() {
    const ctxAntenne = document.getElementById('ctxAntenne');
    const ctxBudget = document.getElementById('ctxBudget');
    const ctxAppro = document.getElementById('ctxAppro');
    // const ctxFin = document.getElementById('ctxFin');
    // const ctxPFAF = document.getElementById('ctxPFAF');
    // const ctxChorus = document.getElementById('ctxChorus');
    const ctxTotalDelay = document.getElementById('ctxTotalDelay');
    const criteriaForm = criteria; 

    ctxAntenne.fillStyle = "white";
    ctxBudget.fillStyle = "white";
    ctxAppro.fillStyle = "white";
    // ctxFin.fillStyle = "white";
    // ctxPFAF.fillStyle = "white";
    // ctxChorus.fillStyle = "white";
    ctxTotalDelay.fillStyle = "white";
    
    const ctxAntenneImage = ctxAntenne.toDataURL('image/png', 1.0);
    const ctxBudgetImage = ctxBudget.toDataURL('image/png', 1.0);
    const ctxApproImage = ctxAppro.toDataURL('image/png', 1.0);
    // const ctxFinImage = ctxFin.toDataURL('image/png', 1.0);
    // const ctxPFAFImage = ctxPFAF.toDataURL('image/png', 1.0);
    // const ctxChorusImage = ctxChorus.toDataURL('image/png', 1.0);
    const ctxTotalDelayImage = ctxTotalDelay.toDataURL('image/png', 1.0);

    const values = Object.entries(criteriaForm)
    .filter(([key, value]) => value !== null && value !== undefined)
    .map(([key, value]) => `${key}: ${value}`);
    const criteriaText = values.join(', ');

    let pdf = new jsPDF('p', 'mm', [360, 370]); // Augmentation de la hauteur pour le décalage
    pdf.setFontSize(8);
    pdf.text("Critères de sélection : " + criteriaText, 15, 5);

    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    pdf.setFontSize(8);
    for (let i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 5);
    }
    pdf.setFontSize(20);
    
    // Ajout du titre au-dessus de tous les éléments
    pdf.text('Délai d\'activité annuelle détaillé par traitement', 15, 15);

    // Ajout des titres et images pour chaque graphique avec décalage
    const titles = [
        'Transmission', 'Traitement', 'Notification', 'Délai total'
    ];
    const images = [
        ctxAntenneImage, ctxBudgetImage, ctxApproImage,ctxTotalDelayImage
    ];
    const positions = [
        { x: 15, y: 35 }, { x: 85, y: 35 }, { x: 170, y: 35 }, { x: 255, y: 35 },
        { x: 15, y: 120 }, { x: 85, y: 120 }, { x: 170, y: 120 }
    ];

    titles.forEach((title, index) => {
        // Ajout du titre au-dessus de chaque graphique avec le décalage
        pdf.text(title, positions[index].x, positions[index].y - 5);

        // Ajout de chaque graphique avec le titre et le décalage
        pdf.addImage(images[index], 'png', positions[index].x, positions[index].y, 70, 70);
    });

    pdf.setFillColor(106, 106, 244, 1);
    pdf.save(`Graphique en pie d'activité annuelle ${dateEdited} .pdf`);
    
  }
}