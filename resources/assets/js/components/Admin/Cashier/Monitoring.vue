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
            placeholder="Operador"
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
          <tr v-for="(bet, index) in sortedBets" :key="bet.id">
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
              <button
                v-if="canDeleteBets"
                @click.prevent="removeBet(bet, index)"
                class="btn btn-warning">
                <i class="fa fa-trash"></i>
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
            <td><b>Saída</b></td>
            <td><b>Comissão</b></td>
            <td><b>Saldo</b></td>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ bets.length }}</td>
            <td>{{ pendingQty }}</td>
            <td>{{ winnersQty }}</td>
            <td class="text-success"><b>{{ betInput | formatMoney }}</b></td>
            <td>
              <span class="label label-danger">
                <b>{{ betWinners | formatMoney }}</b>
              </span>
            </td>
            <td class="text-warning"><b>{{ betCommissions | formatMoney }}</b></td>
            <td
              :class="{'text-success': saldo > 0, 'text-danger': saldo < 0}">
              <b>{{ saldo | formatMoney }}</b>
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
  import {showToast} from '../../../functions/showToast'
  import betSummaryModal from './BetSummaryModal'

  export default {
    components: {vSelect, betSummaryModal},
    data() {
      return {
        canDeleteBets: (process.env.ADMIN_DELETE_BETS == 'true'),
        loading: false,
        startDate: null,
        finalDate: null,
        status: 'win',
        latestOpenedBet: null,
        betId: null,
        bets: [],
        sortBy: 'created_at',
        sortOrder: 'desc',
        managers: [],
        companies: [],
        sellers: [],
        manager: null,
        company: null,
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
        return moment(date).format('DD/MM HH:mm')
      },
      debounceSearch() {
      },
      searchBets() {
        this.$http
          .post('/admin/cashier/search', {betId: this.betId})
          .then(response => {
            this.bets = response.body.bets.map(item => item)
            this.loading = false
          })
          .catch(() => {
            this.loading = false
            showToast('Erro ao listar apostas')
          })
      },
      getBets() {
        this.loading = true

        this.$http
          .get('/admin/cashier/report', {
            params: {
              startDate: this.startDate,
              finalDate: this.finalDate,
              status: this.status,
              companyId: this.company ? this.company.value : null,
              managerId: this.manager ? this.manager.value : null,
              sellerId: this.seller ? this.seller.value : null
            }
          })
          .then(response => {
            this.bets = response.body.bets.map(item => item)
            this.loading = false
          })
          .catch(() => {
            this.loading = false
            showToast('Erro ao listar apostas')
          })
      },
      getCompanies() {
        this.$http.get('/admin/companies/pluck')
          .then(response => {
            this.companies = response.body.companies.map(item => item)
            this.companies.unshift({label: 'Todas', value: 0})
          }, () => showToast('Erro ao listar empresas'))
      },
      getManagers(companyId) {
        this.$http.get('/admin/managers/pluck', {params: {companyId: companyId}})
          .then(response => {
            this.managers = response.body.managers.map(item => item)
            this.managers.unshift({label: 'Todos', value: 0})
          }, err => showToast('Erro ao listar gerentes'))
      },
      getSellers(companyId, managerId) {
        this.$http.get('/admin/sellers/pluck', {params: {companyId, managerId}})
          .then(response => {
            this.sellers = response.body.sellers.map(item => item)
            this.sellers.unshift({label: 'Todos', value: 0})
          }, err => showToast('Erro ao listar operadores'))
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
      cancelBet(bet) {
        this.$http.post(`/admin/bets/cancel/${bet.id}`)
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
      removeBet(bet, index) {
        this.$http.get(`/admin/bets/remove/${bet.id}`)
          .then(response => {
            if (response.ok) {
              this.bets.splice(index, 1)
              this.$swal(
                'Aposta removida!',
                'A aposta foi removida com sucesso.',
                'success'
              )
            }
          })
          .catch(() => this.$swal(
            'Erro ao remover aposta!',
            'Ocorreu algum erro ao remover, por favor, tente novamente mais tarde',
            'error'
          ))
      }
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
    },
    mounted() {

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

  tbody tr td:nth-child(2) {
    width: 65px;
    max-width: 65px;
    white-space: normal;
  }
</style>
