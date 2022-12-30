<template>
  <div class="cnt">
    <div class="title">UTM Fraud comparison</div>
    <div class="chart-container">
      <div v-if="ready">
        <apexchart
          v-if="availableData"
          height="290"
          type="bar"
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

<style>
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
import { numberDisplayFormatter } from "../../../helper/number.js";

export default {
  data() {
    return {
      interval: "daily",
    };
  },
  created() {
    var from_date = new Date();
    from_date.setDate(from_date.getDate() - 7);
    from_date.setHours(0, 0, 0, 0);

    this.$store.dispatch("stats/getStatsAdvanced", {
      interval: "daily",
      dimensions: "utm_source,utm_medium,utm_name,utm_id",
      from_date: from_date.toISOString().split("T")[0],
    });
  },
  methods: {
    getCategories(split = false) {
      var categories_str = [];
      this.stats.map((v) => {
        let name = v.utm_source;
        if (name) {
          if (v.utm_medium) {
            name += "_" + v.utm_medium;
          }
          if (v.utm_name) {
            name += "_" + v.utm_name;
          } else if (v.utm_id) {
            name += "_" + v.utm_id;
          }
          if (!categories_str.includes(name)) {
            categories_str.push(name);
          }
        }
      });
      let ret = ["Direct", ...categories_str.sort()];
      if (split) {
        ret = ret.map((v) => v.split("_"));
      }
      return ret;
    },
  },
  computed: {
    ...mapState({
      ready: (state) => state.stats.init_adv,
      stats: (state) => state.stats.stats_adv,
    }),
    availableData() {
      return this.stats.length > 0;
    },
    getData() {
      const categories = this.getCategories();
      const data_dict = {};
      const data_counter = {};
      this.stats.map((v) => {
        let name = v.utm_source;
        if (name) {
          if (v.utm_medium) {
            name += "_" + v.utm_medium;
          }
          if (v.utm_name) {
            name += "_" + v.utm_name;
          } else if (v.utm_id) {
            name += "_" + v.utm_id;
          }
        } else {
          name = "Direct";
        }
        if (!Object.keys(data_dict).includes(name)) {
          data_dict[name] = 0;
          data_counter[name] = 0;
        }
        data_dict[name] += v.score;
        data_counter[name] += 1;
      });

      return [
        {
          data: categories.map(
            (category) =>
              Math.round((data_dict[category] / data_counter[category]) * 100) /
              100
          ),
        },
      ];
    },
    options() {
      const colors = ["#26c056", "#f89b26", "#cc2300", "#0e305c", "#0661db"];
      return {
        chart: {
          height: 350,
          type: "bar",
          events: {
            click: function (chart, w, e) {
              // console.log(chart, w, e)
            },
          },
        },
        colors,
        plotOptions: {
          bar: {
            columnWidth: "45%",
            distributed: true,
          },
        },
        dataLabels: {
          enabled: false,
        },
        legend: {
          show: false,
        },
        xaxis: {
          categories: this.getCategories(true),
          labels: {
            style: {
              colors: colors,
              fontSize: "12px",
            },
          },
        },
      };
    },
  },
};
</script>
