export default {
    SET_CONFIGURATION_SCRIPT(state, configuration) {
        state.init = true;
        state.configuration = configuration;
    },
    RESET_CONFIGURATION_SCRIPT(state) {
        state.init = false;
        state.configuration = {};
    },
};
