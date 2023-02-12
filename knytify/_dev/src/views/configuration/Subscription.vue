<template>
  <div>
    <PsBillingCustomer
      ref="psBillingCustomerRef"
      :context="billingContext"
      :onOpenModal="openBillingModal"
      :hideInvoiceList="true"
    />
    <PsBillingModal
      v-if="modalType !== ''"
      :context="billingContext"
      :type="modalType"
      :onCloseModal="closeBillingModal"
    />
  </div>
</template>

<script>
import {
  CustomerComponent,
  ModalContainerComponent,
} from "@prestashopcorp/billing-cdc/dist/bundle.umd";
import { Vue } from "vue";


export default {
  data() {
    return {
      form: {
        utm: {},
      },
    };
  },
  components: {
    PsBillingCustomer: CustomerComponent.driver("vue3", Vue),
    PsBillingModal: ModalContainerComponent.driver("vue3", Vue),
  },
  watch: {
    init() {
      this.reset();
    },
  },
  methods: {},
  name: "Configuration",
};
</script>