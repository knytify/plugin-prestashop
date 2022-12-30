import api from "../../api"
import { dictToQueryString, updateQueryStringParameters } from "../../helper/url.js";

const links = window.knytify.links;


const getStatsGraphs = async (context, args) => {
    context.commit('RESET_STATS_GRAPHS', { request: args });
    return api.get(links.stats_graphs, args)
        .then(res => {
            context.commit('SET_STATS_GRAPHS', { request: args, response: res.data });
            return res.data;
        })
}

const getStatsAdvanced = async (context, args = {}) => {
    const url = updateQueryStringParameters(links.stats_getStatsAdvanced, args)
    context.commit('RESET_STATS_ADV', { request: args });
    return api.get(url)
        .then(res => {
            context.commit('SET_STATS_ADV', { request: args, response: res.data });
            return res.data;
        })
}

export default {
    getStatsGraphs,
    getStatsAdvanced,
}