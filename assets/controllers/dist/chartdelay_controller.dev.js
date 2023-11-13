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

var labels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
var ctxdelayChart = document.getElementById('delayChart');
var ctxAntenne = document.getElementById('ctxAntenne');
var ctxBudget = document.getElementById('ctxBudget');
var ctxAppro = document.getElementById('ctxAppro');
var ctxFin = document.getElementById('ctxFin');
var ctxPFAF = document.getElementById('ctxPFAF');
var ctxChorus = document.getElementById('ctxChorus');
var ctxTotalDelay = document.getElementById('ctxTotalDelay');

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
      ctxdelayChart.width = 1200; // Définit la largeur du premier canvas

      ctxdelayChart.height = 800; // Définit la hauteur du premier canvas

      new _auto["default"](ctxdelayChart, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Transmission',
            data: transStat,
            borderWidth: 1
          }, {
            label: 'Notification',
            data: notStat,
            borderWidth: 1
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
      new _auto["default"](ctxAntenne, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égal à 3 jours / ' + [achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"]] + "%", 'Supérieur à 3 jours / ' + [achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"]] + "%"],
          datasets: [{
            label: 'Antenne GSBDD',
            data: [achats_delay_all[0]["CountAntInf3"], achats_delay_all[0]["CountAntSup3"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
            hoverOffset: 4
          }],
          options: {
            responsive: false,
            scales: {
              y: {
                beginAtZero: true
              }
            },
            plugins: {
              title: {
                display: true,
                text: 'Custom Chart Title'
              }
            }
          }
        }
      });
      new _auto["default"](ctxBudget, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égal à 3 jours / ' + [achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"]] + "%", 'Supérieur à 3 jours / ' + [achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"]] + "%"],
          datasets: [{
            label: 'Budget',
            data: [achats_delay_all[1]["CountBudgetInf3"], achats_delay_all[1]["CountBudgetSup3"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
      new _auto["default"](ctxAppro, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égal à 7 jours / ' + [achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"]] + "%", 'Supérieur à 7 jours / ' + [achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"]] + "%"],
          datasets: [{
            label: 'PFAF Appro',
            data: [achats_delay_all[2]["CountApproInf7"], achats_delay_all[2]["CountApproSup7"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
      new _auto["default"](ctxFin, {
        type: 'pie',
        data: {
          labels: ['Inférieur à 7 jours / ' + [achats_delay_all[3]["Pourcentage_Delai_Inf_7_Jours_Fin"]] + "%", 'Supérieur à 7 jours / ' + [achats_delay_all[3]["Pourcentage_Delai_Sup_7_Jours_Fin"]] + "%"],
          datasets: [{
            label: 'PFAF Fin',
            data: [achats_delay_all[3]["CountFinInf7"], achats_delay_all[3]["CountFinSup7"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
      new _auto["default"](ctxPFAF, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égale à 14 jours / ' + [achats_delay_all[5]["Pourcentage_Delai_Inf_14_Jours_Pfaf"]] + "%", 'Supérieur à 14 jours / ' + [achats_delay_all[5]["Pourcentage_Delai_Sup_14_Jours_Pfaf"]] + "%"],
          datasets: [{
            label: 'PFAF Fin',
            data: [achats_delay_all[5]["CountPfafInf14"], achats_delay_all[5]["CountPfafSup14"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
      new _auto["default"](ctxChorus, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égale à 14 jours / ' + [achats_delay_all[4]["Pourcentage_Delai_Inf_10_Jours_Chorus"]] + "%", 'Supérieur à 14 jours / ' + [achats_delay_all[4]["Pourcentage_Delai_Sup_10_Jours_Chorus"]] + "%"],
          datasets: [{
            label: 'Chorus Formul.',
            data: [achats_delay_all[4]["CountChorusFormInf10"], achats_delay_all[4]["CountChorusFormSup10"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
      new _auto["default"](ctxTotalDelay, {
        type: 'pie',
        data: {
          labels: ['Inférieur ou égale à 14 jours / ' + [achats_delay_all[0]["Pourcentage_Delai_Inf_15_Jours"]] + "%", 'Supérieur à 14 jours / ' + [achats_delay_all[0]["Pourcentage_Delai_Sup_15_Jours"]] + "%"],
          datasets: [{
            label: 'Délai Total',
            data: [achats_delay_all[0]["CountDelaiTotalInf15"], achats_delay_all[0]["CountDelaiTotalSup15"]],
            backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
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
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;