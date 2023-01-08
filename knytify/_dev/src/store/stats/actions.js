import api from "../../api"
import { dictToQueryString, updateQueryStringParameters } from "../../helper/url.js";

const links = window.knytify.links;


const getStatsRecap = async (context, args) => {
    context.commit('RESET_STATS_RECAP', { request: args });
    return api.get(links.stats_recap, args)
        .then(res => {
            context.commit('SET_STATS_RECAP', { request: args, response: res.data });
            return res.data;
        })
}

const getStatsAdvanced = async (context, args = {}) => {
    const url = updateQueryStringParameters(links.stats_getStatsAdvanced, args)
    context.commit('RESET_STATS_ADVANCED', { request: args });
    return api.get(url)
        .then(res => {
            context.commit('SET_STATS_ADVANCED', { request: args, response: res.data });
            return res.data;
        })
}

export default {
    getStatsRecap,
    getStatsAdvanced,
}