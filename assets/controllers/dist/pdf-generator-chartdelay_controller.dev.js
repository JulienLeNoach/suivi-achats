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

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

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
      var criteriaForm = criteria;
      canvas.fillStyle = "white";
      var canvasImage = canvas.toDataURL('image/png', 1.0);
      var values = Object.entries(criteriaForm).filter(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 2),
            key = _ref2[0],
            value = _ref2[1];

        return value !== null && value !== undefined;
      }).map(function (_ref3) {
        var _ref4 = _slicedToArray(_ref3, 2),
            key = _ref4[0],
            value = _ref4[1];

        return "".concat(key, ": ").concat(value);
      });
      var criteriaText = values.join(', ');
      var pdf = new _jspdf["default"]('p', 'mm', 'a4');
      pdf.setFontSize(8);
      pdf.text("Critères de sélection : " + criteriaText, 15, 5);
      pdf.setFontSize(15);
      pdf.text('Délai d\'activité annuelle', 15, 15);
      pdf.addImage(canvasImage, 'png', 15, 20, 180, 150);
      pdf.setFillColor(106, 106, 244, 1);
      var dateEdited = "\xE9dit\xE9 le ".concat(new Date().toLocaleDateString());
      var pageCount = pdf.internal.getNumberOfPages();
      pdf.setFontSize(8);

      for (var i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 5);
      }

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
      var criteriaForm = criteria;
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
      var values = Object.entries(criteriaForm).filter(function (_ref5) {
        var _ref6 = _slicedToArray(_ref5, 2),
            key = _ref6[0],
            value = _ref6[1];

        return value !== null && value !== undefined;
      }).map(function (_ref7) {
        var _ref8 = _slicedToArray(_ref7, 2),
            key = _ref8[0],
            value = _ref8[1];

        return "".concat(key, ": ").concat(value);
      });
      var criteriaText = values.join(', ');
      var pdf = new _jspdf["default"]('p', 'mm', [360, 370]); // Augmentation de la hauteur pour le décalage

      pdf.setFontSize(8);
      pdf.text("Critères de sélection : " + criteriaText, 15, 5);
      var dateEdited = "\xE9dit\xE9 le ".concat(new Date().toLocaleDateString());
      var pageCount = pdf.internal.getNumberOfPages();
      pdf.setFontSize(8);

      for (var i = 1; i <= pageCount; i++) {
        pdf.setPage(i);
        pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 5);
      }

      pdf.setFontSize(20); // Ajout du titre au-dessus de tous les éléments

      pdf.text('Délai d\'activité annuelle détaillé par traitement', 15, 15); // Ajout des titres et images pour chaque graphique avec décalage

      var titles = ['Ant. GSBDD', 'Budget', 'Appro', 'Fin', 'PFAF', 'Chorus', 'Délai total'];
      var images = [ctxAntenneImage, ctxBudgetImage, ctxApproImage, ctxFinImage, ctxPFAFImage, ctxChorusImage, ctxTotalDelayImage];
      var positions = [{
        x: 15,
        y: 35
      }, {
        x: 85,
        y: 35
      }, {
        x: 170,
        y: 35
      }, {
        x: 255,
        y: 35
      }, {
        x: 15,
        y: 120
      }, {
        x: 85,
        y: 120
      }, {
        x: 170,
        y: 120
      }];
      titles.forEach(function (title, index) {
        // Ajout du titre au-dessus de chaque graphique avec le décalage
        pdf.text(title, positions[index].x, positions[index].y - 5); // Ajout de chaque graphique avec le titre et le décalage

        pdf.addImage(images[index], 'png', positions[index].x, positions[index].y, 70, 70);
      });
      pdf.setFillColor(106, 106, 244, 1);
      pdf.save('GraphPieDelay.pdf');
    }
  }, {
    key: "generatePDFTable",
    value: function generatePDFTable() {
      // Créez un objet jsPDF
      var pdf = new _jspdf["default"]('l', 'mm', 'a3'); // Select the table HTML element

      var table = document.getElementById('delayTable'); // Use html2canvas to render the table as an image

      (0, _html2canvas["default"])(table).then(function (canvas) {
        // Réduction de la taille de l'image
        var scale = 0.2;
        var imgWidth = canvas.width * scale;
        var imgHeight = canvas.height * scale; // Conversion du canvas en image PNG

        var imgData = canvas.toDataURL('image/png');
        var yearOption = document.querySelector('#statistic_date option:checked').text;
        var checkedElement = document.querySelector('#statistic_jourcalendar input:checked');
        console.log(checkedElement); // Ajout d'un titre au-dessus du tableau

        var title = 'Délai Activité Annuelle';
        pdf.setFontSize(16);
        pdf.text(title, 60, 60); // Position du titre

        pdf.text(yearOption, 120, 60); // Position du titre
        // Si vous voulez ajouter le texte sélectionné à côté de l'année

        pdf.setFontSize(12); // Ajout de l'image redimensionnée au PDF

        pdf.addImage(imgData, 'PNG', 30, 80, imgWidth, imgHeight);
        var dateEdited = "\xE9dit\xE9 le ".concat(new Date().toLocaleDateString());
        var pageCount = pdf.internal.getNumberOfPages();

        for (var i = 1; i <= pageCount; i++) {
          pdf.setPage(i);
          pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 60, 10);
        } // Enregistrement du PDF


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