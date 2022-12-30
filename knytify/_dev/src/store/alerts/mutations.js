export default {
  ADD_ALERT(state, alert) {
    let alert_idx = state.alert_idx;
    state.alerts[alert_idx] = {...alert, alert_idx};
    state.alert_idx += 1;
  },
  HIDE_ALERT(state, { alert_idx }) {
    // console.log("Hide alert", alert_idx);
    if (state.alerts[alert_idx]) {
      state.alerts[alert_idx].show = false;
    }
  },
  REMOVE_ALERT(state, { alert_idx }) {
    // console.log("Remove alert", alert_idx);
    if (state.alerts[alert_idx]) {
      delete state.alerts[alert_idx];
    }
  },
};
