import api from "../../api"

const links = window.knytify.links;

const setConfig = async (context, configuration) => {
    return api.post(links.configuration_set, configuration)
}

const getConfig = async (context) => {
    context.commit("RESET_CONFIGURATION");
    return api.get(links.configuration_get).then((res) => {
        context.commit("SET_CONFIGURATION", res.data);
        return res.data;
    });
}


export default {
    setConfig,
    getConfig,
}