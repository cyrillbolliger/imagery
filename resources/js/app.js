/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import 'bootstrap-material-design-icons/scss/material-icons.scss';
import 'vue2-animate/src/sass/vue2-animate.scss'
import 'vue-search-select/dist/VueSearchSelect.css'

import Vue from 'vue';
import VueRouter from 'vue-router';
import App from './components/App';
import i18n from './i18n'
import Vuelidate from 'vuelidate'
import {VueMasonryPlugin} from 'vue-masonry';
import {store} from './store/store';
import {routes} from './routes';

/**
 * Register global components
 */
import AIconNamed from "./components/atoms/AIconNamed";

Vue.component('AIconNamed', AIconNamed);

/**
 *  Routing
 */
Vue.use(VueRouter);

const router = new VueRouter({
    routes,
    mode: 'history'
});

/**
 * Other Plugins
 */
Vue.use(Vuelidate);
Vue.use(VueMasonryPlugin);

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

/**
 * inject the current user
 */
app.$store.dispatch('user/set', user);
