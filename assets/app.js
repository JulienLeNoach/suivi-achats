/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import { startStimulusApp } from '@symfony/stimulus-bridge';

// any CSS you import will output into a single css file (app.css in this case)

import '../node_modules/@gouvfr/dsfr/dist/dsfr.min.css'
import './styles/app.css';
// import './less/header.less';
import './less/search.less';
// import './less/calendar.less';
// import './less/role.less';
// import './less/stat.less';



// start the Stimulus application


// import './calendar.js'
// import './search_achat.js'


import './bootstrap';
import '@symfony/ux-chartjs';

import '../node_modules/@gouvfr/dsfr/dist/dsfr.module.js'
import '../node_modules/@gouvfr/dsfr/dist/dsfr.nomodule.js'
// Registers Stimulus controllers from controllers.json and in the controllers/ directory





