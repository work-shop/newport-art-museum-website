'use strict';

import { config } from './config.js';
import { loading } from './loading.js';
import { linksNewtab } from './links-newtab.js';
import { stickyNav } from './sticky-nav.js';
import { jumpLinks } from './jump-links.js';
import { modals } from './modals.js';
import { scrollSpy } from './scroll-spy.js';
import { menuToggle } from './menu-toggle.js';
import { slickSlideshows } from './slick-slideshows.js';

loading(config.loading);
linksNewtab(config.linksNewtab);
stickyNav(config.stickyNav);
jumpLinks(config.jumpLinks);
modals(config.modals);
scrollSpy(config.scrollSpy);
menuToggle(config.menuToggle);
slickSlideshows(config.slickSlideshows);

console.log('main.js loaded');
