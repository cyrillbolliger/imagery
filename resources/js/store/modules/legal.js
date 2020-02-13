const initialState = {
    personality: 'unknown',
    people: true,
    originatorType: null,
    stockUrl: '',
    licence: null,
    originator: '',
    rightToUse: false,
    rightToShare: false,
};

const state = {
    personality: initialState.personality,
    people: initialState.people,
    originatorType: initialState.originatorType,
    stockUrl: initialState.stockUrl,
    licence: initialState.licence,
    originator: initialState.originator,
    rightToUse: initialState.rightToUse,
    rightToShare: initialState.rightToShare,
};

const getters = {
    get: state => key => state[key],
};

const mutations = {
    update(state, payload) {
        state[payload.key] = payload.value;
    },
    reset(state) {
        for (const [key, value] of Object.entries(initialState)) {
            state[key] = value;
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
}
