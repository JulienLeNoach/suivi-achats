import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

export default class extends Controller {

  async downloadgraphBar() {
    const canvas1 = document.getElementById('topVal');
    canvas1.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage1 = canvas1.toDataURL('image/png', 1.0);

    const canvas2 = document.getElementById('topVol');
    canvas2.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage2 = canvas2.toDataURL('image/png', 1.0);

    const canvas3 = document.getElementById('actAppro');
    canvas3.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage3 = canvas3.toDataURL('image/png', 1.0);

    const volvalTable = document.getElementById('volvalTable');
    const actApproTable = document.getElementById('actApproTable');
    

    volvalTable.style.backgroundColor = "white";
    actApproTable.style.backgroundColor = "white";

    const volvalTableCanvas = await html2canvas(volvalTable);
    const actApproTableCanvas = await html2canvas(actApproTable);

    const volvalTableImage = volvalTableCanvas.toDataURL('image/png', 1.0);
    const actApproImage = actApproTableCanvas.toDataURL('image/png', 1.0);

    let pdf = new jsPDF('l', 'mm', [300, 200]); 
    pdf.setFontSize(15);

    pdf.addImage(canvasImage1, 'png', 190, 15, 70, 70);

    pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);

    pdf.addImage(canvasImage3, 'png', 15, 120, 270, 70);


    pdf.addImage(volvalTableImage, 'png', 20, 25, 90, 40);

    pdf.text("Activité appro PME", 120, 120);
    pdf.addImage(actApproImage, 'png', 15, 95, 270, 15);


    pdf.setFillColor(106, 106, 244, 1);
    pdf.save('Graphique.pdf');
  }

  exportTableToExcel() {
    const volvalTable = document.getElementById("volvalTable");
    const actApproTable = document.getElementById("actApproTable");



    // Extract the HTML content of the tables with captions
    const html = '<table border=1>' + volvalTable.innerHTML + '</table>';
    const html2 = '<table border=1><caption>Activité appro PME</caption>' + actApproTable.innerHTML + '</table>';


    // Combine tables with page breaks
    const combinedHtml = html + '<br clear="all" style="page-break-before:always;" />' + html2 + '<br clear="all" style="page-break-before:always;" />' 

    // Create a Blob containing the HTML data with Excel MIME type
    const blob = new Blob([combinedHtml], { type: 'application/vnd.ms-excel' });

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