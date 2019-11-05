import {breakpoints} from "../../constants";

const state = {
    open: window.innerWidth >= breakpoints.xl
};

const getters = {
    isOpen: (state) => state.open
};

const actions = {
    open: ({commit}) => commit('open'),
    close: ({commit}) => commit('close'),
};

const mutations = {
    open: (state) => state.open = true,
    close: (state) => state.open = false,
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}
