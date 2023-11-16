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
    key: "downloadgraphBar",
    value: function downloadgraphBar() {
      var canvas1, canvasImage1, canvas2, canvasImage2, canvas3, canvasImage3, tableTotaux, mppaTable, mabcTable, tableTotauxCanvas, mpppaTableCanvas, mabcTableCanvas, tableTotauxImage, mppaTableImage, mabcTableImage, pdf;
      return regeneratorRuntime.async(function downloadgraphBar$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              canvas1 = document.getElementById('mppaMountChart');
              canvas1.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage1 = canvas1.toDataURL('image/png', 1.0);
              canvas2 = document.getElementById('mabcMountChart');
              canvas2.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage2 = canvas2.toDataURL('image/png', 1.0);
              canvas3 = document.getElementById('allMountChart');
              canvas3.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage3 = canvas3.toDataURL('image/png', 1.0);
              tableTotaux = document.getElementById('tableTotaux');
              mppaTable = document.getElementById('mppaTable');
              mabcTable = document.getElementById('mabcTable');
              tableTotaux.style.backgroundColor = "white";
              mppaTable.style.backgroundColor = "white";
              mabcTable.style.backgroundColor = "white";
              _context.next = 17;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(tableTotaux));

            case 17:
              tableTotauxCanvas = _context.sent;
              _context.next = 20;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(mppaTable));

            case 20:
              mpppaTableCanvas = _context.sent;
              _context.next = 23;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(mabcTable));

            case 23:
              mabcTableCanvas = _context.sent;
              tableTotauxImage = tableTotauxCanvas.toDataURL('image/png', 1.0);
              mppaTableImage = mpppaTableCanvas.toDataURL('image/png', 1.0);
              mabcTableImage = mabcTableCanvas.toDataURL('image/png', 1.0);
              pdf = new _jspdf["default"]('p', 'mm', [300, 200]);
              pdf.setFontSize(15);
              pdf.addImage(canvasImage1, 'png', 15, 15, 70, 70);
              pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);
              pdf.addImage(canvasImage3, 'png', 65, 120, 70, 70);
              pdf.text("Montant total", 85, 195);
              pdf.addImage(tableTotauxImage, 'png', 15, 200, 150, 60);
              pdf.text("Montant des MPPA", 25, 90);
              pdf.addImage(mppaTableImage, 'png', 15, 95, 80, 15);
              pdf.text("Montant des MABC", 120, 90);
              pdf.addImage(mabcTableImage, 'png', 100, 95, 80, 15);
              pdf.setFillColor(106, 106, 244, 1);
              pdf.save('Graphique.pdf');

            case 40:
            case "end":
              return _context.stop();
          }
        }
      });
    }
  }, {
    key: "exportTableToExcel",
    value: function exportTableToExcel() {
      var tableTotaux = document.getElementById("tableTotaux");
      var mppaTable = document.getElementById("mppaTable");
      var mabcTable = document.getElementById("mabcTable");
      var allMountTable = document.getElementById("allMountTable"); // Supposons que cette table contient les données pour le graphique en barres

      var barChartData = document.getElementById("barChartData"); // Extract the HTML content of the tables with captions

      var html = '<table border=1>' + tableTotaux.innerHTML + '</table>';
      var html2 = '<table border=1><caption>Montant des MPPA</caption>' + mppaTable.innerHTML + '</table>';
      var html3 = '<table border=1><caption>Montant des MABC</caption>' + mabcTable.innerHTML + '</table>';
      var html4 = '<table border=1><caption>Montant des MPPA + MABC</caption>' + allMountTable.innerHTML + '</table>'; // Ajout de la table pour les données du graphique

      var html5 = '<table border=1><caption>Données pour Graphique en Barres</caption>' + barChartData.innerHTML + '</table>'; // Combine tables with page breaks

      var combinedHtml = html + '<br clear="all" style="page-break-before:always;" />' + html2 + '<br clear="all" style="page-break-before:always;" />' + html3 + '<br clear="all" style="page-break-before:always;" />' + html4 + '<br clear="all" style="page-break-before:always;" />' + html5; // Create a Blob containing the HTML data with Excel MIME type

      var blob = new Blob([combinedHtml], {
        type: 'application/vnd.ms-excel'
      }); // Create a URL for the Blob

      var url = URL.createObjectURL(blob); // Create a temporary anchor element for downloading

      var a = document.createElement('a');
      a.href = url; // Set the desired filename for the downloaded file

      a.download = 'delai_activite_tableau.xls'; // Simulate a click on the anchor to trigger download

      a.click(); // Release the URL object to free up resources

      URL.revokeObjectURL(url);
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;