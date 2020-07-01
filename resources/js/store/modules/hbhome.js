
const state = {
    currentUser: null,
};

const mutations = {
    SET_CURRENT_USER: (state, currentUser) => {
        state.currentUser = currentUser;
    },
};

const actions = {
    setCurrentUser({ commit }, currentUser) {
        commit('SET_CURRENT_USER', currentUser);
    },
};
const getters = {
    currentUser: (state) => state.currentUser,
};
export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};
