const state = {
    counter: 0
};

const getters = {
    get: state => state.counter,
};

const actions = {
    increment({commit}) {
        commit('increment');
    },
};

const mutations = {
    increment: (state) => state.counter++,
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}
