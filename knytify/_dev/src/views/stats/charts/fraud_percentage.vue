<template>
  <div class="cnt">
    <div class="title">{{ $t("fraudpercentage") }}</div>
    <div class="chart-container">
      <div v-if="init">
        <apexchart
          v-if="availableData"
          height="330"
          type="donut"
          :options="labels_and_options"
          :series="getData"
        ></apexchart>
        <div v-else class="card-centered">
          {{ $t("nodata") }}
        </div>
      </div>
      <div v-else class="card-centered">
        <v-progress-circular
          :size="70"
          :width="7"
          color="blue"
          indeterminate
        ></v-progress-circular>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
.cnt {
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
}
.title {
  font-size: 16px;
  text-align: center;
  margin: 10px;
  text-decoration: un;
}
</style>

<script>
import { mapState } from "vuex";
import { numberDisplayFormatter } from "@/helper/number.js";

export default {
  data() {
    return {
      interval: "daily",
    };
  },
  computed: {
    ...mapState({
      init: (state) => state.stats.init_recap,
      stats: (state) => state.stats.stats_recap,
    }),
    availableData() {
      const ts = this.stats.fraud_timeline.y;
      const key = Object.keys(ts)[0];

      if (ts[key].length == 0) {
        return false;
      }

      for (var i = 0; i < ts[key].length; i++) {
        if (ts[key][i] != 0) {
          return true;
        }
      }
      return false;
    },
    getData() {
      let stats = this.stats.fraud_timeline;
      var num_sessions = 0;
      var num_fraud = 0;
      for (var i = 0; i < stats.x.length; i++) {
        num_sessions += stats.y.num_sessions[i];
        num_fraud += stats.y["fraud"][i];
      }
      return [num_fraud, num_sessions];
    },
    labels_and_options() {
      let option = {
        chart: {
          toolbar: {
            show: true,
            tools: {
              download: true,
              selection: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              pan: false,
              reset: false,
            },
          },
        },
        dataLabels: { enabled: false },
        expandOnClick: true,
        stroke: { show: true, width: 25, colors: "#fff" },
        colors: ["#e2a03f", "#5c1ac3", "#e7515a"],
        legend: {
          show: false,
        },
        plotOptions: {
          pie: {
            donut: {
              size: "65%",
              background: "transparent",
              labels: {
                show: true,
                value: {
                  show: true,
                  fontFamily: "Arial, sans-serif",
                  formatter: numberDisplayFormatter,
                },
                total: {
                  show: true,
                  label: this.$t("percentage"),
                  color: "#888ea8",
                  fontSize: "20px",
                  formatter: function (w) {
                    var percentage =
                      (w.globals.seriesTotals[0] /
                        (w.globals.seriesTotals[1] +
                          w.globals.seriesTotals[0])) *
                      100;
                    return percentage.toFixed(2) + "%";
                  },
                },
              },
            },
          },
        },
        labels: [this.$t("frauds"), this.$t("cleansessions")],
      };

      return option;
    },
  },
  mounted() {},
  methods: {},
};
</script>
