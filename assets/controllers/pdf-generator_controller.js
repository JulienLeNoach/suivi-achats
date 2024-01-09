import { Controller } from '@hotwired/stimulus';
import jsPDF from 'jspdf'; // Importez jsPDF
import html2canvas from 'html2canvas';

export default class extends Controller {
  connect(){

  }
  download() {
    const canvas = document.getElementById('myChart');
    const canvas2 = document.getElementById('myChart2');

    const criteriaForm = criteria; 
    
    canvas.fillStyle = "white";
    const canvasImage = canvas.toDataURL('image/png', 1.0);
    const canvasImage2 = canvas2.toDataURL('image/png', 1.0);

    const values = Object.entries(criteriaForm)
    .filter(([key, value]) => value !== null && value !== undefined)
    .map(([key, value]) => `${key}: ${value}`);
    const criteriaText = values.join(', ');
     
    let pdf = new jsPDF('p', 'mm', [360, 350]);
    pdf.setFontSize(8);
    pdf.text("Critères de sélection : " + criteriaText, 15, 10);
    pdf.setFontSize(12);
    pdf.text('Activité en volume', 15, 25);
    pdf.addImage(canvasImage, 'png', 15, 25, 160, 95);
    pdf.text('Activité en valeur ('+criteria.Taxe+')', 175, 25);
    pdf.addImage(canvasImage2, 'png', 175, 25, 160, 95);
    pdf.setFontSize(8);
    pdf.setFillColor(106, 106, 244, 1);
    const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    const pageCount = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 10);
    }
    pdf.save('Graphique.pdf');
  }


  exportTableToExcel(){
    // const table = document.getElementById("volValTable");
    const table1 = document.getElementById("tableCheck");
    const table2 = document.getElementById("volValTable");

    // Extraire le contenu HTML des deux tables
    const html1 = table1.outerHTML;
    const html2 = table2.outerHTML;

    // Concaténer le HTML des deux tables
    const combinedHtml = html1 + html2;

    // Créer un Blob contenant les données HTML avec le type MIME Excel
    const blob = new Blob([combinedHtml], {type: 'application/vnd.ms-excel'});

    // Créer une URL pour le Blob
    const url = URL.createObjectURL(blob);

    // Créer un élément d'ancre temporaire pour le téléchargement
    const a = document.createElement('a');
    a.href = url;

    // Définir le nom de fichier souhaité pour le fichier téléchargé
    a.download = 'delai_activite_tableau.xls';

    // Simuler un clic sur l'ancre pour déclencher le téléchargement
    a.click();

    // Libérer l'objet URL pour libérer des ressources
    URL.revokeObjectURL(url);
  }
}