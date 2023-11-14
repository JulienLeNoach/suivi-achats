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
      var canvas = document.getElementById('delayChart');
      canvas.fillStyle = "white";
      var canvasImage = canvas.toDataURL('image/png', 1.0);
      var pdf = new _jspdf["default"]('p', 'mm', [360, 350]);
      pdf.setFontSize(20);
      pdf.addImage(canvasImage, 'png', 15, 15, 280, 150);
      pdf.setFillColor(106, 106, 244, 1);
      pdf.save('Graphique.pdf');
    }
  }, {
    key: "downloadgraphPie",
    value: function downloadgraphPie() {
      var ctxAntenne = document.getElementById('ctxAntenne');
      var ctxBudget = document.getElementById('ctxBudget');
      var ctxAppro = document.getElementById('ctxAppro');
      var ctxFin = document.getElementById('ctxFin');
      var ctxPFAF = document.getElementById('ctxPFAF');
      var ctxChorus = document.getElementById('ctxChorus');
      var ctxTotalDelay = document.getElementById('ctxTotalDelay');
      ctxAntenne.fillStyle = "white";
      ctxBudget.fillStyle = "white";
      ctxAppro.fillStyle = "white";
      ctxFin.fillStyle = "white";
      ctxPFAF.fillStyle = "white";
      ctxChorus.fillStyle = "white";
      ctxTotalDelay.fillStyle = "white";
      var ctxAntenneImage = ctxAntenne.toDataURL('image/png', 1.0);
      var ctxBudgetImage = ctxBudget.toDataURL('image/png', 1.0);
      var ctxApproImage = ctxAppro.toDataURL('image/png', 1.0);
      var ctxFinImage = ctxFin.toDataURL('image/png', 1.0);
      var ctxPFAFImage = ctxPFAF.toDataURL('image/png', 1.0);
      var ctxChorusImage = ctxChorus.toDataURL('image/png', 1.0);
      var ctxTotalDelayImage = ctxTotalDelay.toDataURL('image/png', 1.0);
      var pdf = new _jspdf["default"]('p', 'mm', [360, 350]);
      pdf.setFontSize(20);
      pdf.addImage(ctxAntenneImage, 'png', 15, 15, 70, 70);
      pdf.addImage(ctxBudgetImage, 'png', 85, 15, 70, 70);
      pdf.addImage(ctxApproImage, 'png', 170, 15, 70, 70);
      pdf.addImage(ctxFinImage, 'png', 255, 15, 70, 70);
      pdf.addImage(ctxPFAFImage, 'png', 15, 85, 70, 70);
      pdf.addImage(ctxChorusImage, 'png', 85, 85, 70, 70);
      pdf.addImage(ctxTotalDelayImage, 'png', 170, 85, 70, 70);
      pdf.setFillColor(106, 106, 244, 1);
      pdf.save('GraphPieDelay.pdf');
    }
  }, {
    key: "generatePDFTable",
    value: function generatePDFTable() {
      // Créez un objet jsPDF
      var pdf = new _jspdf["default"]('l'); // Select the table HTML element

      var table = document.getElementById('delayTable'); // Use html2canvas to render the table as an image

      (0, _html2canvas["default"])(table).then(function (canvas) {
        var imgData = canvas.toDataURL('image/png'); // Add the image to the PDF

        pdf.addImage(imgData, 'PNG', 5, 30); // Save the PDF file

        pdf.save('table.pdf');
      });
    }
  }, {
    key: "exportTableToExcel",
    value: function exportTableToExcel() {
      var table = document.getElementById("delayTable"); // Extract the HTML content of the table

      var html = table.outerHTML; // Create a Blob containing the HTML data with Excel MIME type

      var blob = new Blob([html], {
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