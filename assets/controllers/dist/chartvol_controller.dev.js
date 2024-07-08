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
      var ctx = document.getElementById('myChart');
      var labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']; // Données existantes

      var chartDataCountCurrentMppa = JSON.parse(document.querySelector('input[name="chartDataCountCurrent"]').value).mppa;
      var chartDataCountCurrentMabc = JSON.parse(document.querySelector('input[name="chartDataCountCurrent"]').value).mabc;
      var chartDataTotalCurrentMppa = JSON.parse(document.querySelector('input[name="chartDataTotalCurrent"]').value).mppa;
      var chartDataTotalCurrentMabc = JSON.parse(document.querySelector('input[name="chartDataTotalCurrent"]').value).mabc;
      var chartDataCountPreviousMppa = JSON.parse(document.querySelector('input[name="chartDataCountPrevious"]').value).mppa;
      var chartDataCountPreviousMabc = JSON.parse(document.querySelector('input[name="chartDataCountPrevious"]').value).mabc;
      var chartDataTotalPreviousMppa = JSON.parse(document.querySelector('input[name="chartDataTotalPrevious"]').value).mppa;
      var chartDataTotalPreviousMabc = JSON.parse(document.querySelector('input[name="chartDataTotalPrevious"]').value).mabc; // Calcul du tableau cumulatif pour l'année en cours

      var cumulativeTotalsCurrent = [];
      var cumulativeTotalCurrent = 0;
      chartDataTotalCurrentMppa.forEach(function (valueMppa, index) {
        var valueMabc = chartDataTotalCurrentMabc[index];
        cumulativeTotalCurrent += valueMppa + valueMabc;
        cumulativeTotalsCurrent.push(parseFloat(cumulativeTotalCurrent.toFixed(2))); // Limiter à deux chiffres après la virgule
      }); // Calcul du tableau cumulatif pour l'année précédente

      var cumulativeTotalsPrevious = [];
      var cumulativeTotalPrevious = 0;
      chartDataTotalPreviousMppa.forEach(function (valueMppa, index) {
        var valueMabc = chartDataTotalPreviousMabc[index];
        cumulativeTotalPrevious += valueMppa + valueMabc;
        cumulativeTotalsPrevious.push(parseFloat(cumulativeTotalPrevious.toFixed(2))); // Limiter à deux chiffres après la virgule
      });
      new _auto["default"](ctx, {
        data: {
          labels: labels,
          datasets: [{
            type: 'bar',
            label: 'MPPA Count (Current Year)',
            data: chartDataCountCurrentMppa,
            yAxisID: 'y1',
            order: 4,
            // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgb(77, 104, 188)',
            borderColor: 'rgb(77, 104, 188)'
          }, {
            type: 'bar',
            label: 'MABC Count (Current Year)',
            data: chartDataCountCurrentMabc,
            yAxisID: 'y1',
            order: 4,
            // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgb(162, 225, 228)',
            borderColor: 'rgb(162, 225, 228)'
          }, {
            type: 'bar',
            label: 'MPPA Count (Previous Year)',
            data: chartDataCountPreviousMppa,
            yAxisID: 'y1',
            order: 3,
            // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgba(77, 104, 188, 0.5)',
            borderColor: 'rgba(77, 104, 188, 0.5)'
          }, {
            type: 'bar',
            label: 'MABC Count (Previous Year)',
            data: chartDataCountPreviousMabc,
            yAxisID: 'y1',
            order: 3,
            // Barres seront en dessous
            borderWidth: 1,
            backgroundColor: 'rgba(162, 225, 228, 0.5)',
            borderColor: 'rgba(162, 225, 228, 0.5)'
          }, {
            type: 'line',
            label: 'Cumulative Total (Current Year)',
            data: cumulativeTotalsCurrent,
            yAxisID: 'y',
            order: 2,
            // Lignes seront au-dessus
            borderWidth: 2,
            borderColor: 'rgb(54, 74, 137)',
            backgroundColor: 'rgba(54, 74, 137, 0.2)',
            fill: false
          }, {
            type: 'line',
            label: 'Cumulative Total (Previous Year)',
            data: cumulativeTotalsPrevious,
            yAxisID: 'y',
            order: 1,
            // Lignes seront au-dessus
            borderWidth: 2,
            borderColor: 'rgba(54, 74, 137, 0.5)',
            backgroundColor: 'rgba(54, 74, 137, 0.2)',
            fill: false
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              position: 'left',
              title: {
                display: true,
                text: 'Total Amount'
              }
            },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: {
                drawOnChartArea: false
              },
              title: {
                display: true,
                text: 'Count'
              }
            }
          },
          plugins: {
            legend: {
              position: 'top',
              align: 'end',
              labels: {
                boxWidth: 20
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