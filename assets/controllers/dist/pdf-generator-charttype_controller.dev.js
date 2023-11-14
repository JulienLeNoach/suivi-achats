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
      var canvas1, canvasImage1, canvas2, canvasImage2, tableTotaux, mppaTable, mabcTable, tableTotauxCanvas, mpppaTableCanvas, mabcTableCanvas, tableTotauxImage, mppaTableImage, mabcTableImage, pdf;
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
              tableTotaux = document.getElementById('tableTotaux');
              mppaTable = document.getElementById('mppaTable');
              mabcTable = document.getElementById('mabcTable');
              tableTotaux.style.backgroundColor = "white";
              mppaTable.style.backgroundColor = "white";
              mabcTable.style.backgroundColor = "white";
              _context.next = 14;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(tableTotaux));

            case 14:
              tableTotauxCanvas = _context.sent;
              _context.next = 17;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(mppaTable));

            case 17:
              mpppaTableCanvas = _context.sent;
              _context.next = 20;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(mabcTable));

            case 20:
              mabcTableCanvas = _context.sent;
              tableTotauxImage = tableTotauxCanvas.toDataURL('image/png', 1.0);
              mppaTableImage = mpppaTableCanvas.toDataURL('image/png', 1.0);
              mabcTableImage = mabcTableCanvas.toDataURL('image/png', 1.0);
              pdf = new _jspdf["default"]('p', 'mm', [300, 200]);
              pdf.setFontSize(15);
              pdf.addImage(canvasImage1, 'png', 15, 15, 70, 70);
              pdf.addImage(canvasImage2, 'png', 115, 15, 70, 70);
              pdf.text("Montant total", 15, 115);
              pdf.addImage(tableTotauxImage, 'png', 15, 130, 150, 60);
              pdf.text("Montant des MPPA", 15, 200);
              pdf.addImage(mppaTableImage, 'png', 15, 215, 80, 15);
              pdf.text("Montant des MABC", 100, 200);
              pdf.addImage(mabcTableImage, 'png', 100, 215, 80, 15);
              pdf.setFillColor(106, 106, 244, 1);
              pdf.save('Graphique.pdf');

            case 36:
            case "end":
              return _context.stop();
          }
        }
      });
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;