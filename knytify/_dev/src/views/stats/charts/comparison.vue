<template>
  <Comparison
    :init="init"
    :labels="labelsComputed"
    :data="dataComputed"
    :availableData="availableData"
    :title="$t(dimension)"
  />
</template>


<script>
import { mapState } from "vuex";
import Comparison from "./base/comparison.vue";

export default {
  components: {
    Comparison,
  },
  props: {
    translateLabels: {
      type: Boolean,
      default: false,
    },
    dimension: {
      type: String,
      required: true,
    },
  },
  computed: {
    ...mapState({
      init(state) {
        return state.stats.init_recap && this.dimension !== undefined;
      },
      stats(state) {
        return state.stats.stats_recap[this.dimension];
      },
    }),
    labelsComputed() {
      if (!this.init) {
        return [];
      }
      let labels = this.getCategories();
      if (this.translateLabels) {
        labels = labels.map((label) => this.$t(label));
      }
      return labels;
    },
    availableData() {
      if (!this.init) {
        return false;
      }
      return Object.keys(this.stats).length > 0;
    },
    dimensionComputed() {
      return this.dimension;
    },
    dataComputed() {
      if (!this.init) {
        return [];
      }
      const categories = this.getCategories();
      let ret = [
        {
          name: "Fraud",
          data: [],
        },
        {
          name: "Non fraud",
          data: [],
        },
      ];
      categories.map((category) => {
        const fraud =
          this.stats[category].score / this.stats[category].num_sessions;
        const non_fraud = 1 - fraud;
        ret[0].data.push(fraud);
        ret[1].data.push(non_fraud);
      });
      return ret;
    },
  },
  methods: {
    getCategories() {
      return Object.keys(this.stats).sort();
    },
  },
};
</script>
