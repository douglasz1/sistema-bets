<template>
  <div>
    <div class="btn-group btn-group-full btn-group-space p-tb">
      <div class="form-group margin-between">
        <input v-model="startDate" type="date" class="form-control">
      </div>
      <div class="form-group margin-between">
        <input v-model="endDate" type="date" class="form-control">
      </div>
      <div class="form-group margin-between">
        <select v-model="clientId" class="form-control" required>
          <option value="0" selected>Todos os clientes</option>
          <option :value="client.id" v-for="client in clients" :key="client.id">
            {{ client.name }}
          </option>
        </select>
      </div>
      <button @click="getBets" class="btn btn-success margin-between">
        <i class="fa fa-filter"></i> Filtrar
      </button>
    </div>
    <div class="row">
      <div class="col-sm-6 col-lg-6 col-xs-6">
        <div class="widget text-light" :class="{'text-success': total > 0, 'text-danger': total < 0}">
          <div class="widget-content widget-content-mini text-center clearfix">
            <span>PRESTAÇÃO</span>
            <h2 class="widget-heading h3">
              <strong><span>{{ total | formatMoney }}</span></strong>
            </h2>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-6 col-xs-6">
        <div class="widget">
          <div class="widget-content widget-content-mini text-center clearfix">
            <span class="text-warning">COMISSÃO</span>
            <h2 class="widget-heading text-warning h3">
              <strong><span>{{ commissions | formatMoney }}</span></strong>
            </h2>
          </div>
        </div>
      </div>
    </div>
    <!-- bets -->
    <div class="block full bordered">
      <div class="block-title">
        <h2>ESPORTES</h2>
      </div>
      <table class="table table-vcenter table-striped table-borderless table-hover">
        <tbody v-if="bets">
        <tr class="text-success">
          <td>Entrada</td>
          <td>{{ bets.betsValue | formatMoney }}</td>
        </tr>
        <tr class="text-danger">
          <td>Saída</td>
          <td>{{ bets.winnersValue | formatMoney }}</td>
        </tr>
        <tr class="text-warning">
          <td>Comissão</td>
          <td>{{ bets.commission + profitCommissions | formatMoney }}</td>
        </tr>
        <tr class="text-laranja">
          <td>Gastos</td>
          <td>{{ expenses | formatMoney }}</td>
        </tr>
        <tr class="text-laranja">
          <td>Depósitos</td>
          <td>{{ payments | formatMoney }}</td>
        </tr>
        <tr :class="{'text-success': bets.subtotal > 0, 'text-danger': bets.subtotal < 0}">
          <td>Fechamento</td>
          <td>
            <b>{{ bets.subtotal | formatMoney }}</b>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <!-- bolao -->
    <div class="block full bordered">
      <div class="block-title">
        <h2>BOLÃO</h2>
      </div>
      <table class="table table-vcenter table-striped table-borderless table-hover">
        <tbody v-if="bets">
        <tr class="text-success">
          <td>Entrada</td>
          <td>{{ bets.entradaBolao | formatMoney }}</td>
        </tr>
        <tr class="text-warning">
          <td>Comissão</td>
          <td>{{ bets.comissaoBolao | formatMoney }}</td>
        </tr>
        <tr :class="{'text-success': bets.bolaoSubtotal > 0, 'text-danger': bets.bolaoSubtotal < 0}">
          <td>Fechamento</td>
          <td>
            <b>{{ bets.bolaoSubtotal | formatMoney }}</b>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import {showToast} from '../../functions/showToast'

  export default {
    data() {
      return {
        startDate: null,
        endDate: null,
        seller: null,
        bets: [],
        clients: [],
        clientId: '0',
        showProfitCommission: false
      }
    },
    methods: {
      getBets: function () {
        this.$http
          .get('/seller/financial/report', {
            params: {
              startDate: this.startDate,
              endDate: this.endDate,
              clientId: this.clientId
            }
          })
          .then(response => {
            this.seller = response.body.seller
            this.bets = response.body.bets
            this.showProfitCommission = this.seller.profitPercentage > 0
          })
          .catch(() => showToast('Erro ao buscar dados'))
      },

      getClients() {
        this.loading = true;
        this.$http.get('/seller/clients/list', {
        }).then(response => {
          response.json().then(res => {
            this.clients = res.clients;
            this.loading = false;
          });
        }, err => {
          console.log(err);
          this.loading = false;
        });
      },




    },
    created() {
      this.startDate = new Date().toISOString().slice(0, 10);
      this.endDate = this.startDate;
      this.getBets();
      this.getClients();
    },
    computed: {
      betsValue() {
        return parseFloat(this.bets.betsValue) + parseFloat(this.bets.entradaBolao)
      },
      winnersValue() {
        return this.bets.winnersValue
      },
      commissions() {
        return parseFloat(this.bets.commission) + parseFloat(this.bets.comissaoBolao) + parseFloat(this.bets.profitCommission)
      },
      expenses() {
        return this.bets.expenses
      },
      payments() {
        return this.bets.payments
      },
      subtotal() {
        return parseFloat(this.bets.subtotal) + parseFloat(this.bets.bolaoSubtotal)
      },
      profitCommissions() {
        return this.bets.profitCommission
      },
      total() {
        return parseFloat(this.bets.total) + parseFloat(this.bets.bolaoSubtotal)
      }
    }
  }
</script>

<style scoped>
  tr > td {
    text-align: left;
    width: 50%;
    font-weight: bold;
  }

  .widget {
    border: solid 1px #0e0d0d;
    background-color: var(--bg-game-status);
  }

  .text-warning {
    color: #fff048 !important;
  }

  .text-laranja {
    color: orange !important;
  }

  @media screen and (max-width: 500px) {
    .btn-group-space > button {
      margin-right: 0 !important;
      margin-top: 10px !important;
    }
    select{
      margin-top: 10px !important;
    }
  }
</style>
