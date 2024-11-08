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

var ctxMppa = document.getElementById('mppaMountChart');
var ctxMabc = document.getElementById('mabcMountChart');
var ctxallMount = document.getElementById('allMountChart');

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
      console.log(parameter0);
      new _auto["default"](ctxMppa, {
        type: 'pie',
        data: {
          labels: ['X <=' + parameter0, parameter0 + ' < X <=' + parameter1, parameter1 + '< X <=' + parameter2, parameter2 + ' < X'],
          datasets: [{
            label: 'Montant des MPPA',
            data: [result_achats_mounts[0]["nombre_achats_inf_four1"], result_achats_mounts[0]["nombre_achats_four1_four2"], result_achats_mounts[0]["nombre_achats_four2_four3"], result_achats_mounts[0]["nombre_achats_sup_four3"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(68 196 201)', 'rgb(128, 174, 190)', 'rgb(238 222 182)'],
            hoverOffset: 4,
            datalabels: {
              formatter: function formatter(value, context) {
                return (value / result_achats[0]["nombre_achats_type_1"] * 100).toFixed(1) + '%';
              },
              color: 'black',
              // Couleur du texte du pourcentage
              align: 'start',
              // Alignement du texte
              offset: -10 // Décalage du texte par rapport au point

            }
          }],
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
      new _auto["default"](ctxMabc, {
        type: 'pie',
        data: {
          labels: ['X <=' + parameter0, parameter0 + ' < X <=' + parameter1, parameter1 + '< X <=' + parameter2, parameter2 + ' < X'],
          datasets: [{
            label: 'Montant des MABC',
            data: [result_achats_mounts[1]["nombre_achats_inf_four1"], result_achats_mounts[1]["nombre_achats_four1_four2"], result_achats_mounts[1]["nombre_achats_four2_four3"], result_achats_mounts[1]["nombre_achats_sup_four3"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(68 196 201)', 'rgb(128, 174, 190)', 'rgb(238 222 182)'],
            hoverOffset: 4,
            datalabels: {
              formatter: function formatter(value, context) {
                return (value / result_achats[1]["nombre_achats_type_0"] * 100).toFixed(1) + '%';
              },
              color: 'black',
              // Couleur du texte du pourcentage
              align: 'start',
              // Alignement du texte
              offset: -10 // Décalage du texte par rapport au point

            }
          }],
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
      new _auto["default"](ctxallMount, {
        type: 'pie',
        data: {
          labels: ['X <=' + parameter0, parameter0 + ' < X <=' + parameter1, parameter1 + '< X <=' + parameter2, parameter2 + ' < X'],
          datasets: [{
            label: 'Montant des MPPA + MABC',
            data: [result_achats_mounts[1]["nombre_achats_inf_four1"] + result_achats_mounts[0]["nombre_achats_inf_four1"], result_achats_mounts[1]["nombre_achats_four1_four2"] + result_achats_mounts[0]["nombre_achats_four1_four2"], result_achats_mounts[1]["nombre_achats_four2_four3"] + result_achats_mounts[0]["nombre_achats_four2_four3"], result_achats_mounts[1]["nombre_achats_sup_four3"] + result_achats_mounts[0]["nombre_achats_sup_four3"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(68 196 201)', 'rgb(128, 174, 190)', 'rgb(238 222 182)'],
            hoverOffset: 4,
            datalabels: {
              formatter: function formatter(value, context) {
                return (value / (result_achats[0]["nombre_achats_type_1"] + result_achats[1]["nombre_achats_type_0"]) * 100).toFixed(1) + '%';
              },
              color: 'black',
              // Couleur du texte du pourcentage
              align: 'start',
              // Alignement du texte
              offset: -10 // Décalage du texte par rapport au point

            }
          }],
          options: {
            responsive: true,
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