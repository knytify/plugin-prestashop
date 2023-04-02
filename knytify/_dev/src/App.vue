<template>
  <div>
    <Alerts />

    <div v-if="!ready" class="d-flex justify-center">
      <v-progress-circular
        style="margin-top: 100px"
        :size="70"
        :width="7"
        color="blue"
        indeterminate
      ></v-progress-circular>
    </div>

    <!-- Once the account association / subscription are done, we will show these as a configuration tab instead -->
    <div v-else-if="page == 'setup'">
      <!-- Step 1: Prestashop account association -->
      <prestashop-accounts></prestashop-accounts>

      <!-- Step 2: Subscription. On the webhook, knytify will create an account if it does not exist. -->
      <Subscription
        class="mt-4"
        :accountAssotiation="false"
        v-if="getAccountsVue().isOnboardingCompleted()"
      />

      <!-- Step 3: Knytify account association. We need to store the api key to communicate further. -->
      <AccountAssociation
        class="mt-4"
        :email="psBillingContext?.context?.user?.email"
        v-if="
          getAccountsVue().isOnboardingCompleted() &&
          psBillingContext?.context?.user?.email &&
          active_ps_subscription
        "
      />
    </div>

    <div v-else-if="page == 'wrong_api_key'">
      <v-card class="pa-3">
        <p>It seems like your API key is not valid any more.</p>
        <p>Please, update it, by (re)assotiating your Knytify account.</p>
        <br />
        <p>
          <v-btn @click.prevent="page = 'setup'">Update configuration</v-btn>
        </p>
      </v-card>
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
  data: function () {
    return {
      page: window.knytify.page,
      ready: false,
      psBillingContext: window.psBillingContext,
      psAccount: { ...window.contextPsAccounts },
      controller: window.help_class_name, // Prestashop defined
      active_ps_subscription: window.knytify.active_ps_subscription,
    };
  },
  created() {
    if (this.page == "setup" || this.page == "wrong_api_key") {
      this.ready = true;
    } else {
      this.$store
        .dispatch("knytify_account/getUser")
        .then(() => {
          this.ready = true;
        })
        .catch((err) => {
          this.ready = true;
          if (err.response.status == 401) {
            // The api key is not valid
            this.page = "wrong_api_key";
          } else if (err.response.status == 400) {
            // The user has no PS account associated/subscription anymore.
            this.page = "setup";
            this.$nextTick().then(() => {
              // Next tick = After DOM is rendered
              this.getAccountsVue().init();
            });
          }
        });
    }
  },
  mounted() {
    if (this.page == "setup") {
      this.getAccountsVue().init();
    }
    console.log(this.page);
  },
  methods: {
    getAccountsVue() {
      return (
        // window.psaccountsVue would alpsAccountsReady exist because it is loaded from the CDN (urlAccountsCdn).
        // calling its init() method loads the <prestashop-accounts/> object from the DOM.
        // Using its async component does not work nor show any error message.
        window.psaccountsVue ?? require("prestashop_accounts_vue_components")
      );
    },
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
