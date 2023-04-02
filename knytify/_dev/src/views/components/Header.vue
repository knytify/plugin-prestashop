<template>
  <v-card class="pa-2 pt-1 pb-1 mb-3">
    <div class="d-flex flex-row align-center">
      <img
        class="logo mr-4"
        alt="Knytify Logo"
        src="/modules/knytify/logo.png"
      />
      <span class="title">Knytify S.àr.l.</span>
      <span style="margin-left: auto">
        <a
          class="ml-2"
          v-if="controller !== 'KnytifyConfiguration' && controller !== 'KnytifyAssociation' "
          :href="links.knytify_configuration"
          ><v-btn>Go to Configuration</v-btn></a
        >
        <a class="ml-2" v-if="controller != 'KnytifyStats'" :href="links.stats"
          ><v-btn>Go to Stats</v-btn></a
        >
        <a class="ml-2" href="https://app.knytify.com" target="_blank"
          ><v-btn>Visit app</v-btn></a
        >
      </span>
    </div>
  </v-card>
</template>

<script>
import { mapState } from "vuex";
export default {
  name: "Header",
  data: function () {
    return {
      links: window.knytify.links,
    };
  },
  props: {
    controller: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
.logo {
  width: 50px;
}

.title {
  font-size: 20px;
  font-weight: bold;
}

a:hover {
  text-decoration: initial;
}
</style>