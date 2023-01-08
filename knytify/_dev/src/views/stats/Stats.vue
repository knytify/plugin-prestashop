<template>
  <div>
    <Header controller="KnytifyStats" />
    <div class="cards d-flex flex-wrap">
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
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import FraudCard from "./charts/fraud_card.vue";
import FraudPercentage from "./charts/fraud_percentage.vue";
import ComparisonChart from "./charts/comparison.vue";

export default {
  name: "Stats",
  created() {
    this.$store.dispatch("stats/getStatsRecap");
  },
  data: function () {
    return {
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
.chart-container{
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