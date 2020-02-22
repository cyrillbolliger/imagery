import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import snackbar from "./modules/snackbar";
import user from './modules/user';
import resource from "./modules/resource";
import groups from "./modules/groups";
import counter from "./modules/counter";
import legal from "./modules/legal";
import logos from "./modules/logos";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        counter,
        menu,
        snackbar,
        user,
        users: resource('users'),
        groups,
        logosManageable: logos('manageable'),
        logosUsable: logos('usable'),
        legal,
    }
});
