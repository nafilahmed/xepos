<script setup>
  import ValidationError from '@/components/ValidationError.vue';
</script>
<template>
  <VCard>
    <VCardTitle>
      <div class="d-flex justify-space-between flex-wrap pt-5">
        <div class="me-2 mb-2">
          Employees
        </div>
        <VBtn @click="openCreateModal()" class="me-2 mb-2">Add New</VBtn>
      </div>
    </VCardTitle>
    <VTable>
      <thead>
        <tr>
          <th class="text-uppercase">
            First Name
          </th>
          <th>
            Last Name
          </th>
          <th>
            Email
          </th>
          <th>
            Company
          </th>
          <th>
            phone
          </th>
          <th>
            Action
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="item in allEmployees" :key="item.id">
          <td>
            {{ item.first_name }}
          </td>
          <td class="text-center">
            {{ item.last_name }}
          </td>
          <td class="text-center">
            {{ item.email }}
          </td>
          <td class="text-center">
            {{ item.company ? item.company.name : '' }}
          </td>
          <td class="text-center">
            {{ item.phone }}
          </td>
          <td class="text-center">
            <VIcon @click="openEditModal(item.id)" size="21" icon="bx-edit-alt"></VIcon>
            <VIcon @click="openDeleteModal(item.id)" size="21" icon="bx-trash"></VIcon>
          </td>
        </tr>
      </tbody>
    </VTable>
    <VRow justify="center">
      <VCol cols="3">
        <VContainer class="max-width">
          <VPagination v-model="pagination.current" :length="pagination.total" @update:model-value="getEmployees" ></VPagination>
        </VContainer>
      </VCol>
    </VRow>
  </VCard>

  <div class="text-center">
    <VDialog v-model="dialog" width="600">
      <VCard :title="type + ' Employee'">
        <VCardText>
          <VForm ref="Empform">
            <VRow>
              <VCol cols="12">
                <VTextField v-model="this.employeeFirstName" variant="outlined" hide-details color="primary"
                  :rules="nameRules" label="First Name" required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.employeeLastName" variant="outlined" hide-details color="primary"
                  :rules="nameRules" label="Last Name" />
              </VCol>
              <VCol cols="12">
                <VTextField type="email" v-model="this.employeeEmail" variant="outlined" hide-details color="primary"
                  :rules="emailRules" label="Email" required />
              </VCol>
              <VCol cols="12">
                <VSelect label="Company" v-model="this.employeeCompany" placeholder="Select Company"
                  :items="this.allCompanies" item-value="id" item-title="name" return-object single-line required />
              </VCol>
              <VCol cols="12">
                <VTextField v-model="this.employeePhone" variant="outlined" hide-details color="primary"
                  :rules="phoneRules" label="Phone" placeholder="+1 123 456 7890" type="number" />
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
          <VBtn color="info" variant="text" @click="(this.type == 'Create') ? createEmployee() : updateEmployee()">
            {{ (this.type == 'Create') ? 'Create' : 'Save' }} </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>


  <div class="text-center">

    <VDialog v-model="deleteEmployee" width="600">
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
          <VBtn variant='flat' color="info" @click="this.deleteEmployee = false"> No </VBtn>
          <VBtn color="error" variant='flat' @click="deleteEmployeeFun()"> Yes </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

  </div>
</template>

<script>
export default {
  name: 'employee',
  data() {
    return {
      nameRules: [
        v => !!v || 'Name is required',
        v => (v && v.length <= 20) || 'Name must be less than 20 characters',
      ],
      phoneRules: [
        v => ((v && v?.length > 9 && /[0-9-]+/.test(v)) || !v) || 'Phone number needs to be at least 9 digits',
      ],
      emailRules: [
        v => ((v && (/^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(v))) || !v) || 'Must be a valid e-mail.',
      ],
      allCompanies: {},
      allEmployees: {},
      isUpdated: 0,
      employeeFirstName: '',
      employeeLastName: '',
      employeeCompany: {},
      employeeEmail: '',
      employeePhone: '',
      employeeId: '',
      type: 'Create',
      deleteEmployee: false,
      dialog: false,
      alertMsg: '',
      validationErrors: false,
      pagination: {
        current: 1,
        total: 0
      }
    }
  },
  mounted() {
    this.getEmployees();
  },
  methods: {

    openEditModal: async function (id) {


      let { data } = await axios.get('/api/employee/' + id);

      if (data.status_code == 200) {

        this.isUpdated = 0
        this.employeeFirstName = data.employee.first_name
        this.employeeLastName = data.employee.last_name
        this.employeeEmail = data.employee.email
        this.employeePhone = data.employee.phone
        this.employeeCompany = data.employee.company
        this.employeeId = data.employee.id
        this.validationErrors = false

        this.type = "Update"
        this.getCompanies();
      }
    },

    updateEmployee: async function () {
      this.isUpdated = 0
      const { valid } = await this.$refs.Empform.validate()

      if (valid) {
        await axios.put('/api/employee/' + this.employeeId,
          {
            "first_name": this.employeeFirstName,
            "last_name": this.employeeLastName,
            "email": this.employeeEmail,
            "phone": this.employeePhone,
            "company_id": this.employeeCompany.id,
          }).then(({ data }) => {

            if (data.status_code == 200) {
              this.getEmployees();
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
          });
      }
    },

    openCreateModal: async function () {

      this.getCompanies();

      this.employeeFirstName = ''
      this.employeeLastName = ''
      this.employeeEmail = ''
      this.employeePhone = ''
      this.employeeId = ''
      this.employeeCompany = {}
      this.validationErrors = false
      this.type = "Create"

    },

    createEmployee: async function () {

      const { valid } = await this.$refs.Empform.validate()

      if (valid) {
        this.isUpdated = 0
        await axios.post('/api/employee/',
          {
            "first_name": this.employeeFirstName,
            "last_name": this.employeeLastName,
            "email": this.employeeEmail,
            "phone": this.employeePhone,
            "company_id": this.employeeCompany.id,
          }).then(({ data }) => {

            if (data.status_code == 200) {
              this.getEmployees();
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
          });
      }
    },

    getEmployees: async function () {
      await axios.get('api/employee').then(({ data }) => {

        if (data.status_code == 200) {
          this.allEmployees = data.employees;
        }
      }).catch(error => {
        this.alertMsg = error.message
        this.isUpdated = 500;
      })
    },

    getCompanies: async function () {
      await axios.get('api/company').then(({ data }) => {

        if (data.status_code == 200) {
          this.allCompanies = data.companys;
          this.dialog = true;
        }
      }).catch(error => {
        this.alertMsg = error.message
        this.isUpdated = 500;
      })
    },

    openDeleteModal: function (id) {
      this.employeeId = id
      this.deleteEmployee = true
    },

    deleteEmployeeFun: async function () {
      let { data } = await axios.delete('api/employee/' + this.employeeId);
      this.alertMsg = data.message
      this.isUpdated = (data.status_code == 200) ? 200 : 500;

      this.getEmployees();
    },
  }
}
</script>
