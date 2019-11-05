const state = {
    id: null,
    object: null,
};

const getters = {
    id: (state) => state.id,
    object: (state) => state.object,
    auth: (state) => state.id !== null,
    isAdmin: (state) => state.object ? state.object.is_admin : false,
};

const actions = {
    set: ({commit}, payload) => commit('set', payload),
};

const mutations = {
    set(state, payload) {
        if (payload) {
            state.id = payload.id;
            state.object = payload;
        } else {
            state.id = null;
            state.object = null;
        }
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}
