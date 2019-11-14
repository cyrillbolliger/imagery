import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import user from './modules/user';
import crudRessource from "./modules/crudRessource";

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        menu,
        user,
        users: crudRessource('users')
    }
});
