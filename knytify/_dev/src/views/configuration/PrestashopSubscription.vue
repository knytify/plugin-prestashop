<template>
  <div style="min-height: 150px">
    <div class="mb-4">
      <prestashop-accounts></prestashop-accounts>
    </div>
    <PsBillingCustomer
      ref="psBillingCustomerRef"
      :context="billingContext"
      :onOpenModal="openBillingModal"
      :hideInvoiceList="true"
    />
    <teleport to="body">
      <!-- Regardless of the z-index, if we leave the modal here, it will be partially hidden. -->
      <PsBillingModal
        v-if="modalType !== ''"
        :context="billingContext"
        :type="modalType"
        :onCloseModal="closeBillingModal"
      />
    </teleport>
  </div>
</template>

<script>
import {
  CustomerComponent,
  ModalContainerComponent,
} from "@prestashopcorp/billing-cdc/dist/bundle.umd";
import { Vue } from "vue";

export default {
  mounted() {
    (
      window.psaccountsVue ?? require("prestashop_accounts_vue_components")
    ).init();
  },
  data() {
    return {
      billingContext: { ...window.psBillingContext.context },
      modalType: "",
      sub: null,
    };
  },
  provide() {
    return {
      emailSupport: window.psBillingContext.context.user.emailSupport,
    };
  },
  components: {
    PsBillingCustomer: CustomerComponent.driver("vue3", Vue),
    PsBillingModal: ModalContainerComponent.driver("vue3", Vue),
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
  name: "Subscription",
};
</script>