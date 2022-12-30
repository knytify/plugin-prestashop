<template>
  <div id="alerts-cnt">
    <div v-for="alert in alerts" v-bind:key="alert.alert_idx">
      <transition name="fade">
        <v-alert
          v-if="alert.show"
          :value="alert.alert_idx"
          dismissible
          :icon="getIcon(alert.level)"
          :color="getColor(alert.level)"
          elevation="2"
          colored-border
          :title="alert.title"
          closable
          @click="hideAlert(alert.alert_idx)"
          >{{ alert.text }}</v-alert
        >
      </transition>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  name: "Alerts",

  data: function () {
    return {};
  },

  methods: {
    hideAlert(alert_idx) {
      console.log("Hiding", alert_idx);
      this.$store.dispatch("alerts/hideAlert", { alert_idx });
    },
    getColor(level) {
      if (level === "info") {
        return "blue";
      } else if (level === "warning") {
        return "yellow";
      } else if (level === "error") {
        return "red";
      } else if (level === "success") {
        return "green";
      } else {
        return "blue";
      }
    },
    getIcon(level) {
      if (level === "info") {
        return "mdi-information";
      } else if (level === "warning") {
        return "mdi-exclamation";
      } else if (level === "error") {
        return "mdi-exclamation-thick";
      } else if (level === "success") {
        return "mdi-check-bold";
      } else {
        return "mdi-information";
      }
    },
  },

  computed: {
    ...mapState({
      alerts: (state) => state.alerts.alerts,
    }),
  },
};
</script>

<style scoped>
#alerts-cnt {
  position: fixed;
  bottom: 50px; /**Enough space for Prestashop debug bar */
  right: 10px;
  z-index: 99999;
}
#alerts-cnt .v-alert {
  margin-top: 10px;
  max-width: 400px;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
