<template>
  <div>
    <div v-if="init">
      <v-card>
        <v-container>
          <v-row>
            <v-col cols="6" class="d-flex align-center">
              <v-switch
                color="success"
                class="mr-2"
                v-model="enabled"
                hide-details
                style="flex-grow: 0"
              ></v-switch>
              <span style="cursor: default"
                >Plugin mode <i class="material-icons">help</i
                ><v-tooltip activator="parent"
                  >Activate the traffic analysis</v-tooltip
                ></span
              >
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn @click="save" color="primary">Save</v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </div>
    <div v-else class="d-flex justify-center">
      <v-progress-circular
        style="margin-top: 100px"
        :size="70"
        :width="7"
        color="blue"
        indeterminate
      ></v-progress-circular>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      form: {},
    };
  },
  computed: {
    ...mapState({
      init: (state) => state.configuration.init,
      configuration: (state) => state.configuration.configuration,
    }),
    enabled: {
      set(v) {
        this.form.enabled = v ? "1" : "0";
      },
      get() {
        return this.form.enabled === "1" ? true : false;
      },
    },
  },
  created() {
    this.$store.dispatch("configuration/getConfig");
  },
  watch: {
    init() {
      this.reset();
    },
  },
  methods: {
    reset() {
      this.form = JSON.parse(JSON.stringify(this.configuration));
    },
    save() {
      this.$store.dispatch("configuration/setConfig", this.form).then(() => {
        this.$store.dispatch("alerts/addAlert", {
          title: "Success",
          text: "The configuration has been saved",
          level: "success",
        });
      });
    },
  },
  name: "Configuration",
};
</script>