export default {
    SET_STATS_GRAPHS(state, { request, response }) {
        let new_init = { ...state.init_graphs }
        new_init[request.interval] = true
        state.init_graphs = new_init; // must change the entire object so it triggers compute

        let new_stats = { ...state.stats_graphs }
        new_stats[request.interval] = response
        state.stats_graphs = new_stats

    },
    RESET_STATS_GRAPHS(state, { request }) {
        if (request.interval) {
            state.init_graphs[request.interval] = {};
            state.stats_graphs[request.interval] = {}
        } else {
            state.init = {};
            state.stats_graphs = {}
        }
    },
    SET_STATS_ADV(state, { response }) {
        state.init_adv = true;
        state.stats_adv = response
    },
    RESET_STATS_ADV(state) {
        state.init_adv = false;
        state.stats_adv = {}
    },
}