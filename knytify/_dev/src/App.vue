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

      <div v-if="!psAccountsReady" class="d-flex justify-center">
        <v-progress-circular
          style="margin-top: 100px"
          :size="70"
          :width="7"
          color="blue"
          indeterminate
        ></v-progress-circular>
      </div>

      <!-- Step 2: Subscription. On the webhook, knytify will create an account if it does not exist. -->
      <Subscription
        class="mt-4"
        v-if="getAccountsVue().isOnboardingCompleted()"
      />

      <!-- Step 3: Knytify account association. We need to store the api key to communicate further. -->
      <AccountAssociation
        :email="psBillingContext?.context?.user?.email"
        v-if="
          getAccountsVue().isOnboardingCompleted() &&
          psBillingContext?.context?.user?.email
        "
      />
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
  data: function () {
    return {
      page: window.knytify.page,
      ready: false,
      psAccountsReady: false,
      psBillingContext: window.psBillingContext,
      psAccount: { ...window.contextPsAccounts },
      controller: window.help_class_name, // Prestashop defined
    };
  },
  created() {
    this.$store
      .dispatch("knytify_account/getUser")
      .then(() => {
        this.ready = true;
      })
      .catch((err) => {
        this.ready = true;
        if (this.page != "setup" && this.page != "wrong_api_key") {
          if (err.response.status == 401) {
            // The api key is not valid
            this.page = "wrong_api_key";
          } else if (err.response.status == 400) {
            // The user has no PS account associated/subscription anymore.
            this.page = "setup";
            this.$nextTick().then(() => {
              // Next tick = After DOM is rendered
              this.getAccountsVue().init();
              this.psAccountsReady = true;
            });
          }
        }
      });
  },
  mounted() {
    if (this.page == "setup") {
      this.getAccountsVue().init();
      this.psAccountsReady = true;
    }
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
