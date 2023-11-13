import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

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

  generatePDFTable() {
    // Create a jsPDF instance with landscape orientation
    const pdf = new jsPDF('l');
  
    // Select the first table HTML element
    const table1 = document.getElementById('volValTable');
  
    // Add a title for the first table
    pdf.text('Activité en volume', 20, 10);
  
    // Use html2canvas to render the first table as an image
    html2canvas(table1).then(canvas1 => {
      const imgData1 = canvas1.toDataURL('image/png');
  
      // Add the first table image to the PDF
      pdf.addImage(imgData1, 'PNG', 5, 30);
  
      // Add a title for the second table
      pdf.text('Activité en valeur (HT)', 20, 80);
  
      // Select the second table HTML element
      const table2 = document.getElementById('tableCheck');
  
      // Use html2canvas to render the second table as an image
      html2canvas(table2).then(canvas2 => {
        const imgData2 = canvas2.toDataURL('image/png');
  
        // Add the second table image to the same page
        pdf.addImage(imgData2, 'PNG', 5, 100);
  
        // Save the PDF file
        pdf.save('Activité Volume et valeur.pdf');
      });
    });
  }
}