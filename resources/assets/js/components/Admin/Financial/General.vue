<template>
  <div>
    <div class="block full hidden-print bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
        <div class="form-group">
          <v-select
            placeholder="Empresa"
            v-model="companySelected"
            :on-change="onChangeCompany"
            :options="companiesList">
          </v-select>
        </div>
        <div class="form-group">
          <v-select
            placeholder="Gerente"
            v-model="managerSelected"
            :on-change="onChangeManager"
            :options="managersList">
          </v-select>
        </div>
        <div class="form-group p-t-sm">
          <v-select
            placeholder="Operador"
            v-model="sellerSelected"
            :options="sellersList">
          </v-select>
        </div>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-b">
        <div class="form-group">
          <input v-model="startDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="finalDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <label class="csscheckbox">
            <input v-model="showSellers" type="checkbox">
            <span></span>
            Operadores
          </label>
        </div>
        <div class="form-group">
          <label class="csscheckbox">
            <input v-model="detailed" type="checkbox">
            <span></span>
            Detalhes
          </label>
        </div>
        <button v-if="loading" class="btn btn-primary">
          <i class="fa fa-spinner fa-spin"></i> Carregando
        </button>
        <button v-else @click.prevent="getReport" class="btn btn-success">
          <i class="fa fa-filter"></i> Filtrar
        </button>
        <button onclick="window.print()" class="btn btn-info visible-lg-block">
          <i class="fa fa-print"></i>
          Imprimir
        </button>
      </div>
    </div>
    <FinancialSummary
      v-for="company in report"
      :key="company.id"
      :company="company"
      :show-sellers="showSellers"
      :detailed="detailed"
    ></FinancialSummary>
  </div>
</template>

<script>
  import FinancialSummary from '../../Supervisor/Reports/FinancialSummary'
  import vSelect from 'vue-select'

  export default {
    components: {FinancialSummary, vSelect},
    data() {
      return {
        detailed: false,
        showSellers: false,
        loading: false,
        startDate: null,
        finalDate: null,
        report: [],
        sellersList: [],
        sellerSelected: null,
        managersList: [],
        managerSelected: null,
        companiesList: [],
        companySelected: null
      }
    },
    methods: {
      getReport() {
        this.loading = true
        this.$http
          .get('/admin/financial/report', {
            params: {
              startDate: this.startDate,
              finalDate: this.finalDate,
              companyId: this.companySelected ? this.companySelected.value : null,
              managerId: this.managerSelected ? this.managerSelected.value : null,
              sellerId: this.sellerSelected ? this.sellerSelected.value : null
            }
          })
          .then(response => this.report = response.body.companies)
          .catch(() => this.$swal(
            'Erro ao recuparar dados do relatório',
            'Ocorreu algum erro ao recuperar os dados, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => this.loading = false)
      },
      getCompanies() {
        this.$http.get('/admin/companies/pluck')
          .then(response => {
            this.companiesList = response.body.companies
            this.companiesList.unshift({label: 'Todas', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao recuperar empresas',
            'Ocorreu algum erro ao recuperar os dados, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      getManagers(companyId) {
        this.$http.get('/admin/managers/pluck', {params: {companyId}})
          .then(response => {
            this.managersList = response.body.managers
            this.managersList.unshift({label: 'Todos', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao listar gerentes',
            'Ocorreu algum erro ao recuperar os dados, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      getSellers(companyId, managerId) {
        this.$http.get('/admin/sellers/pluck', {params: {companyId, managerId}})
          .then(response => {
            this.sellersList = response.body.sellers
            this.sellersList.unshift({label: 'Todos', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao listar operadores',
            'Ocorreu algum erro ao recuperar os dados, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      onChangeCompany(company) {
        this.companySelected = company
        this.managerSelected = null
        this.managersList = []
        this.sellerSelected = null
        this.sellersList = []

        if (company !== null) {
          let companyId = company.value !== 0 ? company.value : null
          this.getManagers(companyId)
          this.getSellers(companyId, null)
        }
      },
      onChangeManager(manager) {
        this.managerSelected = manager
        this.sellerSelected = null
        this.sellersList = []

        if (manager !== null) {
          let managerId = manager.value !== 0 ? manager.value : null
          this.getSellers(null, managerId)
        }
      }
    },
    created() {
      this.getCompanies()
      this.getManagers()
      this.getSellers()
    },
    mounted() {
      this.startDate = new Date().toISOString().slice(0, 10)
      this.finalDate = this.startDate
    }
  }
</script>

<style scoped>
    .block.full {
        padding-bottom: 0;
    }

    .block-title {
        margin-bottom: 5px;
    }
</style>
