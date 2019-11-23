import Api from '../../service/Api';
import Vue from 'vue';

/**
 * Helper object to manage the loading state on concurrent requests
 *
 * @type {{add(): *, counter: number, queue: [], remove(*): *}}
 */
const loadingSet = {
    counter: 0,
    queue: [],
    add() {
        const ticket = loadingSet.counter++;
        loadingSet.queue.push(ticket);
        return ticket;
    },
    remove(ticket) {
        const idx = loadingSet.queue.findIndex(number => number === ticket);
        if (idx) {
            loadingSet.queue.splice(idx, 1);
        }

        return loadingSet.queue.length;
    }
};


/**
 * Returns namespaced vuex module with CRUD methods of the given resource
 *
 * @param {string} resource
 * @returns {{mutations: *, state: *, getters: *, actions: *, namespaced: boolean}}
 */
export default function getStore(resource) {
    const state = {
        data: [],
        loading: false,
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
            Vue.set(state.data, idx, obj);
        },

        delete(state, id) {
            const idx = getIdxById(id);
            state.data.splice(idx, 1);
        },

        addLoading(state) {
            state.loading = true;
            return loadingSet.add();
        },

        removeLoading(state, ticket) {
            const loadingCounter = loadingSet.remove(ticket);
            if (0 === loadingCounter) {
                state.loading = false;
            }
        }
    };


    const actions = {
        load({commit, state}, refresh = false) {
            if (state.data.length && !refresh) {
                return new Promise(resolve => resolve());
            }

            const ticket = commit('addLoading');
            return Api().get(resource)
                .then(response => response.data)
                .then(data => commit('setAll', data))
                .finally(() => commit('removeLoading', ticket));
        },

        add({commit, state}, payload) {
            const ticket = commit('addLoading');
            return Api().post(resource, payload)
                .then(response => response.data)
                .then(data => {
                    commit('add', data);
                    return data;
                })
                .finally(() => commit('removeLoading', ticket));
        },

        update({commit}, payload) {
            const ticket = commit('addLoading');
            return Api().put(`${resource}/${payload.id}`, payload)
                .then(response => response.data)
                .then(data => {
                    commit('update', data);
                    return data;
                })
                .finally(() => commit('removeLoading', ticket));
        },

        delete({commit}, payload) {
            const ticket = commit('addLoading');
            return Api().delete(`${resource}/${payload.id}`)
                .then(() => commit('delete', payload.id))
                .finally(() => commit('removeLoading', ticket));
        },
    };


    const getIdxById = function (id) {
        return state.data.findIndex(data => data.id === id);
    };


    return {
        namespaced: true,
        state,
        getters,
        actions,
        mutations,
    };
}
