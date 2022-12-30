<template>
  <div>
    <Header controller="KnytifyStats" />
    <v-card>
      <UtmFraudGraph />
    </v-card>
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import UtmFraudGraph from "./graphs/utm_fraud.vue";

export default {
  name: "App",
  data: function () {
    return {
      tab: "general",
    };
  },
  components: {
    Header,
    UtmFraudGraph,
  },
};
</script>
