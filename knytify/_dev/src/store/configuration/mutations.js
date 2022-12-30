export default {
    SET_CONFIGURATION(state, configuration) {
        state.init = true;
        state.configuration = configuration;
    },
    RESET_CONFIGURATION(state) {
        state.init = false;
        state.configuration = {};
    },
};
