/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
import App from './components/App';
import i18n from './i18n'
import {store} from './store/store';
import {routes} from './routes';

/**
 *  Routing
 */
window.Vue.use(VueRouter);

const router = new VueRouter({
    routes,
    mode: 'history'
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    render: h => h(App),
    store,
    router,
    i18n,
});
