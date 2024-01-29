"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

var _fullcalendar = require("fullcalendar");

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
    key: "initialize",
    value: function initialize() {
      this.initCustomCalendar();
    }
  }, {
    key: "initCustomCalendar",
    value: function initCustomCalendar() {
      var calendarEl = document.getElementById('calendar'); // const calendar = new Calendar(calendarEl, {
      //   initialView: 'dayGridMonth'
      // })

      var calendar = new _fullcalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        timeZone: 'Europe/Paris',
        firstDay: 1,
        headerToolbar: {
          start: 'prev,next today',
          center: 'title',
          end: 'dayGridYear,dayGridMonth,timeGridWeek'
        },
        events: events,
        // Assurez-vous que 'events' est défini quelque part dans votre code
        eventContent: function eventContent(info) {
          // Votre logique personnalisée pour eventContent
          var dateStr = info.event.start.toISOString().slice(0, 10);
          var tdElts = document.querySelectorAll('td[data-date="' + dateStr + '"]');

          for (var i = 0; i < tdElts.length; i++) {
            tdElts[i].classList.add('mark');
          }

          return {
            html: '<style> td[data-date="' + info.event.start.toISOString().slice(0, 10) + '"]{background-color:' + info.backgroundColor + ';}</style>'
          };
        },
        dayCellDidMount: function dayCellDidMount(info) {
          if (info.date.getUTCDay() === 6 || info.date.getUTCDay() === 0) {
            // 6 = Samedi, 0 = Dimanche
            info.el.style.backgroundColor = 'red';
          }
        }
      });
      calendar.render();
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;