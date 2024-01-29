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

    const mppaTable = document.getElementById('mppaTable');
    const mabcTable = document.getElementById('mabcTable');
    const allMountTable = document.getElementById('allMountTable');

    const criteriaForm = criteria; 


    mppaTable.style.backgroundColor = "white";
    mabcTable.style.backgroundColor = "white"; 
    allMountTable.style.backgroundColor = "white"; 

    const mpppaTableCanvas = await html2canvas(mppaTable);
    const mabcTableCanvas = await html2canvas(mabcTable);
    const allMountTableCanvas = await html2canvas(allMountTable);

    const mppaTableImage = mpppaTableCanvas.toDataURL('image/png', 1.0);
    const mabcTableImage = mabcTableCanvas.toDataURL('image/png', 1.0);
    const allMountTableCanvasImage = allMountTableCanvas.toDataURL('image/png', 1.0);

    const values = Object.entries(criteriaForm)
    .filter(([key, value]) => value !== null && value !== undefined)
    .map(([key, value]) => `${key}: ${value}`);
    const criteriaText = values.join(', ');

    let pdf = new jsPDF('l', 'mm', 'a4');
    pdf.setFontSize(10);
    pdf.text("Critères de sélection : " + criteriaText, 15, 5);
    pdf.setFontSize(15);

    pdf.addImage(canvasImage1, 'png', 15, 15, 70, 70);
    pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);
    pdf.addImage(canvasImage3, 'png', 205, 15, 70, 70);

    pdf.text("Montant des MPPA", 25, 90);
    pdf.addImage(mppaTableImage, 'png', 15, 95, 80, 15);

    pdf.text("Montant des MABC", 120, 90);
    pdf.addImage(mabcTableImage, 'png', 100, 95, 80, 15);


    pdf.text("Montant des MABC + MPPA", 195, 90);
    pdf.addImage(allMountTableCanvasImage, 'png', 185, 95, 80, 15);
    pdf.setFontSize(10);

    pdf.setFillColor(106, 106, 244, 1);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 10);
    }
    pdf.save(`Graphique type marché ${dateEdited} .pdf`);
  }




}
