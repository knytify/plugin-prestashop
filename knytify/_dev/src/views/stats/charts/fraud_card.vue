<template>
  <div class="cnt">
    <div class="title">{{ $t("fraudanalytics") }}</div>
    <div class="chart-container">
      <div v-if="init">
        <apexchart
          v-if="availableData"
          height="305"
          type="area"
          :options="options"
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

<style lang="scss" scoped>
.cnt {
  width: 100%;
  height: 100%;
  min-width: 100%;
  min-height: 100%;
}
.title {
  font-size: 16px;
  text-align: center;
  margin: 8px;
  text-decoration: undefined;
}
</style>

<script>
import { mapState } from "vuex";
import { numberDisplayFormatter } from "@/helper/number.js";
import { formatForChartFromStr } from "@/helper/date.js";
import { CHART_COLORS_BY_NAME } from "./config";

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
      return (
        this.stats.fraud_timeline.y.num_sessions.reduce(
          (partialSum, a) => partialSum + a,
          0
        ) > 0
      );
    },
    getData() {
      let stats = this.stats.fraud_timeline;
      var timeseries = [];
      Object.keys(stats.y)
        .sort()
        .forEach((ts_name) => {
          let ts = stats.y[ts_name];
          timeseries.push({
            name: this.$t(ts_name),
            data: ts,
            color: CHART_COLORS_BY_NAME[ts_name],
          });
        });
      return timeseries;
    },
    options() {
      return {
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
        stroke: { show: true, width: 2 },
        // colors: Object.keys(this.stats.fraud_timeline.y)
        //   .sort()
        //   .forEach((ts_name) => CHART_COLORS_BY_NAME[ts_name]),
        xaxis: {
          tooltip: {
            enabled: false,
          },
          categories: this.stats.fraud_timeline.x.map((v) =>
            formatForChartFromStr(v)
          ),
          axisBorder: { show: false },
          labels: {
            offsetX: 0,
            offsetY: 5,
            style: {
              fontSize: "11px",
            },
          },
        },
        yaxis: {
          tickAmount: 7,
          labels: {
            formatter: numberDisplayFormatter,
            offsetX: -10,
            offsetY: 0,
            style: {
              fontSize: "11px",
            },
          },
        },
        grid: {
          borderColor: "#e0e6ed",
          strokeDashArray: 5,
          xaxis: { lines: { show: true } },
          yaxis: { lines: { show: false } },
          padding: { top: -10, right: 10, bottom: -10, left: 0 },
        },
        legend: {
          position: "top",
          horizontalAlign: "center",
          offsetY: 0,
          fontSize: "11px",
          markers: {
            width: 8,
            height: 8,
            strokeWidth: 0,
            strokeColor: "#fff",
            radius: 10,
            offsetX: 0,
            offsetY: 0,
          },
          itemMargin: { horizontal: 20, vertical: 2 },
        },
        fill: {
          type: "gradient",
          gradient: {
            type: "vertical",
            shadeIntensity: 1,
            inverseColors: 0,
            opacityFrom: 0.28,
            opacityTo: 0.05,
            stops: [45, 100],
          },
        },
      };
    },
  },
};
</script>
