import Api from '../../service/api';

const state = {
    users: [],
    apiCallLoading: true,
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
        return id => getById(id);
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

    if (state.apiCallLoading) {
        usersWatchers.fetchAll = fetchAll;
        return;
    } else {
        delete usersWatchers.fetchAll;
    }

    state.apiCallLoading = true;
    Api().get('users')
        .then(response => {
            updateUsers(response.data);
        })
        .catch(reason => {
            state.users = [];
            state.list.lastError = reason;
        }).finally(() => {
        state.list.loading = false;
        state.apiCallLoading = false;
    });
};

const getById = function (id) {
    state.get.loading = true;
    state.get.lastError = null;
    state.get.data = null;

    if (state.apiCallLoading) {
        usersWatchers.getById = getById;
        return state.get.data;
    } else {
        delete usersWatchers.getById;
    }

    for (let user of state.users) {
        if (id === user.id) {
            state.get.loading = false;
            return user;
        }
    }

    state.apiCallLoading = true;
    Api().get('users/' + id)
        .then(response => {
            updateUsers(state.users.push(response.data));
        })
        .catch(reason => {
            state.get.lastError = reason;
        }).finally(() => {
        state.get.loading = false;
        state.apiCallLoading = false;
    });
};

let usersWatchers = {};

const updateUsers = function (data) {
    state.users = data;
    state.list.loading = false;

    for (let key in usersWatchers) {
        // noinspection JSUnfilteredForInLoop
        usersWatchers[key].call();
    }
};

