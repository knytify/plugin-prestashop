export default {
  addAlert(context, { title, text, icon, timeout, level }) {
    timeout = timeout !== undefined ? timeout : 5000;
    const alert_idx = context.state.alert_idx;
    context.commit("ADD_ALERT", {
      title,
      text,
      icon,
      timeout,
      level,
      show: true,
    });
    if (timeout) {
      setTimeout(() => {
        context.dispatch("hideAlert", { alert_idx });
      }, timeout);
      setTimeout(() => {
        context.dispatch("removeAlert", { alert_idx });
      }, timeout + 5000);
    }
  },
  hideAlert(context, { alert_idx }) {
    context.commit("HIDE_ALERT", { alert_idx });
  },
  removeAlert(context, { alert_idx }) {
    context.commit("REMOVE_ALERT", { alert_idx });
  },
};
