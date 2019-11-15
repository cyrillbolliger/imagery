import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import snackbar from "./modules/snackbar";
import user from './modules/user';
import resource from "./modules/resource";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        menu,
        snackbar,
        user,
        users: resource('users'),
        groups: resource('groups'),
        logos: resource('logos'),
    }
});
