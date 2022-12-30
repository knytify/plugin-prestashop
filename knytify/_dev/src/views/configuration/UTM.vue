<template>
  <div>
    <div v-if="init">
      <v-card>
        <v-container>
          <v-row>
            <v-col cols="12" sm="6">
              You can customize the name of the UTM parameters to be analyzed. This will allow them to appear in your stats.
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
                v-model="utmSource"
                hide-details
              />
            </v-col>

          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM Campaign name"
                placeholder="utm_name"
                v-model="utmSource"
                hide-details
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                label="UTM Campaign ID"
                placeholder="utm_id"
                v-model="utmSource"
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
    <div v-else>Loading...</div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      form: {
        tag_config: {
          utm: {
            utm_source: "",
          },
        },
      },
    };
  },
  computed: {
    ...mapState({
      init: (state) => state.configuration.init,
      configuration: (state) => state.configuration.configuration,
    }),
    utmSource: {
      set(v) {
        this.form.tag_config.utm.utm_source = v;
      },
      get() {
        return this.form.tag_config.utm.utm_source;
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
      // this.form = JSON.parse(JSON.stringify(this.configuration));
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