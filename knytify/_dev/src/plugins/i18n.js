import { createI18n } from "vue-i18n";

import en from "../locales/en.json";
import fr from "../locales/fr.json";
import es from "../locales/es.json";

const prestashop_locale = ["en", "fr", "es"].includes(window.iso_user) ? window.iso_user : "en"

// https://github.com/intlify/vue-i18n-next/issues/350#issuecomment-782007971
export default createI18n({
  globalInjection: true,
  locale: prestashop_locale,
  messages: {
    fr,
    en,
    es
  },
});
