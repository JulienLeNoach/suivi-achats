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
      var canvas1, canvasImage1, canvas2, canvasImage2, canvas3, canvasImage3, volvalTable, actApproTable, criteriaForm, volvalTableCanvas, actApproTableCanvas, volvalTableImage, actApproImage, values, criteriaText, pdf, dateEdited, pageCount, i;
      return regeneratorRuntime.async(function downloadgraphBar$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              canvas1 = document.getElementById('topVal');
              canvas1.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage1 = canvas1.toDataURL('image/png', 1.0);
              canvas2 = document.getElementById('topVol');
              canvas2.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage2 = canvas2.toDataURL('image/png', 1.0);
              canvas3 = document.getElementById('actAppro');
              canvas3.style.backgroundColor = "white"; // Assurez-vous que le fond est blanc

              canvasImage3 = canvas3.toDataURL('image/png', 1.0);
              volvalTable = document.getElementById('volvalTable');
              actApproTable = document.getElementById('actApproTable');
              criteriaForm = criteria;
              volvalTable.style.backgroundColor = "white";
              actApproTable.style.backgroundColor = "white";
              _context.next = 16;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(volvalTable));

            case 16:
              volvalTableCanvas = _context.sent;
              _context.next = 19;
              return regeneratorRuntime.awrap((0, _html2canvas["default"])(actApproTable));

            case 19:
              actApproTableCanvas = _context.sent;
              volvalTableImage = volvalTableCanvas.toDataURL('image/png', 1.0);
              actApproImage = actApproTableCanvas.toDataURL('image/png', 1.0);
              values = Object.entries(criteriaForm).filter(function (_ref) {
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
              criteriaText = values.join(', ');
              pdf = new _jspdf["default"]('l', 'mm', 'a4');
              pdf.setFontSize(10);
              pdf.text("Critères de sélection : " + criteriaText, 15, 10);
              pdf.setFontSize(15);
              pdf.text("Top 5 Département MPPA PME en valeur", 10, 25); // Titre pour 'canvasImage1'

              pdf.addImage(canvasImage1, 'png', 10, 25, 130, 35);
              pdf.text("Top 5 Département MPPA PME en volume", 150, 25); // Titre pour 'canvasImage2'

              pdf.addImage(canvasImage2, 'png', 150, 25, 140, 35);
              pdf.text("Activité appro PME en valeur", 110, 70);
              pdf.addImage(canvasImage3, 'png', 10, 75, 260, 35);
              pdf.setFillColor(106, 106, 244, 1);
              pdf.setFontSize(10);
              dateEdited = "\xE9dit\xE9 le ".concat(new Date().toLocaleDateString());
              pageCount = pdf.internal.getNumberOfPages();

              for (i = 1; i <= pageCount; i++) {
                50;
                pdf.setPage(i);
                pdf.text(dateEdited, pdf.internal.pageSize.getWidth() - 30, 10);
              }

              pdf.save("Graphique statistique PME ".concat(dateEdited, " .pdf"));

            case 40:
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