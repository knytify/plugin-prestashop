<template>
  <div>
    <ps-billing-customer
      v-if="billingContext.user.email"
      ref="psBillingCustomerRef"
      :context="billingContext"
      :onOpenModal="openBillingModal"
      :hideInvoiceList="true"
    />
    <ps-billing-modal
      v-if="modalType !== ''"
      :context="billingContext"
      :type="modalType"
      :onCloseModal="closeBillingModal"
    />
    <div v-if="sub && sub.id">Subscribed</div>
    <Header controller="KnytifyConfiguration"/>
    <v-card>
      <v-tabs>
        <v-tab @click="tab = 'general'">General</v-tab>
        <v-tab @click="tab = 'utm'">UTM source</v-tab>
      </v-tabs>
      <General v-if="tab == 'general'" />
      <UTM v-else-if="tab == 'utm'" />
    </v-card>
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import General from "./General.vue";
import UTM from "./UTM.vue";
import {
  CustomerComponent,
  ModalContainerComponent,
} from "@prestashopcorp/billing-cdc/dist/bundle.umd";

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
    PsBillingCustomer: CustomerComponent,
    PsBillingModal: ModalContainerComponent,
    Header,
    General,
    UTM,
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