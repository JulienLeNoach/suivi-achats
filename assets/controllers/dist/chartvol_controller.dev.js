"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

var _auto = _interopRequireDefault(require("chart.js/auto"));

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

var ctx = document.getElementById('myChart');
var ctx2 = document.getElementById('myChart2');
var labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

var _default =
/*#__PURE__*/
function (_Controller) {
  _inherits(_default, _Controller);

  function _default() {
    _classCallCheck(this, _default);

    return _possibleConstructorReturn(this, _getPrototypeOf(_default).apply(this, arguments));
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      // ctx.width = 1200; // Définit la largeur du premier canvas
      // ctx.height = 800; // Définit la hauteur du premier canvas
      // ctx2.width = 1200; // Définit la largeur du deuxième canvas
      // ctx2.height = 800;
      new _auto["default"](ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'MPPA',
            data: datasets1,
            borderWidth: 1,
            backgroundColor: 'rgb(77 104 188)',
            borderColor: 'rgb(77 104 188)'
          }, {
            label: 'MABC',
            data: datasets2,
            borderWidth: 1,
            backgroundColor: 'rgb(162 225 228)',
            borderColor: 'rgb(162 225 228)'
          }],
          options: {
            responsive: true,
            // maintainAspectRatio: false, // Désactive la mise à l'échelle automatique
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
      new _auto["default"](ctx2, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'MPPA',
            data: datasets3,
            borderWidth: 1,
            backgroundColor: 'rgb(77 104 188)',
            borderColor: 'rgb(77 104 188)'
          }, {
            label: 'MABC',
            data: datasets4,
            borderWidth: 1,
            backgroundColor: 'rgb(162 225 228)',
            borderColor: 'rgb(162 225 228)'
          }],
          options: {
            responsive: true,
            // maintainAspectRatio: false, // Désactive la mise à l'échelle automatique
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;