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
    pdf.addImage(canvasImage, 'png', 15, 15, 280, 150);
    pdf.setFillColor(106, 106, 244, 1);
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

    let pdf = new jsPDF('p', 'mm', [360, 350]);
    pdf.setFontSize(20);
    pdf.addImage(ctxAntenneImage, 'png', 15, 15, 70, 70);
    pdf.addImage(ctxBudgetImage, 'png', 85, 15, 70, 70);
    pdf.addImage(ctxApproImage, 'png', 170, 15, 70, 70);
    pdf.addImage(ctxFinImage, 'png', 255, 15, 70, 70);
    pdf.addImage(ctxPFAFImage, 'png', 15, 85, 70, 70);
    pdf.addImage(ctxChorusImage, 'png', 85, 85, 70, 70);
    pdf.addImage(ctxTotalDelayImage, 'png', 170, 85, 70, 70);

    pdf.setFillColor(106, 106, 244, 1);
    pdf.save('GraphPieDelay.pdf');
    
  }
   generatePDFTable() {
    // Créez un objet jsPDF
    const pdf = new jsPDF('l');

    // Select the table HTML element
    const table = document.getElementById('delayTable');

    // Use html2canvas to render the table as an image
    html2canvas(table).then(canvas => {
      const imgData = canvas.toDataURL('image/png');

      // Add the image to the PDF
      pdf.addImage(imgData, 'PNG', 5, 30);

      // Save the PDF file
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