<template>
  <div>
    <div v-if="init">
      <v-card>
        <v-container>
          <v-row>
            <v-col cols="12" sm="6">
              You can customize the name of the UTM parameters to be analyzed.
              This will allow them to appear in your stats.
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM source*"
                placeholder="utm_source"
                v-model="utmSource"
                hide-details
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM medium*"
                placeholder="utm_medium"
                v-model="utmMedium"
                hide-details
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM Campaign name"
                placeholder="utm_name"
                v-model="utmName"
                hide-details
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM Campaign ID"
                placeholder="utm_id"
                v-model="utmId"
                hide-details
              />
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
    <div v-else>
      <v-progress-circular
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
      form: {
        utm: {},
      },
    };
  },
  computed: {
    ...mapState({
      init: (state) => state.configuration_script.init,
      configuration_script: (state) => state.configuration_script.configuration,
    }),
    utmSource: {
      set(v) {
        this.form.utm.utm_source = v;
      },
      get() {
        return this.form.utm.utm_source;
      },
    },
    utmMedium: {
      set(v) {
        this.form.utm.utm_medium = v;
      },
      get() {
        return this.form.utm.utm_medium;
      },
    },
    utmName: {
      set(v) {
        if (v === "") {
          if (this.form.utm.utm_name) {
            delete this.form.utm.utm_name;
          }
        } else {
          this.form.utm.utm_name = v;
        }
      },
      get() {
        return this.form.utm.utm_name;
      },
    },
    utmId: {
      set(v) {
        if (v === "") {
          if (this.form.utm.utm_id) {
            delete this.form.utm.utm_id;
          }
        } else {
          this.form.utm.utm_id = v;
        }
      },
      get() {
        return this.form.utm.utm_id;
      },
    },
  },
  created() {
    this.$store.dispatch("configuration_script/getScriptConfig");
  },
  watch: {
    init() {
      this.reset();
    },
  },
  methods: {
    reset() {
      let configuration =
        this.configuration_script && this.configuration_script.utm
          ? JSON.parse(JSON.stringify(this.configuration_script))
          : { utm: {} };
      console.log(configuration, "_-");
      this.form = configuration;
    },
    save() {
      if (this.form.utm.utm_source === "" || this.form.utm.utm_medium === "") {
        this.$store.dispatch("alerts/addAlert", {
          title: "Validation error",
          text: "You must fill a value for the source and the medium",
          level: "warning",
        });
        return;
      }

      this.$store
        .dispatch("configuration_script/setScriptConfig", this.form)
        .then(() => {
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