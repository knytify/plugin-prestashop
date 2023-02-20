import api from "../../api"

const links = window.knytify.links;

const getUser = async (context, args = {}) => {
    context.commit("RESET_KNYTIFY_USER");
    return api.get(links.account + "&api_key=" + (args.api_key ?? '')).then((res) => {
        context.commit("SET_KNYTIFY_USER", res.data);
        return res.data;
    });
}

const login = async (context, data) => {
    return api.post(links.account_login, data).then((res) => {
        return res.data;
    });
}

const setup = async (context, data) => {
    return api.post(links.account_setup, data).then((res) => {
        return res.data;
    });
}

export default {
    getUser,
    login,
    setup,
}