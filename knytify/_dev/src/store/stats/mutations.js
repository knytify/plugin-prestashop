export default {
    SET_STATS_RECAP(state, { request, response }) {
        state.init_recap = true
        state.stats_recap = response
    },
    RESET_STATS_RECAP(state, { request }) {
        state.init_recap = false;
        state.stats_recap = {};
    },
    SET_STATS_ADVANCED(state, { response }) {
        state.init_adv = true;
        state.stats_adv = response
    },
    RESET_STATS_ADVANCED(state) {
        state.init_adv = false;
        state.stats_adv = {}
    },
}