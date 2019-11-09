import Api from '../../service/api';

const state = {
    users: [],
    list: {
        get data() {
            return state.users
        },
        lastError: null,
        loading: true
    },
};

const getters = {
    get(state, id) {
    },
    list(state) {
        if (!state.list.data.length) {
            fetchAll();
        }

        return state.list;
    }
};

const actions = {
    delete({commit}, id) {
    },
    update({commit}, id) {
    },
    add({commit}, payload) {
    },
};

const mutations = {};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}

const fetchAll = function () {
    state.list.loading = true;
    state.list.lastError = null;

    Api().get('users')
        .then(response => {
            state.users = response.data;
        })
        .catch(reason => {
            state.users = [];
            state.list.lastError = reason;
        }).finally(() => {
        state.list.loading = false;
    });
};
