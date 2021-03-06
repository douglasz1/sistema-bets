<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space tres-botoes p-tb p-lr">
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
            placeholder="Operadores"
            :options="sellers">
          </v-select>
        </div>
      </div>
      <div class="btn-group btn-group-full tres-botoes p-lr p-b">
        <button :class="{'btn-primary': status === 'all'}" @click.prevent="status = 'all'" class="btn btn-default">Todos</button>
        <button :class="{'btn-primary': status === 'partial'}" @click.prevent="status = 'partial'" class="btn btn-default">P. Parcial</button>
        <button :class="{'btn-primary': status === 'lose'}" @click.prevent="status = 'lose'" class="btn btn-default">Perdidas</button>
        <button :class="{'btn-primary': status === 'win'}" @click.prevent="status = 'win'" class="btn btn-default">Premiadas</button>
        <button :class="{'btn-primary': status === 'pending'}" @click.prevent="status = 'pending'" class="btn btn-default">Andamento</button>
        <button :class="{'btn-primary': status === 'canceled'}" @click.prevent="status = 'canceled'" class="btn btn-default">Canceladas</button>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-b p-lr">
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
        <h2>Resumo de apostas</h2>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td><b>Jogos</b></td>
            <td><b>Aberto</b></td>
            <td><b>Prêmio</b></td>
            <td><b>Entrada</b></td>
            <td><b>Comissão</b></td>
            <td><b>Saída</b></td>
            <td><b>Saldo</b></td>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ bets.length }}</td>
            <td>{{ pendingQty }}</td>
            <td>{{ winnersQty }}</td>
            <td class="text-success">{{ betInput | formatMoney }}</td>
            <td class="text-warning">{{ betCommissions | formatMoney }}</td>
            <td class="text-danger">{{ betWinners | formatMoney }}</td>
            <td
              :class="{'text-success': saldo > 0, 'text-danger': saldo < 0}">
              {{ saldo | formatMoney }}
            </td>
          </tr>
          </tbody>
        </table>
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
            <td><b>Bilhete</b></td>
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
            <td><b>Nome</b></td>
            <td><b>Jogos</b></td>
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
            <td><b>Comissão</b></td>
            <td><b>Cancelar</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="bet in sortedBets" :key="bet.id">
            <td>
              <button
                @click.prevent="openBet(bet)"
                :class="{'wasOpened': bet.id === latestOpenedBet}"
                class="btn btn-info">
                <i class="fa fa-folder-open"></i>
              </button>
            </td>
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
            <td>{{ formatDate(bet.created_at) }}</td>
            <td>{{ bet.seller.name }}</td>
            <td><b>{{ bet.client_name }}</b></td>
            <td>{{ bet.tips_quantity }}</td>
            <td><b>{{ bet.bet_value | formatMoney }}</b></td>
            <td>
              <span class="label"
                :class="{'label-success': bet.status === 'win', 'label-danger': bet.status === 'lose', 'label-warning': bet.status === 'canceled', 'label-default': bet.status === 'pending'}">
                <b>{{ bet.prize | formatMoney }}</b>
              </span>
            </td>
            <td>{{ bet.commission | formatMoney }}</td>
            <td>
              <button v-if="bet.status !== 'canceled'"
                      @click.prevent="cancelBet(bet)"
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
    <bet-summary-modal ref="betSummaryModal"></bet-summary-modal>
  </div>
</template>

