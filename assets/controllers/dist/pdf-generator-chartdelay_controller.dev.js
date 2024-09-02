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
    key: "downloadgraphPie",
    // downloadgraphBar() {
    //   const canvas = document.getElementById('delayChart');
    //   const criteriaForm = criteria; 
    //   canvas.fillStyle = "white";
    //   const canvasImage = canvas.toDataURL('image/png', 1.0);
    //   const values = Object.entries(criteriaForm)
    //   .filter(([key, value]) => value !== null && value !== undefined)
    //   .map(([key, value]) => `${key}: ${value}`);
    //   const criteriaText = values.join(', ');
    //   let pdf = new jsPDF('p', 'mm', 'a4');
    //   pdf.setFontSize(8);
    //   pdf.text("Critères de sélection : " + criteriaText, 15, 5);
    //   pdf.setFontSize(15);
    //   pdf.text('Délai d\'activité annuelle', 15, 15);
    //   pdf.addImage(canvasImage, 'png', 15, 20, 180, 150);
    //   pdf.setFillColor(106, 106, 244, 1);
    //   const dateEdited = `édité le ${new Date().toLocaleDateString()}`;
    //   const pageCount = pdf.internal.getNumberOfPages();
    //   pdf.setFontSize(8);
    //   for (let i = 1; i <= pageCount; i++) {
    //       pdf.setPage(i);
    //       pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 5);
    //   }
    //   pdf.save(`Graphique en bar d'activité annuelle ${dateEdited} .pdf`);
    // }
    value: function downloadgraphPie() {
      var ctxAntenne = document.getElementById('ctxAntenne');
      var ctxBudget = document.getElementById('ctxBudget');
      var ctxAppro = document.getElementById('ctxAppro'); // const ctxFin = document.getElementById('ctxFin');
      // const ctxPFAF = document.getElementById('ctxPFAF');
      // const ctxChorus = document.getElementById('ctxChorus');

      var ctxTotalDelay = document.getElementById('ctxTotalDelay');
      var criteriaForm = criteria;
      ctxAntenne.fillStyle = "white";
      ctxBudget.fillStyle = "white";
      ctxAppro.fillStyle = "white"; // ctxFin.fillStyle = "white";
      // ctxPFAF.fillStyle = "white";
      // ctxChorus.fillStyle = "white";

      ctxTotalDelay.fillStyle = "white";
      var ctxAntenneImage = ctxAntenne.toDataURL('image/png', 1.0);
      var ctxBudgetImage = ctxBudget.toDataURL('image/png', 1.0);
      var ctxApproImage = ctxAppro.toDataURL('image/png', 1.0); // const ctxFinImage = ctxFin.toDataURL('image/png', 1.0);
      // const ctxPFAFImage = ctxPFAF.toDataURL('image/png', 1.0);
      // const ctxChorusImage = ctxChorus.toDataURL('image/png', 1.0);

      var ctxTotalDelayImage = ctxTotalDelay.toDataURL('image/png', 1.0);
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

      var titles = ['Transmission', 'Traitement', 'Notification', 'Délai total'];
      var images = [ctxAntenneImage, ctxBudgetImage, ctxApproImage, ctxTotalDelayImage];
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
      pdf.save("Graphique en pie d'activit\xE9 annuelle ".concat(dateEdited, " .pdf"));
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;