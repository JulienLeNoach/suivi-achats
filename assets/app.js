/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import { startStimulusApp } from '@symfony/stimulus-bridge';
import Chart from 'chart.js/auto';
import Calendar from 'fullcalendar';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(ChartDataLabels);

Chart.defaults.set('plugins.datalabels', {
    color: 'black',
    anchor: 'end',
    // size: '20',
    font: {
        size:16,
      },
      align:'top',
      display: function(context) {
        return context.dataset.data[context.dataIndex] > 1; // or >= 1 or ...
     }
  });
// any CSS you import will output into a single css file (app.css in this case)

import './less/search.less';
import '../node_modules/@gouvfr/dsfr/dist/dsfr.min.css'
import '../node_modules/@gouvfr/dsfr/dist/utility/icons/icons.css';
import './styles/app.css';


    
// start the Stimulus application





import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../node_modules/@gouvfr/dsfr/dist/dsfr.module.js'
// Registers Stimulus controllers from controllers.json and in the controllers/ directory





