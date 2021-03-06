<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space tres-botoes p-lr p-tb">
        <div class="form-group">
          <v-select
            placeholder="Empresa"
            v-model="company"
            :on-change="onChangeCompany"
            :options="companies">
          </v-select>
        </div>
        <div class="form-group">
          <v-select
            placeholder="Gerente"
            v-model="manager"
            :on-change="onChangeManager"
            :options="managers">
          </v-select>
        </div>
        <div class="form-group">
          <v-select
            v-model="seller"
            placeholder="Cambista"
            :options="sellers">
          </v-select>
        </div>
      </div>
      <div class="btn-group btn-group-full tres-botoes p-lr p-b">
        <button :class="{'btn-primary': status === 'all'}" @click.prevent="status = 'all'" class="btn btn-default">Todos</button>
        <button :class="{'btn-primary': status === 'win'}" @click.prevent="status = 'win'" class="btn btn-default">Prêmio</button>
        <button :class="{'btn-primary': status === 'pending'}" @click.prevent="status = 'pending'" class="btn btn-default">Aberto</button>
        <button :class="{'btn-primary': status === 'partial'}" @click.prevent="status = 'partial'" class="btn btn-default">P. Parcial</button>
        <button :class="{'btn-primary': status === 'lose'}" @click.prevent="status = 'lose'" class="btn btn-default">Perdidas</button>
        <button :class="{'btn-primary': status === 'canceled'}" @click.prevent="status = 'canceled'" class="btn btn-default">Canceladas</button>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-b">
        <div class="form-group">
          <input v-model="startDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="finalDate" type="date" class="form-control">
        </div>
        <div class="form-group m-t-sm">
          <input v-model="betId" @keyup="debounceSearch" pattern="[0-9]" type="number"
                 class="form-control" placeholder="N. Bilhete">
        </div>
        <button @click.prevent="getBets" class="btn m-t-sm"
                :class="{'btn-success': !loading, 'btn-primary': loading}">
          <i v-if="loading" class="fa fa-spinner fa-spin"></i>
          <i class="fa fa-filter" v-else></i> Filtrar
        </button>
      </div>
    </div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Últimas apostas</h2>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td class="link" @click.prevent="changeOrder('id')">
              <div>
                <b>Código</b>
                <i
                  v-if="sortBy === 'id'"
                  :class="{'fa-chevron-down': sortOrder === 'desc', 'fa-chevron-up': sortOrder === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Data</b></td>
            <td><b>Operador</b></td>
            <td class="link" @click.prevent="changeOrder('bet_value')">
              <div>
                <b>Valor</b>
                <i
                  v-if="sortBy === 'bet_value'"
                  :class="{'fa-chevron-down': sortOrder === 'desc', 'fa-chevron-up': sortOrder === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td class="link" @click.prevent="changeOrder('prize')">
              <div>
                <b>Prêmio</b>
                <i
                  v-if="sortBy === 'prize'"
                  :class="{'fa-chevron-down': sortOrder === 'desc', 'fa-chevron-up': sortOrder === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Nome</b></td>
            <td><b>Jogos</b></td>
            <td><b>Comissão</b></td>
            <td><b>Opções</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="bet in sortedBets" :key="bet.id">
            <td>
              <span class="label label-default">{{ bet.id }}</span>
              <br>
              <span class="label label-default" v-if="bet.status === 'pending'">Andamento</span>
              <span class="label label-warning" v-else-if="bet.status === 'canceled'">Cancelada</span>
              <span class="label label-success" v-else-if="bet.status === 'win'">Ganhou</span>
              <span class="label label-danger" v-else-if="bet.status === 'lose'">Perdeu</span>
              <br>
              <span class="label label-info" v-if="bet.origin === 'live'">Ao vivo</span>
            </td>
            <td>
              <span v-if="bet.status !== 'canceled'">{{ formatDate(bet.created_at) }}</span>
              <div v-else>
                D: {{ formatDate(bet.created_at) }}
                <br>
                <b>C: {{ formatDate(bet.canceled_at) }}</b>
              </div>
            </td>
            <td>
              {{ bet.company.name }}
              <br>
              {{ bet.seller.name }}
            </td>
            <td><b>{{ bet.bet_value | formatMoney }}</b></td>
            <td>
              <span class="label"
                    :class="{'label-success': bet.status === 'win', 'label-danger': bet.status === 'lose', 'label-warning': bet.status === 'canceled', 'label-default': bet.status === 'pending'}">
                <b>{{ bet.prize | formatMoney }}</b>
              </span>
            </td>
            <td><b>{{ bet.client_name }}</b></td>
            <td>{{ bet.tips_quantity }}</td>
            <td>{{ bet.commission | formatMoney }}</td>
            <td>
              <button
                @click.prevent="openBet(bet)"
                :class="{'wasOpened': bet.id === latestOpenedBet}"
                class="btn btn-info">
                Bilhete
              </button>
              <button
                v-if="bet.status !== 'canceled'"
                @click.prevent="perguntarParaCancelar(bet)"
                class="btn btn-danger">
                <i class="fa fa-ban"></i>
              </button>
            </td>
          </tr>
          <tr v-if="bets.length === 0">
            <td colspan="10" class="text-center">
              <strong>Nenhuma aposta encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import vSelect from 'vue-select'
  import { showToast } from '../../../functions/showToast'

  export default {
    components: {vSelect},
    data () {
      return {
        loading: false,
        startDate: null,
        finalDate: null,
        status: 'all',
        latestOpenedBet: null,
        betId: null,
        bets: [],
        sortBy: 'created_at',
        sortOrder: 'desc',
        companies: [],
        managers: [],
        sellers: [],
        company: null,
        manager: null,
        seller: null
      }
    },
    methods: {
      changeOrder(sortBy) {
        if (this.sortBy === sortBy) {
          this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
          return
        }
        this.sortBy = sortBy;
        this.sortOrder = 'desc'
      },
      formatDate (date) {
        return moment(date).format('DD/MM HH:mm:ss')
      },
      debounceSearch () {},
      searchBets () {
        this.loading = true;
        this.$http
          .post('/technical/cashier/search', {betId: this.betId})
          .then(response => this.bets = response.body.bets)
          .catch(() => this.$swal('Erro!', 'Erro ao listar simulações', 'error'))
          .finally(() => this.loading = false)
      },
      getBets () {
        this.loading = true;
        this.$http
          .get('/technical/cashier/report', {
            params: {
              startDate: this.startDate,
              finalDate: this.finalDate,
              status: this.status,
              companyId: this.company ? this.company.value : null,
              managerId: this.manager ? this.manager.value : null,
              sellerId: this.seller ? this.seller.value : null
            }
          })
          .then(response => this.bets = response.body.bets)
          .catch(() => this.$swal('Erro!', 'Erro ao listar simulações', 'error'))
          .finally(() => this.loading = false)
      },
      getCompanies() {
        this.$http.get('/technical/companies/pluck')
          .then(response => {
            this.companies = response.body.companies;
            this.companies.unshift({label: 'Todas', value: 0})
          })
          .catch(() => this.$swal('Erro!', 'Erro ao listar empresas', 'error'))
      },
      getManagers (companyId) {
        this.$http.get('/technical/managers/pluck', {params: {companyId: companyId}})
          .then(response => this.managers = response.body.managers)
          .catch(() => showToast('Erro ao listar gerentes'))
      },
      getSellers (companyId, managerId) {
        this.$http.get('/technical/sellers/pluck', {params: {companyId, managerId}})
          .then(response => this.sellers = response.body.sellers)
          .catch(() => showToast('Erro ao listar cambistas'))
      },
      onChangeCompany(company) {
        this.company = company;
        this.manager = null;
        this.managers = [];
        this.seller = null;
        this.sellers = [];

        if (company !== null) {
          let companyId = company.value !== 0 ? company.value : null;
          this.getManagers(companyId);
          this.getSellers(companyId, null)
        }
      },
      onChangeManager (manager) {
        this.manager = manager;
        this.seller = null;
        this.sellers = [];
        if (manager !== null) {
          let managerId = manager.value !== 0 ? manager.value : null;
          this.getSellers(null, managerId)
        }
      },
      openBet (bet) {
        this.latestOpenedBet = bet.id;
        window.open(`/technical/bets/summary/${bet.id}`)
      },
      perguntarParaCancelar(aposta) {
        this.$swal({
          title: 'Cancelar simulação',
          html: `Deseja cancelar a simulação <b>${aposta.id}</b>?`,
          type: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '<i class="glyphicon glyphicon-chevron-left"></i> Voltar',
          confirmButtonText: '<i class="glyphicon glyphicon-ok"></i> Sim, cancelar'
        }).then(result => {
          if (result) this.cancelBet(aposta)
        }).catch(() => false)
      },
      cancelBet (bet) {
        this.$http.get(`/technical/bets/cancel/${bet.id}`)
          .then(response => {
            bet.status = response.body.bet.status;
            this.$swal('Sucesso!', 'Aposta cancelada com sucesso', 'success')
          })
          .catch(() => this.$swal('Erro!', 'Erro ao cancelar aposta', 'error'))
      }
    },
    computed: {
      sortedBets() {
        const sortBy = this.sortBy;
        const sortOrder = this.sortOrder;
        const numericValues = ['id', 'bet_value', 'prize'];

        if (numericValues.indexOf(sortBy) < 0)
          return _.orderBy(this.bets, sortBy, sortOrder);

        return _.orderBy(this.bets, bet => parseFloat(bet[`${sortBy}`]), sortOrder)
      }
    },
    created () {
      this.debounceSearch = _.debounce(() => this.searchBets(), 500)
    },
    mounted () {
      this.getCompanies();
      this.getManagers();
      this.getSellers();
      this.startDate = moment().format('YYYY-MM-DD');
      this.finalDate = this.startDate;
      this.getBets()
    }
  }
</script>

<style scoped>
    .wasOpened {
      background-color: #01567d;
      border-color: #0b4763;
      color: whitesmoke;
    }
</style>
