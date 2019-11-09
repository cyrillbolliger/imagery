import Vue from 'vue';
import Vuex from 'vuex';

import menu from './modules/menu';
import user from './modules/user';
import users from './modules/users';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        menu,
        user,
        users
    }
});
