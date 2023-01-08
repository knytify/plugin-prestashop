<template>
  <div class="cnt">
    <div class="title">{{ title }}</div>
    <div class="chart-container">
      <div v-if="init">
        <apexchart
          v-if="availableData"
          height="290"
          type="bar"
          :options="optionsComputed"
          :series="data"
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

<script>
import { GOOD_BAD_COLORS } from "../config";

export default {
  created() {},
  props: {
    horizontal: {
      type: Boolean,
      default: true,
    },
    init: {
      type: Boolean,
      default: false,
    },
    labels: {
      type: Array,
      default: [],
    },
    data: {
      type: Object,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    availableData: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    optionsComputed() {
      // Update the labels inside options
      return this.options();
    },
  },
  methods: {
    options() {
      return {
        tooltip: {
          enabled: false
        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },
        chart: {
          type: "bar",
          height: 350,
          stacked: true,
          stackType: "100%",
        },
        colors: GOOD_BAD_COLORS,
        plotOptions: {
          bar: {
            horizontal: this.horizontal,
            barHeight: '100%',
          },
        },
        fill: {
          opacity: 1,
        },
        legend: {
          show: false,
        },
        xaxis: {
          categories: this.labels,
          labels: {
            show: false,
          },
        },
      };
    },
  },
};
</script>


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
  margin: 10px;
  text-decoration: un;
}
</style>