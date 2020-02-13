import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import snackbar from "./modules/snackbar";
import user from './modules/user';
import resource from "./modules/resource";
import groups from "./modules/groups";
import counter from "./modules/counter";
import legal from "./modules/legal"

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        counter,
        menu,
        snackbar,
        user,
        users: resource('users'),
        groups,
        logos: resource('logos'),
        legal,
    }
});
