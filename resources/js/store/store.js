import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import snackbar from "./modules/snackbar";
import user from './modules/user';
import resource from "./modules/resource";
import groups from "./modules/groups";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        menu,
        snackbar,
        user,
        users: resource('users'),
        groups,
        logos: resource('logos'),
    }
});
