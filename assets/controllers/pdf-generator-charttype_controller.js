import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

export default class extends Controller {

  async downloadgraphBar() {
    const canvas1 = document.getElementById('mppaMountChart');
    canvas1.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage1 = canvas1.toDataURL('image/png', 1.0);

    const canvas2 = document.getElementById('mabcMountChart');
    canvas2.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage2 = canvas2.toDataURL('image/png', 1.0);

    const canvas3 = document.getElementById('allMountChart');
    canvas3.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc
    const canvasImage3 = canvas3.toDataURL('image/png', 1.0);

    const tableTotaux = document.getElementById('tableTotaux');
    const mppaTable = document.getElementById('mppaTable');
    const mabcTable = document.getElementById('mabcTable');
    const allMountTable = document.getElementById('allMountTable');

    

    tableTotaux.style.backgroundColor = "white";
    mppaTable.style.backgroundColor = "white";
    mabcTable.style.backgroundColor = "white"; 
    allMountTable.style.backgroundColor = "white"; 

    const tableTotauxCanvas = await html2canvas(tableTotaux);
    const mpppaTableCanvas = await html2canvas(mppaTable);
    const mabcTableCanvas = await html2canvas(mabcTable);
    const allMountTableCanvas = await html2canvas(allMountTable);

    const tableTotauxImage = tableTotauxCanvas.toDataURL('image/png', 1.0);
    const mppaTableImage = mpppaTableCanvas.toDataURL('image/png', 1.0);
    const mabcTableImage = mabcTableCanvas.toDataURL('image/png', 1.0);
    const allMountTableCanvasImage = allMountTableCanvas.toDataURL('image/png', 1.0);

    let pdf = new jsPDF('l', 'mm', 'a3');
    pdf.setFontSize(15);

    pdf.addImage(canvasImage1, 'png', 15, 15, 70, 70);
    pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);
    pdf.addImage(canvasImage3, 'png', 65, 120, 70, 70);

    pdf.text("Montant des MPPA", 25, 90);
    pdf.addImage(mppaTableImage, 'png', 15, 95, 80, 15);

    pdf.text("Montant des MABC", 120, 90);
    pdf.addImage(mabcTableImage, 'png', 100, 95, 80, 15);

    pdf.text("Statistiques MPPA/MABC", 190, 25); // Déplace le titre au-dessus de l'image
    pdf.addImage(tableTotauxImage, 'png', 190, 30, 100, 80); // Ajustement des coordonnées pour placer à droite

    pdf.text("Montant des MABC + MPPA", 65, 195);
    pdf.addImage(allMountTableCanvasImage, 'png', 55, 200, 80, 15);

    pdf.setFillColor(106, 106, 244, 1);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 60, 10);
    }
    pdf.save('Graphique.pdf');
  }

  exportTableToExcel() {
    const tableTotaux = document.getElementById("tableTotaux");
    const mppaTable = document.getElementById("mppaTable");
    const mabcTable = document.getElementById("mabcTable");
    const allMountTable = document.getElementById("allMountTable");
    
    // Supposons que cette table contient les données pour le graphique en barres
    const barChartData = document.getElementById("barChartData");

    // Extract the HTML content of the tables with captions
    const html = '<table border=1>' + tableTotaux.innerHTML + '</table>';
    const html2 = '<table border=1><caption>Montant des MPPA</caption>' + mppaTable.innerHTML + '</table>';
    const html3 = '<table border=1><caption>Montant des MABC</caption>' + mabcTable.innerHTML + '</table>';
    const html4 = '<table border=1><caption>Montant des MPPA + MABC</caption>' + allMountTable.innerHTML + '</table>';

    // Ajout de la table pour les données du graphique
    const html5 = '<table border=1><caption>Données pour Graphique en Barres</caption>' + barChartData.innerHTML + '</table>';

    // Combine tables with page breaks
    const combinedHtml = html + '<br clear="all" style="page-break-before:always;" />' + 
                          html2 + '<br clear="all" style="page-break-before:always;" />' + 
                          html3 + '<br clear="all" style="page-break-before:always;" />' + 
                          html4 + '<br clear="all" style="page-break-before:always;" />' +
                          html5;

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
