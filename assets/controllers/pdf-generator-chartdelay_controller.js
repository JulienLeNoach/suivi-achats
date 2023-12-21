import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

export default class extends Controller {
  
  downloadgraphBar() {
    const canvas = document.getElementById('delayChart');
    canvas.fillStyle = "white";
    const canvasImage = canvas.toDataURL('image/png', 1.0);
    let pdf = new jsPDF('p', 'mm', [360, 350]);
    pdf.setFontSize(20);
    pdf.text('Délai d\'activité annuelle', 15, 10);

    pdf.addImage(canvasImage, 'png', 15, 15, 280, 150);
    pdf.setFillColor(106, 106, 244, 1);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 60, 10);
    }
    pdf.save('Graphique.pdf');
  }
  downloadgraphPie() {
    const ctxAntenne = document.getElementById('ctxAntenne');
    const ctxBudget = document.getElementById('ctxBudget');
    const ctxAppro = document.getElementById('ctxAppro');
    const ctxFin = document.getElementById('ctxFin');
    const ctxPFAF = document.getElementById('ctxPFAF');
    const ctxChorus = document.getElementById('ctxChorus');
    const ctxTotalDelay = document.getElementById('ctxTotalDelay');

    ctxAntenne.fillStyle = "white";
    ctxBudget.fillStyle = "white";
    ctxAppro.fillStyle = "white";
    ctxFin.fillStyle = "white";
    ctxPFAF.fillStyle = "white";
    ctxChorus.fillStyle = "white";
    ctxTotalDelay.fillStyle = "white";
    
    const ctxAntenneImage = ctxAntenne.toDataURL('image/png', 1.0);
    const ctxBudgetImage = ctxBudget.toDataURL('image/png', 1.0);
    const ctxApproImage = ctxAppro.toDataURL('image/png', 1.0);
    const ctxFinImage = ctxFin.toDataURL('image/png', 1.0);
    const ctxPFAFImage = ctxPFAF.toDataURL('image/png', 1.0);
    const ctxChorusImage = ctxChorus.toDataURL('image/png', 1.0);
    const ctxTotalDelayImage = ctxTotalDelay.toDataURL('image/png', 1.0);

    let pdf = new jsPDF('p', 'mm', [360, 370]); // Augmentation de la hauteur pour le décalage
    pdf.setFontSize(20);
    
    // Ajout du titre au-dessus de tous les éléments
    pdf.text('Délai d\'activité annuelle détaillé par traitement', 15, 10);

    // Ajout des titres et images pour chaque graphique avec décalage
    const titles = [
        'Ant. GSBDD', 'Budget', 'Appro', 'Fin', 'PFAF', 'Chorus', 'Délai total'
    ];
    const images = [
        ctxAntenneImage, ctxBudgetImage, ctxApproImage, ctxFinImage, ctxPFAFImage, ctxChorusImage, ctxTotalDelayImage
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
    pdf.save('GraphPieDelay.pdf');
    
  }
   generatePDFTable() {
    // Créez un objet jsPDF
    const pdf = new jsPDF('l', 'mm', 'a3');

    // Select the table HTML element
    const table = document.getElementById('delayTable');

    // Use html2canvas to render the table as an image
    html2canvas(table).then(canvas => {
      // Réduction de la taille de l'image
      const scale = 0.2;
      const imgWidth = canvas.width * scale;
      const imgHeight = canvas.height * scale;

      // Conversion du canvas en image PNG
      const imgData = canvas.toDataURL('image/png');
      const yearOption = document.querySelector('#statistic_date option:checked').text;
      const checkedElement = document.querySelector('#statistic_jourcalendar input:checked');
      console.log(checkedElement);
      // Ajout d'un titre au-dessus du tableau
      const title = 'Délai Activité Annuelle';
      pdf.setFontSize(16);
      pdf.text(title, 60, 60); // Position du titre
      pdf.text(yearOption, 120, 60); // Position du titre

      // Si vous voulez ajouter le texte sélectionné à côté de l'année
      pdf.setFontSize(12);

      // Ajout de l'image redimensionnée au PDF
      pdf.addImage(imgData, 'PNG', 30, 80, imgWidth, imgHeight);
      const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
      const pageCount = pdf.internal.getNumberOfPages();
      for (let i = 1; i <= pageCount; i++) {
          pdf.setPage(i);
          pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 60, 10);
      }
      // Enregistrement du PDF
      pdf.save('table.pdf');
  });
}
exportTableToExcel(){
  const table = document.getElementById("delayTable");

  // Extract the HTML content of the table
  const html = table.outerHTML;

  // Create a Blob containing the HTML data with Excel MIME type
  const blob = new Blob([html], {type: 'application/vnd.ms-excel'});

  // Create a URL for the Blob
  const url = URL.createObjectURL(blob);

  // Create a temporary anchor element for downloading
  const a = document.createElement('a');
  a.href = url;

  // Set the desired filename for the downloaded file
  a.download = 'delai_activite_tableau.xls';

  // Simulate a click on the anchor to trigger download
  a.click();

  // Release the URL object to free up resources
  URL.revokeObjectURL(url);
}
}