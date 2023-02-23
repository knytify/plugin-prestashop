<template>
  <div>
    <Alerts />

    <!-- Once the account association / subscription are done, we will show these as a configuration tab instead -->
    <div v-if="page == 'setup'">
      <!-- Step 1: Prestashop account association -->
      <prestashop-accounts
        v-if="!this.getAccountsVue().isOnboardingCompleted()"
      ></prestashop-accounts>

      <!-- Step 2: Subscription. On the webhook, knytify will create an account if it does not exist. -->
      <Subscription
        v-if="
          this.getAccountsVue().isOnboardingCompleted() &&
          !psBillingContext?.context?.user?.email
        "
      />

      <!-- Step 3: Knytify account association. We need to store the api key to communicate further. -->
      <AccountAssociation :email="psBillingContext?.context?.user?.email" />
    </div>

    <div v-else-if="page == 'wrong_api_key'">
      <p>It seems like your API key is not valid any more.</p>
      <p>Please, update it</p>
      <p><v-btn @click.prevent="page = 'setup'">Update configuration</v-btn></p>
    </div>

    <KnytifyConfiguration v-else-if="page == 'configuration'" />
    <KnytifyStats v-else-if="page == 'stats'" />
  </div>
</template>


<script>
import Alerts from "./views/components/Alerts.vue";
import Header from "./views/components/Header.vue";
import Subscription from "./views/configuration/PrestashopSubscription.vue";
import AccountAssociation from "./views/association/AccountAssociation.vue";
import KnytifyConfiguration from "./views/configuration/Configuration.vue";
import KnytifyStats from "./views/stats/Stats.vue";

export default {
  name: "App",
  created() {
    this.$store.dispatch("knytify_account/getUser").catch((err) => {
      if (err.response.status == 401 && this.page != "setup") {
        this.page = "wrong_api_key";
      }
    });
  },
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
      page: window.knytify.page,
      psBillingContext: window.psBillingContext,
      psAccount: { ...window.contextPsAccounts },
      controller: window.help_class_name, // Prestashop defined
    };
  },
  components: {
    Alerts,
    Header,
    Subscription,
    AccountAssociation,
    KnytifyConfiguration,
    KnytifyStats,
  },
};
</script>

<style lang="scss">
#knytify-app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;

  .title-3 {
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: 600;
  }
}
</style>
