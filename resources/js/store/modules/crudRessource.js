import Api from '../../service/Api';

const state = {
    data: [],
    loading: false
};

const getters = {
    getAll: state => state.data,
    getById: state => (id) => {
        const matches = state.data.filter(obj => obj.id === id);
        return 1 === matches.length ? matches[0] : null;
    },
    loading: state => state.loading,
};

const mutations = {
    setAll(state, data) {
        state.data = data;
    },
    add(state, obj) {
        state.data.push(obj);
    },
    update(state, obj) {
        const idx = getIdxById(obj.id);
        state.data[idx] = obj;
    },
    delete(state, id) {
        const idx = getIdxById(id);
        state.data.splice(idx, 1);
    }
};

const actions = {
    load({commit, state}) {
        state.loading = true;
        return Api().get('users')
            .then(response => response.data)
            .then(data => commit('setAll', data))
            .finally(() => state.loading = false);
    },
    add({commit, state}, payload) {
        state.loading = true;
        return Api().post('users', payload)
            .then(response => response.data)
            .then(data => commit('add', data))
            .finally(() => state.loading = false);
    },
    update({commit}, payload) {
        state.loading = true;
        return Api().put(`users/${payload.id}`, payload)
            .then(response => response.data)
            .then(data => commit('update', data))
            .finally(() => state.loading = false);
    },
    delete({commit}, payload) {
        state.loading = true;
        return Api().delete(`users/${payload.id}`)
            .then(() => commit('delete', payload.id))
            .finally(() => state.loading = false);
    },
};

const getIdxById = function (id) {
    return state.data.findIndex(data => data.id === id);
};

const store = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

let resourcePath = '';

/**
 * Returns namespaced vuex module with CRUD methods of the given resource
 *
 * @param {String} resource
 * @returns {{mutations: *, state: *, getters: *, actions: *, namespaced: boolean}}
 */
export default function getStore(resource) {
    resourcePath = resource;
    return store;
}
