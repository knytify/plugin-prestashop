<template>
  <div>
    <Header controller="KnytifyStats" />
    <div v-if="!err" class="cards d-flex flex-wrap">
      <v-card height="350" width="650">
        <FraudCard />
      </v-card>
      <v-card height="350" width="350">
        <FraudPercentage />
      </v-card>
      <v-card height="350" width="350">
        <ComparisonChart dimension="fraud_by_source" />
      </v-card>
      <v-card height="350" width="350">
        <ComparisonChart dimension="fraud_by_campaign" />
      </v-card>
      <v-card height="350" width="350">
        <ComparisonChart dimension="fraud_by_device" />
      </v-card>
      <v-card height="350" width="350">
        <ComparisonChart dimension="fraud_by_reason" :translate-labels="true" />
      </v-card>
    </div>
    <div v-else>
      <v-card height="350" width="650">
        An error happened while recovering the stats. <br />
        If this happens, it can mean that your Knytify Api Key is not properly
        linked to your shop, or that your Knytify account setup is not complete.
        <br />
        You can try to set up your account again. <br />
        <v-btn :href="link_setup">Set Up Knytify account</v-btn>
      </v-card>
    </div>
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import FraudCard from "./charts/fraud_card.vue";
import FraudPercentage from "./charts/fraud_percentage.vue";
import ComparisonChart from "./charts/comparison.vue";

export default {
  name: "Stats",
  data() {
    return {
      err: false,
    };
  },
  created() {
    this.$store.dispatch("stats/getStatsRecap").catch((err) => {
      this.err = true;
    });
  },
  data: function () {
    return {
      link_setup: window.knytify.links.account_setup,
      tab: "general",
    };
  },
  components: {
    Header,
    FraudCard,
    FraudPercentage,
    ComparisonChart,
  },
};
</script>


<style scoped>
.v-card {
  margin-bottom: 20px;
  margin-right: 20px;
}
.chart-container {
  margin: 0;
}
</style>

<style>
.card-centered {
  display: flex;
  flex-direction: row;
  justify-content: center;
  position: absolute;
  top: 40px;
  left: 0;
  right: 0;
  bottom: 0;
  align-items: center;
  font-size: 22px;
  color: gray;
  pointer-events: none;
  user-select: none;
}
</style>