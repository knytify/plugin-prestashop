<template>
  <v-card>
    <v-container style="position: relative" class="p-4">
      <span
        style="position: absolute; top: 6px; right: 6px; cursor: pointer"
        v-if="step !== 'do_you_have_an_account'"
        @click.prevent="step = 'do_you_have_an_account'"
      >
        Choose another method
        <v-icon icon="mdi-keyboard-return" />
      </span>

      <div class="d-flex" v-if="!requesting">
        <div>
          <img
            class="logo mr-6"
            alt="Knytify Logo"
            src="/modules/knytify/logo.png"
          />
        </div>

        <div style="flex-grow: 1">
          <div v-if="step == 'do_you_have_an_account'">
            <v-row>
              <v-col cols="12" sm="12">
                <h2 class="title-3">
                  Do you already have an account with Knytify?
                </h2>
                <v-btn
                  @click.prevent="step = 'login_or_api_key'"
                  class="mr-2"
                  color="primary"
                  >Yes, I do</v-btn
                >
                <v-btn @click.prevent="step = 'setup_password'" color="error"
                  >Not yet</v-btn
                >
              </v-col>
            </v-row>
          </div>

          <div v-else-if="step == 'login_or_api_key'">
            <v-row>
              <v-col cols="12" sm="12">
                <span class="d-flex">
                  <h2 class="title-3">
                    Choose a method to associate your Knytify account
                  </h2>
                  <span class="ml-3">
                    <v-icon icon="mdi:mdi-help-box"></v-icon
                    ><v-tooltip activator="parent"
                      >We will only store your API key, regardless of the
                      method.</v-tooltip
                    >
                  </span>
                </span>
                <v-btn @click.prevent="step = 'api_key'" class="mr-2"
                  >Enter Api Key</v-btn
                >
                <v-btn @click.prevent="step = 'login'">Log-in</v-btn>
              </v-col>
            </v-row>
          </div>

          <div v-else-if="step == 'setup_password'">
            <v-form v-if="nextRetry === null">
              <v-row>
                <v-col cols="12" sm="6">
                  <h2 class="title-3">Setup a password</h2>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    :value="email"
                    readonly
                    hide-details
                    class="mr-3"
                  />

                  <span>
                    <v-icon icon="mdi-help-box" />
                    <v-tooltip activator="parent" location="right">
                      The e-mail used with your Prestashop association and
                      <br />
                      subscription, is the e-mail that will be used to create
                      <br />
                      your Knytify account.will be used to create your Knytify
                      account.
                    </v-tooltip>
                  </span>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6" class="d-flex align-center">
                  <v-text-field
                    clearable
                    type="password"
                    label="Password"
                    v-model="password"
                    required
                    hide-details
                    class="mr-3"
                  />
                  <span>
                    <v-icon icon="mdi-help-box" />
                    <v-tooltip activator="parent" location="right">
                      At least one upper case English letter, (?=.*?[A-Z])
                      <br />
                      At least one lower case English letter, (?=.*?[a-z])
                      <br />
                      At least one digit, (?=.*?[0-9]) <br />
                      At least one special character, (?=.*?[#?!@$%^&*-]) <br />
                      Minimum eight in length .{8,} (with the anchors)
                    </v-tooltip>
                  </span>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    type="password"
                    clearable
                    label="Password confirmation"
                    v-model="password_check"
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click.prevent="doSetup"
                    >Confirm password
                    <v-tooltip activator="parent">
                      You will be able to modify it later, through the app.
                    </v-tooltip></v-btn
                  >
                </v-col>
              </v-row>
            </v-form>
            <div v-else>
              <div v-if="nextRetry >= 1">
                We are still in the process of creating your account. <br />
                Retrying in {{ nextRetry }} seconds
              </div>

              <div v-else class="d-flex justify-center">
                <v-progress-circular
                  :size="70"
                  :width="7"
                  color="blue"
                  indeterminate
                ></v-progress-circular>
              </div>
            </div>
          </div>

          <div v-else-if="step == 'login'">
            <v-form>
              <v-row>
                <v-col cols="12" sm="6">
                  <h2 class="title-3">
                    Log-in to associate your Api Key with the plug-in.
                  </h2>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="E-mail"
                    :value="email"
                    required
                    hide-details
                    class="mr-3"
                  />

                  <span>
                    <v-icon icon="mdi-help-box" />
                    <v-tooltip activator="parent" location="right">
                      Your Knytify account has to be the e-mail that you used to subscribe with Prestashop. <br/>
                      If it is not, please, set up a password instead.
                    </v-tooltip>
                  </span>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    clearable
                    type="password"
                    label="Password"
                    v-model="password"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click.prevent="doLogin">Log In</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </div>

          <div v-else-if="step == 'api_key'">
            <v-form>
              <v-row>
                <v-col cols="12" sm="6">
                  <h2 class="title-3">Paste here your api key</h2>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    clearable
                    label="Api Key"
                    v-model="api_key"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click.prevent="doApiKey"
                    >Confirm password
                    <v-tooltip activator="parent">
                      You will be able to modify it later, through the app.
                    </v-tooltip></v-btn
                  >
                </v-col>
              </v-row>
            </v-form>
          </div>

          <div v-else-if="step == 'all_set'">
            <v-row>
              <v-col cols="12" sm="6">
                <h2 class="title-3">All set!</h2>
                <p>
                  We are now analyzing your visitors.<br />
                  You can check the stats in the stats page, or configure your
                  UTM parameters in the configuration page.
                </p>
                <p>
                  <a :href="link_config"
                    ><v-btn class="mt-3">Go to configuration page</v-btn></a
                  >
                </p>
              </v-col>
            </v-row>
          </div>
        </div>
      </div>

      <div v-else class="d-flex justify-center">
        <v-progress-circular
          :size="70"
          :width="7"
          color="blue"
          indeterminate
        ></v-progress-circular>
      </div>
    </v-container>
  </v-card>
</template>


<script>
import { formatHttpError } from "@/helper/format";

export default {
  name: "App",
  props: {
    email: {
      type: String,
    },
  },
  data() {
    return {
      step: "do_you_have_an_account",
      api_key: "",
      password: "",
      password_check: "",
      link_config: window.knytify.links.knytify_configuration,
      next_retry: null,
      requesting: false,
    };
  },
  methods: {
    doApiKey() {
      if (this.api_key.length < 10) {
        this.$store.dispatch("alerts/addAlert", {
          title: "Error",
          text: "Api key required",
          level: "warning",
        });
        return;
      }

      if (this.api_key) {
        this.requesting = true;
        this.$store
          .dispatch("knytify_account/getUser", {
            api_key: this.api_key.trim(),
          })
          .then(() => {
            this.requesting = false;
            this.step = "all_set";
            this.$store.dispatch("alerts/addAlert", {
              title: "Success",
              text: "The API Key has been set.",
              level: "success",
            });
          })
          .catch((err) => {
            this.requesting = false;
            this.$store.dispatch("alerts/addAlert", {
              title: "Error",
              text: formatHttpError(
                err,
                "We could not associate this API Key",
                this.$t
              ),
              level: "warning",
            });
          });
      }
    },
    doLogin() {
      if (this.password < 10) {
        this.$store.dispatch("alerts/addAlert", {
          title: "Error",
          text: "Password required",
          level: "warning",
        });
        return;
      }

      this.requesting = true;

      this.$store
        .dispatch("knytify_account/login", {
          password: this.password,
        })
        .then(() => {
          this.requesting = false;
          this.step = "all_set";
          this.$store.dispatch("alerts/addAlert", {
            title: "Success",
            text: "The API Key has been set.",
            level: "success",
          });
        })
        .catch((err) => {
          this.requesting = false;
          this.$store.dispatch("alerts/addAlert", {
            title: "Error",
            text: formatHttpError(err, "We could not log-in", this.$t),
            level: "warning",
          });
        });
    },
    doSetup() {
      if (this.password !== this.password_check) {
        this.$store.dispatch("alerts/addAlert", {
          title: "Validation",
          text: "Passwords do not match",
          level: "warning",
        });
        return;
      }

      this._try_setup();
    },
    _try_setup() {
      if (this.step !== "setup_password") {
        // Prevent timeout to trigger if the user changes the step.
        this.nextRetry = null;
        return;
      }

      if (this.nextRetry && this.nextRetry > 0) {
        this.nextRetry -= 1;
        setTimeout(this._try_setup.bind(this), 1000);
        return;
      }

      this.requesting = true;
      this.$store
        .dispatch("knytify_account/setup", {
          password: this.password,
        })
        .then(() => {
          this.requesting = false;
          this.step = "all_set";
          this.next_retry = null;
          this.$store.dispatch("alerts/addAlert", {
            title: "Success",
            text: "The setup is complete. The API Key has been configured.",
            level: "success",
          });
        })
        .catch((err) => {
          this.requesting = false;
          if (
            err.response.status == 400 &&
            err.response.data === "incorrect_credentials"
          ) {
            // The account creation triggered after the subscription webhook is still not completed.
            this.$store.dispatch("alerts/addAlert", {
              title: "Retrying soon",
              text: "Waiting for account...",
              level: "warning",
            });
            this.next_retry = 10;
            setTimeout(this._try_setup.bind(this), 1000);
            console.log("RETRYING IN 1s - FIRST");
          } else {
            console.log("NOT INC");
            this.next_retry = null;
            this.$store.dispatch("alerts/addAlert", {
              title: "Error",
              text: formatHttpError(err, "Error during setup", this.$t),
              level: "warning",
            });
          }
        });
    },
  },
  computed: {
    nextRetry: {
      get() {
        return this.next_retry;
      },
      set(v) {
        this.next_retry = v;
      },
    },
  },
};
</script>

<style scoped>
.logo {
  width: 40px;
  height: 40px;
}
</style>