<script>
  import moment from 'moment'
  import vSelect from 'vue-select'
  import summaryBoxes from '../../shared/SummaryBoxes.vue'
  import betSummaryModal from './BetSummaryModal'

  export default {
    components: {vSelect, summaryBoxes, betSummaryModal},
    data() {
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
          this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
          return
        }
        this.sortBy = sortBy
        this.sortOrder = 'desc'
      },
      formatDate(date) {
        return moment(date).format('DD/MM HH:mm:ss')
      },
      debounceSearch() {
      },
      searchBets() {
        this.loading = true
        this.$http
          .post('/supervisor/cashier/search', {betId: this.betId})
          .then(response => this.bets = response.body.bets)
          .catch(() => this.$swal(
            'Erro ao listar apostas!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => this.loading = false)
      },
      getBets() {
        this.loading = true
        this.$http
          .get('/supervisor/cashier/report', {
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
          .catch(() => this.$swal(
            'Erro ao listar apostas!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => this.loading = false)
      },
      getCompanies() {
        this.$http
          .get('/supervisor/companies/pluck')
          .then(response => {
            this.companies = response.body.companies
            this.companies.unshift({label: 'Todas', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao listar empresas!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      getSellers(companyId, managerId) {
        this.$http.get('/supervisor/sellers/pluck', {params: {companyId, managerId}})
          .then(response => {
            this.sellers = response.body.sellers
            this.sellers.unshift({label: 'Todos', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao listar operadores!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      getManagers(companyId) {
        this.$http
          .get('/supervisor/managers/pluck', {params: {companyId: companyId}})
          .then(response => {
            this.managers = response.body.managers
            this.managers.unshift({label: 'Todos', value: 0})
          })
          .catch(() => this.$swal(
            'Erro ao listar gerentes!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
      },
      onChangeCompany(company) {
        this.company = company
        this.manager = null
        this.managers = []
        this.seller = null
        this.sellers = []

        if (company !== null) {
          let companyId = company.value !== 0 ? company.value : null
          this.getManagers(companyId)
          this.getSellers(companyId, null)
        }
      },
      onChangeManager(manager) {
        this.manager = manager
        this.seller = null
        this.sellers = []

        if (manager !== null) {
          let managerId = manager.value !== 0 ? manager.value : null
          this.getSellers(null, managerId)
        }
      },
      openBet(bet) {
        this.latestOpenedBet = bet.id
        this.$refs.betSummaryModal.getBet(bet.id)
      },
      cancelBet(bet) {
        this.$http.post(`/supervisor/bets/cancel/${bet.id}`)
          .then(() => {
            bet.status = 'canceled'
            this.$swal(
              'Cancelado!',
              'A aposta foi cancelada com sucesso.',
              'success'
            )
          })
          .catch(() => this.$swal(
            'Erro ao cancelar!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
      },
    },
    computed: {
      betInput () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status !== 'canceled' ? parseFloat(bet.bet_value) : 0);
        return parseFloat(value).toFixed(2);
      },
      betWinners () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status === 'win' ? parseFloat(bet.prize) : 0);
        return parseFloat(value).toFixed(2);
      },
      betCommissions () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status !== 'canceled' ? parseFloat(bet.commission) : 0);
        return parseFloat(value).toFixed(2);
      },
      winnersQty () {
        const list = this.bets
        let winners = _.filter(list, bet => bet.status === 'win')
        return winners.length
      },
      pendingQty () {
        const list = this.bets
        let pending = _.filter(list, bet => bet.status === 'pending')
        return pending.length
      },
      saldo () {
        let value = parseFloat(this.betInput) - parseFloat(this.betWinners) - parseFloat(this.betCommissions);
        return value.toFixed(2);
      },
      sortedBets() {
        const sortBy = this.sortBy
        const sortOrder = this.sortOrder
        const numericValues = ['id', 'bet_value', 'prize']

        if (numericValues.indexOf(sortBy) < 0)
          return _.orderBy(this.bets, sortBy, sortOrder)

        return _.orderBy(this.bets, bet => parseFloat(bet[`${sortBy}`]), sortOrder)
      }
    },
    created() {
      this.startDate = new Date().toISOString().slice(0, 10)
      this.finalDate = this.startDate
      this.getCompanies()
      this.getManagers()
      this.getSellers()
      this.getBets()
      this.debounceSearch = _.debounce(() => this.searchBets(), 500)
    }
  }
</script>

<style scoped>
  .link {
    cursor: pointer;
  }
  thead > tr div {
    display: flex;
    align-items: center;
  }
  td .fa-chevron-down,
  td .fa-chevron-up {
    font-size: .5em;
    margin-left: 5px;
  }
  .wasOpened {
    background-color: #01567d;
    border-color: #0b4763;
    color: whitesmoke;
  }
</style>
