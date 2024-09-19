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
var ctxAntenne = document.getElementById('ctxAntenne');
var ctxBudget = document.getElementById('ctxBudget');
var ctxAppro = document.getElementById('ctxAppro');
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
      new _auto["default"](ctxAntenne, {
        type: 'pie',
        data: {
          labels: ["<= ".concat(delaiTransmissions, " jours / ") + achats_delay_all[0]["Pourcentage_Delai_Inf_3_Jours_Ant"] + "%", "> ".concat(delaiTransmissions, " jours / ") + achats_delay_all[0]["Pourcentage_Delai_Sup_3_Jours_Ant"] + "%"],
          datasets: [{
            label: 'Transmission',
            data: [achats_delay_all[0]["CountAntInf3"], achats_delay_all[0]["CountAntSup3"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: false,
          scales: {
            y: {
              display: false // Désactive l'affichage de l'axe des ordonnées

            }
          },
          plugins: {}
        }
      });
      new _auto["default"](ctxBudget, {
        type: 'pie',
        data: {
          labels: ["<= ".concat(delaiTraitement, " jours / ") + achats_delay_all[1]["Pourcentage_Delai_Inf_3_Jours_Budget"] + "%", "> ".concat(delaiTraitement, " jours / ") + achats_delay_all[1]["Pourcentage_Delai_Sup_3_Jours_Budget"] + "%"],
          datasets: [{
            label: 'Traitement',
            data: [achats_delay_all[1]["CountBudgetInf3"], achats_delay_all[1]["CountBudgetSup3"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: false,
          scales: {
            y: {
              display: false // Désactive l'affichage de l'axe des ordonnées

            }
          }
        }
      });
      new _auto["default"](ctxAppro, {
        type: 'pie',
        data: {
          labels: ["<= ".concat(delaiNotifications, " jours / ") + achats_delay_all[2]["Pourcentage_Delai_Inf_7_Jours_Appro"] + "%", "> ".concat(delaiNotifications, " jours / ") + achats_delay_all[2]["Pourcentage_Delai_Sup_7_Jours_Appro"] + "%"],
          datasets: [{
            label: 'Notification',
            data: [achats_delay_all[2]["CountApproInf7"], achats_delay_all[2]["CountApproSup7"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: false,
          scales: {
            y: {
              display: false // Désactive l'affichage de l'axe des ordonnées

            }
          }
        }
      });
      new _auto["default"](ctxTotalDelay, {
        type: 'pie',
        data: {
          labels: ["<= ".concat(delaiTotal, " jours / ") + achats_delay_all[3]["Pourcentage_Delai_Inf_15_Jours"] + "%", "> ".concat(delaiTotal, " jours / ") + achats_delay_all[3]["Pourcentage_Delai_Sup_15_Jours"] + "%"],
          datasets: [{
            label: 'Délai Total',
            data: [achats_delay_all[3]["CountDelaiTotalInf15"], achats_delay_all[3]["CountDelaiTotalSup15"]],
            backgroundColor: ['rgb(77 104 188)', 'rgb(162 225 228)'],
            hoverOffset: 4
          }]
        },
        options: {
          responsive: false,
          scales: {
            y: {
              display: false // Désactive l'affichage de l'axe des ordonnées

            }
          }
        }
      });
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;