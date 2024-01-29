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
    
    const criteriaForm = criteria; 


    volvalTable.style.backgroundColor = "white";
    actApproTable.style.backgroundColor = "white";

    const volvalTableCanvas = await html2canvas(volvalTable);
    const actApproTableCanvas = await html2canvas(actApproTable);

    const volvalTableImage = volvalTableCanvas.toDataURL('image/png', 1.0);
    const actApproImage = actApproTableCanvas.toDataURL('image/png', 1.0);

    const values = Object.entries(criteriaForm)
    .filter(([key, value]) => value !== null && value !== undefined)
    .map(([key, value]) => `${key}: ${value}`);
    const criteriaText = values.join(', ');

    let pdf = new jsPDF('l', 'mm', 'a4'); 
    pdf.setFontSize(10);
    pdf.text("Critères de sélection : " + criteriaText, 15, 10);
    pdf.setFontSize(15);


    pdf.text("Top 5 Département MPPA PME en valeur", 10, 25); // Titre pour 'canvasImage1'
    pdf.addImage(canvasImage1, 'png', 10, 25, 130, 35);

    pdf.text("Top 5 Département MPPA PME en volume", 150, 25); // Titre pour 'canvasImage2'
    pdf.addImage(canvasImage2, 'png', 150, 25, 140, 35);

    pdf.text("Activité appro PME en valeur", 110, 70);
    pdf.addImage(canvasImage3, 'png',10, 75, 260, 35);

    pdf.setFillColor(106, 106, 244, 1);
    pdf.setFontSize(10);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {50
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 10);
    }
    pdf.save(`Graphique statistique PME ${dateEdited} .pdf`);
  }



}