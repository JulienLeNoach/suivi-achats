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

var ctxactAppro = document.getElementById('actAppro');
var ctxtopVal = document.getElementById('topVal');
var ctxtopVol = document.getElementById('topVol');
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
      var dataValues = [];
      var dataValues2 = [];
      var dataValues3 = [];
      result_achatsSum.forEach(function (achats) {
        dataValues.push(achats["nombre_total_achats_pme"]);
      });
      result_achatsSumVol.forEach(function (achats) {
        dataValues2.push({
          key: achats["departement"],
          value: achats["total_nombre_achats"]
        });
      });
      result_achatsSumVal.forEach(function (achats) {
        dataValues3.push({
          key: achats["departement"],
          value: achats["somme_montant_achat"]
        });
      });
      new _auto["default"](ctxactAppro, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'NB PME',
            data: dataValues,
            backgroundColor: ['rgb(206,5,0)'],
            hoverOffset: 4
          }],
          options: {
            responsive: true,
            // maintainAspectRatio:false,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
      var labelsVal = [];
      var dataVal = [];
      dataValues3.forEach(function (item) {
        labelsVal.push(item.key);
        dataVal.push(item.value);
      });
      new _auto["default"](ctxtopVal, {
        type: 'bar',
        data: {
          labels: labelsVal,
          datasets: [{
            label: 'Valeur PME',
            data: dataVal,
            backgroundColor: ['rgb(251,231,105)'],
            hoverOffset: 4
          }],
          options: {
            responsive: false,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        }
      });
      console.log(dataValues2);
      var labelsVol = [];
      var dataVol = [];
      dataValues2.forEach(function (item) {
        labelsVol.push(item.key);
        dataVol.push(item.value);
      });
      new _auto["default"](ctxtopVol, {
        type: 'bar',
        data: {
          labels: labelsVol,
          datasets: [{
            label: 'Volume PME',
            data: dataVol,
            backgroundColor: ['rgb(169,251,104)'],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;