import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF

export default class extends Controller {
  connect() {}

  download() {
    const canvas = document.getElementById('myChart');

    const criteriaForm = criteria;

    canvas.fillStyle = "white";
    const canvasImage = canvas.toDataURL('image/png', 1.0);

    const values = Object.entries(criteriaForm)
      .filter(([key, value]) => value !== null && value !== undefined)
      .map(([key, value]) => `${key}: ${value}`);
    const criteriaText = values.join(', ');

    let pdf = new jsPDF('l', 'mm', [360, 350]);
    pdf.setFontSize(8);
    pdf.text("Critères de sélection : " + criteriaText, 15, 10);
    pdf.setFontSize(12);
    pdf.text('Activité en volume et valeur', 15, 25);
    pdf.addImage(canvasImage, 'png', 15, 30, 320, 200);
    pdf.setFontSize(8);
    pdf.setFillColor(106, 106, 244, 1);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
      pdf.setPage(i);
      pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 10);
    }
    pdf.save(`Graphique activité annuelle ${dateEdited}.pdf`);
  }
}
