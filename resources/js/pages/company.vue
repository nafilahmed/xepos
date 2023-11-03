<script setup>
  import ValidationError from '@/components/ValidationError.vue';
</script>
<template>
  <VCard>
    <VCardTitle>
      <div class="d-flex justify-space-between flex-wrap pt-5">
        <div class="me-2 mb-2">
          Companies
        </div>
        <VBtn @click="openCreateModal()" class="me-2 mb-2">Add New</VBtn>
      </div>
    </VCardTitle>
    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase">
            Name
          </th>
          <th>
            Email
          </th>
          <th>
            Website
          </th>
          <th>
            Action
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="item in allCompanies" :key="item.id">
          <td>
            {{ item.name }}
          </td>
          <td class="text-center">
            {{ item.email }}
          </td>
          <td class="text-center">
            {{ item.website }}
          </td>
          <td class="text-center">
            <VIcon @click="openEditModal(item.id)" size="21" icon="bx-edit-alt"></VIcon>
            <VIcon @click="openDeleteModal(item.id)" size="21" icon="bx-trash"></VIcon>
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>

  <div class="text-center">
    <VDialog v-model="dialog" width="600">
      <VCard :title="type + ' Company'">
        <VCardText>
          <VForm ref="form" enctype="multipart/form-data">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="this.formData.name" variant="outlined" hide-details color="primary"
                  :rules="nameRules" label="Name" />
              </VCol>
              <VCol cols="12">
                <VTextField type="file" accept="image/png, image/jpeg, image/bmp" hidden placeholder="Pick an logo"
                  prepend-inner-icon="mdi-camera" label="Logo" ref="file" @change="handleFileObject()"></VTextField>
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.formData.email" variant="outlined" hide-details color="primary"
                  :rules="emailRules" label="Email" />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.formData.website" variant="outlined" hide-details color="primary"
                  label="Website" />
              </VCol>
              <VCol cols="12" v-if="this.isUpdated != 0">
                <VAlert :color="(this.isUpdated == 200) ? 'success' : 'error'"
                  :icon="(this.isUpdated == 200) ? $success : $error" :title="this.alertMsg"></VAlert>
              </VCol>
              <VCol cols="12">
                <ValidationError :validationErrors="validationErrors" />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VSpacer></VSpacer>
          <VBtn color="info" variant="text" @click="createCompany()">
            {{ (this.type == 'Create') ? 'Create' : 'Save' }} </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>

  <div class="text-center">

    <VDialog v-model="deleteCompany" width="600">
      <VCard>
        <VCardTitle class="text-h5">
          Are you Sure ?
        </VCardTitle>
        <VCardText>
          <VCol cols="12" v-if="this.isUpdated != 0">
            <VAlert :color="(this.isUpdated == 200) ? 'success' : 'error'"
              :icon="(this.isUpdated == 200) ? $success : $error" :title="this.alertMsg"></VAlert>
          </VCol>
        </VCardText>
        <VCardActions>
          <VSpacer></VSpacer>
          <VBtn variant='flat' color="info" @click="this.deleteCompany = false"> No </VBtn>
          <VBtn color="error" variant='flat' @click="deleteCompanyFun()"> Yes </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

  </div>
</template>

<script>
export default {
  name: 'company',
  data() {
    return {
      nameRules: [
        v => !!v || 'Name is required',
        v => (v && v.length <= 20) || 'Name must be less than 20 characters',
      ],
      emailRules: [
        v => ((v && (/^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(v))) || !v) || 'Must be a valid e-mail.',
      ],
      allCompanies: {},
      formData: {
        name: '',
        email: '',
        website: '',
        id: ''
      },
      isUpdated: 0,
      dialog: false,
      companyId: '',
      type: 'Create',
      deleteCompany: false,
      config: {
        headers: {
          'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2)
        }
      },
      logo: '',
      alertMsg: '',
      validationErrors: false
    }
  },
  mounted() {
    this.getCompanies();
  },
  methods: {

    openEditModal: async function (id) {

      let { data } = await axios.get('/api/company/' + id);

      if (data.status_code == 200) {
        this.isUpdated = 0

        this.formData = {
          name: data.company.name,
          email: data.company.email,
          website: data.company.website,
          id: data.company.id
        }

        this.validationErrors = false
        this.logo = ''
        this.type = "Update"
        this.dialog = true
      }
    },

    openCreateModal: async function () {
      this.isUpdated = 0
      this.formData = {
        name: '',
        email: '',
        website: '',
        id: ''
      }
      this.logo = ''
      this.type = "Create"
      this.validationErrors = false
      this.dialog = true;
    },

    createCompany: async function () {

      const { valid } = await this.$refs.form.validate()

      if (valid) {

        this.isUpdated = 0

        if (this.logo) {
          this.formData.logo = this.logo
        }

        await axios.post('/api/company/', this.formData, this.config).then(({ data }) => {

          if (data.status_code == 200) {
            this.getCompanies();
            this.alertMsg = data.message
            this.isUpdated = 200;
          } else if (data.status_code == 422) {
            this.validationErrors = data.error
          } else {
            this.alertMsg = data.message
            this.isUpdated = 500;
          }
        }).catch(error => {
          this.alertMsg = error.message
          this.isUpdated = 500;
        })
      }
    },

    getCompanies: async function () {
      await axios.get('api/company').then(({ data }) => {

        if (data.status_code == 200) {
          this.allCompanies = data.companys;
        }
      }).catch(({ response }) => {
        this.alertMsg = response.message
        this.isUpdated = 500;

      })
    },

    openDeleteModal: function (id) {
      this.companyId = id
      this.deleteCompany = true
    },

    deleteCompanyFun: async function () {
      let { data } = await axios.delete('api/company/' + this.companyId);
      this.alertMsg = data.message
      this.isUpdated = (data.status_code == 200) ? 200 : 500;

      this.getCompanies();
    },

    handleFileObject: function () {
      this.logo = this.$refs.file.files[0]
    }
  }
}
</script>
