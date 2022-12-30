// Styles
import "vuetify/styles"; // Global CSS has to be imported


// Vue
import { createApp } from "vue";

// Plugins for Vue
import App from "./App.vue";
import store from "./store";
import vuetify from "./plugins/vuetify";
import VueApexCharts from "vue3-apexcharts";

// Create apps
const app = createApp(App);

// Use plugins
app.use(store);
app.use(vuetify);
app.use(VueApexCharts);


// Plugins components
app.component("apexchart", VueApexCharts);


app.mount('#app')
