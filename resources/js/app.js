/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import 'bootstrap-material-design-icons/scss/material-icons.scss';

window.Vue = require('vue');
import VueRouter from 'vue-router';
import App from './components/App';
import i18n from './i18n'
import {store} from './store/store';
import {routes} from './routes';

/**
 * Register global components
 */
import NavShowButton from "./components/misc/NavShowButton";

window.Vue.component('NavShowButton', NavShowButton);

/**
 *  Routing
 */
window.Vue.use(VueRouter);

const router = new VueRouter({
    routes,
    mode: 'history'
});

/**
 * Initialize Vue
 */
const app = new Vue({
    el: '#app',
    render: h => h(App),
    store,
    router,
    i18n,
});
