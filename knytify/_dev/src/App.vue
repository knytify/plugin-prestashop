<template>
  <div>
    <Alerts />
    <prestashop-accounts
      v-if="!this.getAccountsVue().isOnboardingCompleted()"
    ></prestashop-accounts>
    <KnytifyStats v-else-if="controller == 'KnytifyStats'" />
    <KnytifyConfiguration v-else-if="controller == 'KnytifyConfiguration'" />
  </div>
</template>


<script>
import Alerts from "./views/components/Alerts.vue";
import KnytifyConfiguration from "./views/configuration/Configuration.vue";
import KnytifyStats from "./views/stats/Stats.vue";

export default {
  name: "App",

  mounted() {
    this.getAccountsVue().init();
  },
  methods: {
    getAccountsVue() {
      return (
        // window.psaccountsVue would already exist because it is loaded from the CDN (urlAccountsCdn).
        // calling its init() method loads the <prestashop-accounts/> object from the DOM.
        // Using its async component does not work nor show any error message.
        window.psaccountsVue ?? require("prestashop_accounts_vue_components")
      );
    },
  },
  data: function () {
    return {
      psAccount: { ...window.contextPsAccounts },
      controller: window.help_class_name, // Prestashop defined
    };
  },
  components: {
    KnytifyConfiguration,
    KnytifyStats,
    Alerts,
  },
};
</script>

<style>
#knytify-app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
