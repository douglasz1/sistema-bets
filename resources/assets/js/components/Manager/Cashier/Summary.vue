<template>
  <div>
    <div class="block full bordered">
        <div class="block-title">
          <h2>Opções de filtragem</h2>
        </div>
        <div class="btn-group btn-group-full p-lr p-tb">
          <button :class="{'btn-primary': status === 'all'}" @click.prevent="status = 'all'" class="btn btn-default">Todos</button>
          <button :class="{'btn-primary': status === 'pending'}" @click.prevent="status = 'pending'" class="btn btn-default">Andamento</button>
          <button :class="{'btn-primary': status === 'lose'}" @click.prevent="status = 'lose'" class="btn btn-default">Perdidas</button>
          <button :class="{'btn-primary': status === 'win'}" @click.prevent="status = 'win'" class="btn btn-default">Premiadas</button>
        </div>
        <div class="btn-group btn-group-full p-b p-lr">
          <div class="form-group">
            <input v-model="startDate" type="date" class="form-control">
          </div>
          <div class="form-group p-lr">
            <input v-model="endDate" type="date" class="form-control">
          </div>
          <button @click="getBets" type="submit" class="btn btn-success">
            <i class="fa fa-filter"></i> Filtrar
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
              <span class="label label-success" v-if="bet.status === 'win'">Ganhou</span>
              <span class="label label-danger" v-if="bet.status === 'lose'">Perdeu</span>
            </td>
            <td><strong>{{ bet.client_name }}</strong></td>
            <td>{{ bet.tips_quantity }}</td>
            <td>{{ bet.bet_value | formatMoney }}</td>
            <td class="text-primary"><b>{{ bet.prize | formatMoney }}</b></td>
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
    <modal ref="modal" title="Resumo da aposta" :bet.sync="bet"></modal>
  </div>
</template>

<script>
  import modal from '../../shared/Modal/BetResume.vue'
  import summaryBoxes from '../../shared/SummaryBoxes.vue'
  import {showToast} from '../../../functions/showToast'

  export default {
    components: {modal, summaryBoxes},
    data () {
      return {
        bet: null,
        startDate: null,
        endDate: null,
        status: 'all',
        latestOpenedBet: null,
        bets: []
      }
    },
    methods: {
      getBets() {
        this.$http.get('/manager/cashier/summary/report', {
          params: {
            start_date: this.startDate,
            end_date: this.endDate,
            status: this.status
          }
        }).then(response => {
          response.json().then(res => this.bets = res.bets);
        }, err => showToast(err.data.message));
      },
      openBet(bet) {
        this.latestOpenedBet = bet.id;
        this.bet = null;
        this.$http.get('/api/bet/' + bet.id)
        .then(response => {
          response.json().then(res => {
            this.bet = res.bet;
            this.$refs.modal.showModal();
          });
        }, err => showToast(err.data.message));
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
