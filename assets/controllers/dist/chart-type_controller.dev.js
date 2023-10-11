// import { Controller } from '@hotwired/stimulus';
// import { Chart } from 'chart.js';
// import ChartDataLabels from 'chartjs-plugin-datalabels';
// export default class extends Controller {
//     connect() {
//         this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
//         this.element.addEventListener('chartjs:connect', this._onConnect);
//         document.addEventListener('chartjs:init', function (event) {
//             const Chart = event.detail.Chart;
//             Chart.register(ChartDataLabels);
//         });
//     }
//     disconnect() {
//         // You should always remove listeners when the controller is disconnected to avoid side effects
//         this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
//         this.element.removeEventListener('chartjs:connect', this._onConnect);
//     }
//     _onPreConnect(event) {
//         // The chart is not yet created
//         // You can access the config that will be passed to "new Chart()"
//         // event.detail.config.type = 'line';
//         // For instance you can format Y axis
//         event.detail.config.options.scales = {
//             yAxes: [
//                 {
//                     ticks: {
//                         callback: function (value, index, values) {
//                             /* ... */
//                         },
//                     },
//                 },
//             ],
//         };
//     }
//     _onLineButtonClick(event) {
//         console.log(this.testTarget.dataset["symfony-UxChartjs-ChartViewValue"]);
//         let originalObject = JSON.parse(this.testTarget.dataset["symfony-UxChartjs-ChartViewValue"]);
//         let copiedObject = JSON.parse(JSON.stringify(originalObject));
//         copiedObject.type = 'line';
//         this.testTarget.dataset["symfony-UxChartjs-ChartViewValue"] = JSON.stringify(copiedObject);
//         console.log(this.testTarget.dataset["symfony-UxChartjs-ChartViewValue"]);
//     }
//     _onConnect(event) {
//         // The chart was just created
//         // console.log(event.detail.chart); // You can access the chart instance using the event details
//         // For instance you can listen to additional events
//         event.detail.chart.options.onHover = (mouseEvent) => {
//         };
//         event.detail.chart.options.onClick = (mouseEvent) => {
//         };
//     }
// }
"use strict";