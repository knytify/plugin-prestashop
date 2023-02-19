import api from "../../api"

const links = window.knytify.links;

const getUser = async (context) => {
    context.commit("RESET_KNYTIFY_USER");
    return api.get(links.account).then((res) => {
        context.commit("SET_KNYTIFY_USER", res.data);
        return res.data;
    });
}

export default {
    getUser
}