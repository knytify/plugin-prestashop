<template>
  <div>
    <div class="mb-3" v-if="billingContext.user.email">
      <Header controller="KnytifyConfiguration" />
      <v-card>
        <v-tabs>
          <v-tab @click="tab = 'general'">General</v-tab>
          <v-tab @click="tab = 'utm'">UTM source</v-tab>
          <v-tab @click="tab = 'subscription'">Subscription</v-tab>
        </v-tabs>
        <General v-if="tab == 'general'" />
        <UTM v-else-if="tab == 'utm'" />
        <Subscription v-if="tab == 'subscription'" />
      </v-card>
    </div>

    <div v-else>
      <Subscription />
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
  data: function () {
    return {
      tab: "general",
      billingContext: { ...window.psBillingContext.context },
      modalType: "",
      sub: null,
    };
  },
  components: {
    Header,
    General,
    UTM,
    Subscription,
  },
  provide() {
    return {
      emailSupport: window.psBillingContext.context.user.emailSupport,
    };
  },
  methods: {
    openBillingModal(type, data) {
      this.modalType = type;
      this.billingContext = { ...this.billingContext, ...data };
    },
    closeBillingModal(data) {
      this.modalType = "";
      this.$refs.psBillingCustomerRef.parent.updateProps({
        context: {
          ...this.billingContext,
          ...data,
        },
      });
    },
    eventHookHandler(type, data) {
      switch (type) {
        case EVENT_HOOK_TYPE.BILLING_INITIALIZED:
          // data structure is: { customer, subscription }
          console.log("Billing initialized", data);
          this.sub = data.subscription;
          break;
        case EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
          // data structure is: { customer, subscription, card }
          console.log("Sub updated", data);
          this.sub = data.subscription;
          break;
        case EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:
          // data structure is: { customer, subscription }
          console.log("Sub cancelled", data);
          this.sub = data.subscription;
          break;
      }
    },
  },
};
</script>