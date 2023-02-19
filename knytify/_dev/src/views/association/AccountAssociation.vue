<template>
  <v-card>
    <v-container style="position: relative" class="p-4">
      <span
        style="position: absolute; top: 6px; right: 6px; cursor: pointer"
        v-if="step !== 'do_you_have_an_account'"
        @click="step = 'do_you_have_an_account'"
      >
        Choose another method
        <v-icon icon="mdi-keyboard-return" />
      </span>

      <div class="d-flex">
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
                  @click="step = 'login_or_api_key'"
                  class="mr-2"
                  color="primary"
                  >Yes, I do</v-btn
                >
                <v-btn @click="step = 'setup_password'" color="error"
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
                <v-btn @click="step = 'api_key'" class="mr-2"
                  >Enter Api Key</v-btn
                >
                <v-btn @click="step = 'login'">Log-in</v-btn>
              </v-col>
            </v-row>
          </div>

          <div v-else-if="step == 'setup_password'">
            <v-form>
              <v-row>
                <v-col cols="12" sm="6">
                  <h2 class="title-3">Setup a password</h2>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="E-mail"
                    :value="email"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Password"
                    v-model="password"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Password confirmation"
                    readonly
                    v-model="password_check"
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click="doSetup"
                    >Confirm password
                    <v-tooltip activator="parent">
                      You will be able to modify it later, through the app.
                    </v-tooltip></v-btn
                  >
                </v-col>
              </v-row>
            </v-form>
          </div>


          <div v-else-if="step == 'login'">
            <v-form>
              <v-row>
                <v-col cols="12" sm="6">
                  <h2 class="title-3">Log-in to associate your Api Key with the plug-in.</h2>
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="E-mail"
                    :value="email"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Password"
                    v-model="password"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click="doLogin"
                    >Log In</v-btn>
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
                    label="Api Key"
                    v-model="api_key"
                    required
                    hide-details
                  />
                </v-col> </v-row
              ><v-row class="mt-2">
                <v-col cols="12" sm="6">
                  <v-btn type="submit" @click="doApiKey"
                    >Confirm password
                    <v-tooltip activator="parent">
                      You will be able to modify it later, through the app.
                    </v-tooltip></v-btn
                  >
                </v-col>
              </v-row>
            </v-form>
          </div>

        </div>
      </div>
    </v-container>
  </v-card>
</template>


<script>
export default {
  name: "App",
  props: {
    email: {
      type: String,
    },
  },
  methods: {
    doApiKey() {

    },
    doLogin() {

    },
    doSetup() {

    }
  },
  data: function () {
    return {
      step: "do_you_have_an_account",
      password: null,
      password_check: null,
    };
  },
  components: {},
};
</script>

<style scoped>
.logo {
  width: 40px;
  height: 40px;
}
</style>