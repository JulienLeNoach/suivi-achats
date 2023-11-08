import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF

export default class extends Controller {
  download() {
    const canvas = document.getElementById('myChart');
    const canvas2 = document.getElementById('myChart2');
    canvas.fillStyle = "white";
    const canvasImage = canvas.toDataURL('image/png', 1.0);
    const canvasImage2 = canvas2.toDataURL('image/png', 1.0);
    let pdf = new jsPDF('p', 'mm', [360, 350]);
    pdf.setFontSize(20);
    pdf.addImage(canvasImage, 'png', 15, 15, 280, 150);
    pdf.addImage(canvasImage2, 'png', 15, 200, 280, 150);
    pdf.setFillColor(106, 106, 244, 1);
    pdf.save('Graphique.pdf');
  }
}