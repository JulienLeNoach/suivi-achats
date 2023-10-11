"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.app = void 0;

var _stimulusBridge = require("@symfony/stimulus-bridge");

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
var app = (0, _stimulusBridge.startStimulusApp)(require.context('@symfony/stimulus-bridge/lazy-controller-loader!./controllers', true, /\.[jt]sx?$/)); // register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

exports.app = app;