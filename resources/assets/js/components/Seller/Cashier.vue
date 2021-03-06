<template>
  <div>
    <div class="btn-group btn-group-full p-b">
      <button :class="{'btn-primary': status === 'all'}" @click.prevent="status = 'all'" class="btn btn-default">Todos</button>
      <button :class="{'btn-primary': status === 'win'}" @click.prevent="status = 'win'" class="btn btn-default">Premiadas</button>
    </div>
    <div class="btn-group btn-group-full p-b">
        <div class="form-group">
          <input v-model="startDate" @change="getBets" type="date" class="form-control">
        </div>
      </div>
    <div class="block full bordered hidden">
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
              <b>{{ saldo | formatMoney }}</b>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="block full bordered">
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td><b>Bilhete</b></td>
            <td><b>Código</b></td>
            <td><b>Nome</b></td>
            <td><b>Jogos</b></td>
            <td><b>Valor</b></td>
            <td><b>Prêmio</b></td>
            <td><b>Comissão</b></td>
            <td><b>Data</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="bet in bets" :key="bet.id">
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
            <td><strong>{{ bet.client_name }}</strong></td>
            <td>{{ bet.tips_quantity }}</td>
            <td>{{ bet.bet_value | formatMoney }}</td>
            <td>
              <span class="label"
                :class="{'label-success': bet.status === 'win', 'label-danger': bet.status === 'lose', 'label-warning': bet.status === 'canceled', 'label-default': bet.status === 'pending'}">
                <b>{{ bet.prize | formatMoney }}</b>
              </span>
            </td>
            <td>{{ bet.commission | formatMoney }}</td>
            <td>{{ bet.human_date }}</td>
          </tr>
          <tr v-if="bets.length === 0">
            <td colspan="8" class="text-center">
              <strong>Nenhuma aposta encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <modal ref="modal" title="Resumo da simulação" :bet.sync="bet"></modal>
  </div>
</template>

<script>
  import modal from '../shared/Modal/BetResume'

  export default {
    components: {modal},
    data() {
      return {
        bet: null,
        latestOpenedBet: null,
        startDate: null,
        endDate: null,
        status: 'all',
        bets: []
      }
    },
    methods: {
      getBets: function () {
        this.$http.get('/seller/cashier/report', {
          params: {
            start_date: this.startDate,
            end_date: this.startDate,
            status: this.status
          }
        }).then(response => {
          response.json().then(res => this.bets = res.bets);
        }, error => console.log(error));
      },
      openBet: function (bet) {
        this.latestOpenedBet = bet.id
        this.bet = null;
        this.$http.get('/api/bet/' + bet.id)
          .then(response => {
            response.json().then(res => {
              this.bet = res.bet;
              this.$refs.modal.showModal();
            });
          }, err => console.error(err));
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
    },
    watch: {
      startDate: function () {
        this.getBets();
      },
      status: function () {
        this.getBets();
      },
    },
    created() {
      this.startDate = new Date().toISOString().slice(0, 10);
      this.endDate = this.startDate;
      this.getBets();
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
