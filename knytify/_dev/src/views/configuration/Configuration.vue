<template>
  <div>
    <Header controller="KnytifyConfiguration"/>
    <v-card>
      <v-tabs>
        <v-tab @click="tab = 'general'">General</v-tab>
        <v-tab @click="tab = 'utm'">UTM source</v-tab>
      </v-tabs>
      <General v-if="tab == 'general'" />
      <UTM v-else-if="tab == 'utm'" />
    </v-card>
  </div>
</template>


<script>
import Header from "../components/Header.vue";
import General from "./General.vue";
import UTM from "./UTM.vue";

export default {
  name: "App",
  data: function () {
    return {
      tab: "general",
    };
  },
  components: {
    Header,
    General,
    UTM,
  },
};
</script>