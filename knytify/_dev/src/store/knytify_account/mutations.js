export default {
    SET_KNYTIFY_USER(state, data) {
        state.init = true;
        state.email = data.email;
    },
    RESET_KNYTIFY_USER(state) {
        state.init = false;
        state.email = null;
    },
};
