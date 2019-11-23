import {isXl} from "../../service/Window";

const state = {
    open: isXl()
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
