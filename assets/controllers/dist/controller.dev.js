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

var isChartInitialized = false;

var default_1 =
/*#__PURE__*/
function (_Controller) {
  _inherits(default_1, _Controller);

  function default_1() {
    var _this;

    _classCallCheck(this, default_1);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(default_1).apply(this, arguments));
    _this.chart = null;
    return _this;
  }

  _createClass(default_1, [{
    key: "connect",
    value: function connect() {
      if (!isChartInitialized) {
        isChartInitialized = true;
        this.dispatchEvent('init', {
          Chart: _auto["default"]
        });
      }

      if (!(this.element instanceof HTMLCanvasElement)) {
        throw new Error('Invalid element');
      }

      var payload = this.viewValue;

      if (Array.isArray(payload.options) && 0 === payload.options.length) {
        payload.options = {};
      }

      this.dispatchEvent('pre-connect', {
        options: payload.options,
        config: payload
      });
      var canvasContext = this.element.getContext('2d');

      if (!canvasContext) {
        throw new Error('Could not getContext() from Element');
      }

      console.log(canvasContext);
      console.log(payload);
      this.chart = new _auto["default"](canvasContext, payload); // this.dispatchEvent('connect', { chart: this.chart });
    }
  }, {
    key: "viewValueChanged",
    value: function viewValueChanged() {
      if (this.chart) {
        this.chart.data = this.viewValue.data;
        this.chart.options = this.viewValue.options;
        this.chart.update();
        var parentElement = this.element.parentElement;

        if (parentElement && this.chart.options.responsive) {
          var originalWidth = parentElement.style.width;
          parentElement.style.width = parentElement.offsetWidth + 1 + 'px';
          setTimeout(function () {
            parentElement.style.width = originalWidth;
          }, 0);
        }
      }
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(name, payload) {
      this.dispatch(name, {
        detail: payload,
        prefix: 'chartjs'
      });
    }
  }]);

  return default_1;
}(_stimulus.Controller);

exports["default"] = default_1;
default_1.values = {
  view: Object
};