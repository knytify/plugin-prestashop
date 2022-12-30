import { createStore } from "vuex";

import configuration from "./configuration/index";
import configuration_script from "./configuration_script/index";
import stats from "./stats/index";
import alerts from "./alerts/index";

export default createStore({
  state() {
    return {};
  },
  mutations: {},
  getters: {},
  actions: {},
  modules: {
    configuration,
    configuration_script,
    stats,
    alerts
  },
});
