"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

var _jspdf = _interopRequireDefault(require("jspdf"));

var _html2canvas = _interopRequireDefault(require("html2canvas"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

var _default =
/*#__PURE__*/
function (_Controller) {
  _inherits(_default, _Controller);

  function _default() {
    _classCallCheck(this, _default);

    return _possibleConstructorReturn(this, _getPrototypeOf(_default).apply(this, arguments));
  }

  _createClass(_default, [{
    key: "download",
    value: function download() {
      var canvas = document.getElementById('myChart');
      var canvas2 = document.getElementById('myChart2');
      canvas.fillStyle = "white";
      var canvasImage = canvas.toDataURL('image/png', 1.0);
      var canvasImage2 = canvas2.toDataURL('image/png', 1.0);
      var pdf = new _jspdf["default"]('p', 'mm', [360, 350]);
      pdf.setFontSize(20);
      pdf.addImage(canvasImage, 'png', 15, 15, 280, 150);
      pdf.addImage(canvasImage2, 'png', 15, 200, 280, 150);
      pdf.setFillColor(106, 106, 244, 1);
      pdf.save('Graphique.pdf');
    }
  }, {
    key: "generatePDFTable",
    value: function generatePDFTable() {
      // Create a jsPDF instance with landscape orientation
      var pdf = new _jspdf["default"]('l'); // Select the first table HTML element

      var table1 = document.getElementById('volValTable'); // Add a title for the first table

      pdf.text('Activité en volume', 20, 10); // Use html2canvas to render the first table as an image

      (0, _html2canvas["default"])(table1).then(function (canvas1) {
        var imgData1 = canvas1.toDataURL('image/png'); // Add the first table image to the PDF

        pdf.addImage(imgData1, 'PNG', 5, 30); // Add a title for the second table

        pdf.text('Activité en valeur (HT)', 20, 80); // Select the second table HTML element

        var table2 = document.getElementById('tableCheck'); // Use html2canvas to render the second table as an image

        (0, _html2canvas["default"])(table2).then(function (canvas2) {
          var imgData2 = canvas2.toDataURL('image/png'); // Add the second table image to the same page

          pdf.addImage(imgData2, 'PNG', 5, 100); // Save the PDF file

          pdf.save('Activité Volume et valeur.pdf');
        });
      });
    }
  }, {
    key: "exportTableToExcel",
    value: function exportTableToExcel() {
      // const table = document.getElementById("volValTable");
      var table1 = document.getElementById("tableCheck");
      var table2 = document.getElementById("volValTable"); // Extraire le contenu HTML des deux tables

      var html1 = table1.outerHTML;
      var html2 = table2.outerHTML; // Concaténer le HTML des deux tables

      var combinedHtml = html1 + html2; // Créer un Blob contenant les données HTML avec le type MIME Excel

      var blob = new Blob([combinedHtml], {
        type: 'application/vnd.ms-excel'
      }); // Créer une URL pour le Blob

      var url = URL.createObjectURL(blob); // Créer un élément d'ancre temporaire pour le téléchargement

      var a = document.createElement('a');
      a.href = url; // Définir le nom de fichier souhaité pour le fichier téléchargé

      a.download = 'delai_activite_tableau.xls'; // Simuler un clic sur l'ancre pour déclencher le téléchargement

      a.click(); // Libérer l'objet URL pour libérer des ressources

      URL.revokeObjectURL(url);
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;