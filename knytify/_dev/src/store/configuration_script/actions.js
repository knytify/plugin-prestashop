import api from "../../api"

const links = window.knytify.links;


const setScriptConfig = async (context, configuration) => {
    return api.post(links.configuration_script_set, configuration)
}

const getScriptConfig = async (context) => {
    context.commit("RESET_CONFIGURATION_SCRIPT");
    return api.get(links.configuration_script_get).then((res) => {
        context.commit("SET_CONFIGURATION_SCRIPT", res.data);
        return res.data;
    });
}

export default {
    setScriptConfig,
    getScriptConfig
}