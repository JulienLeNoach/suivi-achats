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

    const tableTotaux = document.getElementById('tableTotaux');
    const mppaTable = document.getElementById('mppaTable');
    const mabcTable = document.getElementById('mabcTable');

    tableTotaux.style.backgroundColor = "white";
    mppaTable.style.backgroundColor = "white";
    mabcTable.style.backgroundColor = "white"; 

    const tableTotauxCanvas = await html2canvas(tableTotaux);
    const mpppaTableCanvas = await html2canvas(mppaTable);
    const mabcTableCanvas = await html2canvas(mabcTable);

    const tableTotauxImage = tableTotauxCanvas.toDataURL('image/png', 1.0);
    const mppaTableImage = mpppaTableCanvas.toDataURL('image/png', 1.0);
    const mabcTableImage = mabcTableCanvas.toDataURL('image/png', 1.0);

    let pdf = new jsPDF('p', 'mm', [300, 200]); 
    pdf.setFontSize(15);

    pdf.addImage(canvasImage1, 'png', 15, 15, 70, 70);

    pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);

    pdf.text("Montant total", 15, 115);
    pdf.addImage(tableTotauxImage, 'png', 15, 130, 150, 60);

    pdf.text("Montant des MPPA", 15, 200);
    pdf.addImage(mppaTableImage, 'png', 15, 215, 80, 15);

    pdf.text("Montant des MABC", 100, 200);
    pdf.addImage(mabcTableImage, 'png', 100, 215, 80, 15);

    pdf.setFillColor(106, 106, 244, 1);
    pdf.save('Graphique.pdf');
  }
}
