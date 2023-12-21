"use strict";

var _stimulusBridge = require("@symfony/stimulus-bridge");

var _auto = _interopRequireDefault(require("chart.js/auto"));

var _fullcalendar = _interopRequireDefault(require("fullcalendar"));

var _chartjsPluginDatalabels = _interopRequireDefault(require("chartjs-plugin-datalabels"));

require("./less/search.less");

require("../node_modules/@gouvfr/dsfr/dist/dsfr.min.css");

require("../node_modules/@gouvfr/dsfr/dist/utility/icons/icons.css");

require("./styles/app.css");

require("./bootstrap");

require("bootstrap/dist/css/bootstrap.min.css");

require("../node_modules/@gouvfr/dsfr/dist/dsfr.module.js");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
_auto["default"].register(_chartjsPluginDatalabels["default"]);

_auto["default"].defaults.set('plugins.datalabels', {
  color: 'black',
  anchor: 'end',
  // size: '20',
  font: {
    size: 16
  },
  align: 'top',
  display: function display(context) {
    return context.dataset.data[context.dataIndex] > 1; // or >= 1 or ...
  }
}); // any CSS you import will output into a single css file (app.css in this case)
// Registers Stimulus controllers from controllers.json and in the controllers/ directory