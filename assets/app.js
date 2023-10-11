/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import { startStimulusApp } from '@symfony/stimulus-bridge';

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/header.less';
import './less/search.less';
import './styles/calendar.css';
import './less/calendar.less';
import './less/role.less';
import './less/stat.less';
import './less/visu_achat.less';




// start the Stimulus application

import './bootstrap';
import './search_achat.js';
import './calendar.js';
import './role.js';
// Registers Stimulus controllers from controllers.json and in the controllers/ directory





