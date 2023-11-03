<script setup>
  import { mapActions } from 'vuex'
  import logo from '@images/logo.svg?raw'
  import ValidationError from '@/components/ValidationError.vue'
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard class="auth-card pa-4 pt-7" max-width="448">
      <VCardItem class="justify-center">
        <template #prepend>
          <div class="d-flex">
            <div class="d-flex text-primary" v-html="logo" />
          </div>
        </template>

        <VCardTitle class="text-2xl font-weight-bold">
          XEPOS
        </VCardTitle>
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 mb-1">
          Welcome to XEPOS! 👋🏻
        </h5>
        <p class="mb-0">
          Please sign-in to your account and start the adventure
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="login()">
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField v-model="auth.email" name="email" autofocus placeholder="johndoe@email.com" label="Email"
                type="email" />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField v-model="auth.password" name="password" label="Password" placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible" />
              <br>
              <!-- login button -->
              <VBtn block type="submit">
                Login
              </VBtn>
            </VCol>
            <VCol cols="12">
              <ValidationError :validationErrors="validationErrors" />
            </VCol>

          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<script>

export default {
  name: "login",
  data() {
    return {
      auth: {
        email: "",
        password: ""
      },
      validationErrors: false,
      processing: false,
      isPasswordVisible: false
    }
  },
  methods: {
    ...mapActions({
      signIn: 'auth/login'
    }),
    async login() {
      this.processing = true
      await axios.get('/sanctum/csrf-cookie')
      await axios.post('/login', this.auth).then(({ data }) => {
        this.signIn()
      }).catch(({ response }) => {
        if (response.status === 422) {
          this.validationErrors = response.data.errors
        } else {
          this.validationErrors = false
          alert(response.data.message)
        }
      })
    },
  }
}
</script>
