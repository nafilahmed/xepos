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
      <VCard :title="type+' Company'">
        <VCardText>
          <VForm ref="form">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="this.companyName" variant="outlined" hide-details color="primary" :rules="nameRules" label="Name" />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.companyEmail" variant="outlined" hide-details color="primary" :rules="emailRules" label="Email" />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.companyWebsite" variant="outlined" hide-details color="primary" label="Website" />
              </VCol>

              <VCol cols="12" v-if="this.isUpdated != 0">
                <VAlert :color="(this.isUpdated == 200) ? 'success' : 'error'"
                  :icon="(this.isUpdated == 200) ? $success : $error" :title="this.alertMsg"></VAlert>
              </VCol>

            </VRow>
          </VForm>
        </VCardText>
        <VCardActions>
          <VSpacer></VSpacer>
          <VBtn color="info" variant="text" @click="(this.type == 'Create') ? createCompany() : updateCompany()">
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
      isUpdated:0,
      dialog: false,
      companyName : '',
      companyEmail : '',
      companyWebsite : '',
      companyId : '',
      type:'Create',
      deleteCompany:false
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
        this.companyName = data.company.name
        this.companyEmail = data.company.email
        this.companyWebsite = data.company.website
        this.companyId = data.company.id
        
        this.type = "Update"
        this.dialog = true
      }
    },

    updateCompany: async function () {
      this.isUpdated = 0
      const { valid } = await this.$refs.form.validate()

      if (valid) {
        let { data } = await axios.put('/api/company/' + this.companyId,
          {
            "name": this.companyName,
            "email": this.companyEmail,
            "website": this.companyWebsite,
          });

        this.alertMsg = data.message
        this.isUpdated = (data.status_code == 200) ? 200 : 500;
        this.getCompanies();
      }
    },

    openCreateModal: async function () {

      this.companyName = ''
        this.companyEmail = ''
        this.companyWebsite = ''
        this.companyId = ''
        this.type = "Create"
        this.dialog = true;
    },

    createCompany: async function () {

      const { valid } = await this.$refs.form.validate()

      if (valid) {
        this.isUpdated = 0
        let { data } = await axios.post('/api/company/',
          {
            "name": this.companyName,
            "email": this.companyEmail,
            "website": this.companyWebsite,
          });
        this.alertMsg = data.message
        this.isUpdated = (data.status_code == 200) ? 200 : 500;

        this.getCompanies();

      }
    },

    getCompanies: async function () {
      await axios.get('api/company').then(({ data }) => {

        if (data.status_code == 200) {
          this.allCompanies = data.companys;
        }
      }).catch(({ response }) => {
        if (response.status === 422) {
          this.validationErrors = response.data.errors
        } else {
          this.validationErrors = {}
          alert(response.data.message)
        }
      })
    },

    openDeleteModal: function (id){
        this.companyId = id
        this.deleteCompany = true
    },

    deleteCompanyFun: async function (){
        let {data} = await axios.delete('api/company/'+this.companyId);
        this.alertMsg = data.message
        this.isUpdated = (data.status_code == 200) ? 200 : 500;

        this.getCompanies();
    },
  }
}
</script>
