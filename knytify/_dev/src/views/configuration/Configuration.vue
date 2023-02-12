<template>
  <div>
    <div v-if="psAccount.user.email">
      <div class="mb-3" v-if="psBillingContext.user.email">
        <Header controller="KnytifyConfiguration" />
        <v-card>
          <v-tabs>
            <v-tab @click="tab = 'general'">General</v-tab>
            <v-tab @click="tab = 'utm'">UTM source</v-tab>
            <v-tab @click="tab = 'subscription'">Subscription</v-tab>
          </v-tabs>
          <General v-if="tab == 'general'" />
          <UTM v-else-if="tab == 'utm'" />
          <Subscription
            v-if="tab == 'subscription'"
          />
        </v-card>
      </div>

      <div v-else>
        <Subscription />
      </div>
    </div>
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import General from "./General.vue";
import UTM from "./UTM.vue";
import Subscription from "./Subscription.vue";

export default {
  name: "App",
  created() {},
  data: function () {
    console.log("PS Account", window.contextPsAccounts);
    console.log("PS Billing", window.psBillingContext.context);
    return {
      tab: "general",
      psAccount: { ...window.contextPsAccounts },
      psBillingContext: { ...window.psBillingContext.context },
    };
  },
  components: {
    Header,
    General,
    UTM,
    Subscription,
  },
};
</script>