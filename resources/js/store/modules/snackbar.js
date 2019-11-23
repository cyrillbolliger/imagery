const state = {
    queue: [],
    id: 1,
};

const getters = {
    queue: state => state.queue,
};

const mutations = {
    push(state, snackbar) {
        state.queue.push(snackbar);
    },
    remove(state, id) {
        const idx = state.queue.findIndex(snackbar => snackbar.id === id);
        state.queue.splice(idx, 1);
    }
};

const actions = {
    push(context, snackbar) {
        snackbar.setId(state.id++);
        context.commit('push', snackbar);

        return snackbar.launch()
            .then(() => context.commit('remove', snackbar.id));
    },

    dismiss({commit}, snackbar) {
        commit('remove', snackbar.id);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}
