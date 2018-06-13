'use strict';

global.$ = require('jquery');
global.jQuery = global.$;
window.$ = global.$;

import { config } from './config.js';
import { loading } from './loading.js';
import { viewportLabel } from './viewport-label.js';
import { linksNewtab } from './links-newtab.js';
import { dropdowns } from './dropdowns.js';
import { nav } from './nav.js';
import { jumpLinks } from './jump-links.js';
import { modals } from './modals.js';
import { scrollSpy } from './scroll-spy.js';
import { menuToggle } from './menu-toggle.js';
import { slickSlideshows } from './slick-slideshows.js';
import { jqueryAccordian } from './jquery-accordian.js';
import { accordian } from './accordian.js';
import { menuOverflow } from './menu-overflow.js';
import { collection } from './collection.js';
import { ecommerceHelpers } from './ecommerce-helpers.js';
import { livereload } from './livereload-client.js';


livereload();

loading(config.loading);
viewportLabel(config.viewportLabel);
linksNewtab(config.linksNewtab);
dropdowns(config.dropdowns);
nav(config.stickyNav);
jumpLinks(config.jumpLinks);
modals(config.modals);
scrollSpy(config.scrollSpy);
menuToggle(config.menuToggle);
slickSlideshows(config.slickSlideshows);
collection();
jqueryAccordian();
accordian();
ecommerceHelpers();
menuOverflow();

//console.log('main.js loaded, with gulp!');
