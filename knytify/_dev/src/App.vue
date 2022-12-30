<template>
  <div>
    <Alerts/>
    <KnytifyStats v-if="controller == 'KnytifyStats'" />
    <KnytifyConfiguration v-else-if="controller == 'KnytifyConfiguration'" />
  </div>
</template>


<script>
import Alerts from "./views/components/Alerts.vue";
import KnytifyConfiguration from "./views/configuration/Configuration.vue";
import KnytifyStats from "./views/stats/Stats.vue";

export default {
  name: "App",
  data: function () {
    return {
      controller: window.help_class_name, // Prestashop defined
    };
  },
  components: {
    KnytifyConfiguration,
    KnytifyStats,
    Alerts,
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
