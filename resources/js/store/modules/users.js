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
    get: {
        data: null,
        lastError: null,
        loading: true
    }
};

const getters = {
    get() {
        return id => {
            if (!id) {
                return state.get.data;
            }

            if (!state.get.data || state.get.data.id !== id) {
                getById(id);
            }

            return state.get.data;
        };
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

    return Api().get('users')
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

const getById = function (id) {
    state.get.loading = true;
    state.get.lastError = null;
    state.get.data = null;

    for (let user of state.users) {
        if (id === user.id) {
            state.get.loading = false;
            state.get.data = user;
            return;
        }
    }

    Api().get('users/' + id)
        .then(response => {
            state.get.data = response.data;
            fetchAll();
        })
        .catch(reason => {
            state.get.lastError = reason;
        }).finally(() => {
        state.get.loading = false;
    });
};
