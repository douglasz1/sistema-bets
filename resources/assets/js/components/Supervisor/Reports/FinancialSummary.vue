<template>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Resumo financeiro - {{ company.company.name }}</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-borderless table-striped table-hover text-right">
                <thead>
                <tr>
                    <td><b>Gerente</b></td>
                    <td><b>Apostas</b></td>
                    <td><b>Entrada</b></td>
                    <td><b>Saída</b></td>
                    <td><b>Comissão</b></td>
                    <td><b>Gastos</b></td>
                    <td><b>Subtotal</b></td>
                    <td><b>% Gerente</b></td>
                    <td><b>Depósitos</b></td>
                    <td><b>Ap. %</b></td>
                    <td><b>Total</b></td>
                </tr>
                </thead>

                <tbody v-if="company.managers.length === 0">
                <tr>
                    <td colspan="11" class="text-center">
                        <strong>Nenhuma aposta encontrada</strong>
                    </td>
                </tr>
                </tbody>

                <tbody v-for="manager in company.managers" :key="manager.manager.id">
                <tr v-if="showSellers" v-for="seller in manager.sellers" :key="seller.id" class="seller">
                  <td>{{ seller.seller.name }}</td>
                  <td>
                    <div v-if="detailed">
                      Bolão: {{ seller.bolaoQtd }}<br>
                      Bets: {{ seller.betsQty }}<br>
                      Dezenas: 0<br>
                    </div>
                    <span v-if="detailed">Total:</span>
                    {{ seller.bolaoQtd + seller.betsQty }}
                  </td>
                  <td>
                    <div v-if="detailed">
                      {{ seller.entradaBolao | formatMoney }}<br>
                      {{ seller.betsValue | formatMoney }}<br>
                      R$ 0<br>
                    </div>
                    {{ seller.entradaBolao + seller.betsValue | formatMoney }}
                  </td>
                  <td>
                    ({{ seller.winnersQty }})
                    {{ seller.winnersValue | formatMoney }}
                  </td>
                  <td>
                    <div v-if="detailed">
                      {{ seller.comissaoBolao | formatMoney }}<br>
                      {{ seller.commissions | formatMoney }}<br>
                      R$ 0<br>
                    </div>
                    {{ seller.comissaoBolao + seller.commissions | formatMoney }}
                  </td>
                  <td>{{ seller.expenses | formatMoney }}</td>
                  <td :class="{'text-success': seller.subtotal > 0, 'text-danger': seller.subtotal < 0}">
                    <b>{{ seller.subtotal | formatMoney }}</b>
                  </td>
                  <td>-</td>
                  <td>{{ seller.payments | formatMoney }}</td>
                  <td>-</td>
                  <td>-</td>
                </tr>
                <tr class="manager" :class="{'open': showSellers}">
                    <td>
                        {{ manager.manager.name }}
                        ({{ manager.sellers.length }})
                    </td>
                    <td>
                      <div v-if="detailed">
                        Bolão: {{ manager.summary.bolaoQtd }}<br>
                        Bets: {{ manager.summary.betsQty }}<br>
                        Dezenas: 0
                      </div>
                      <span v-if="detailed">Total:</span>
                      {{ manager.summary.bolaoQtd + manager.summary.betsQty }}
                    </td>
                    <td>
                      <div v-if="detailed">
                        {{ manager.summary.entradaBolao | formatMoney }}<br>
                        {{ manager.summary.betsValue | formatMoney }}<br>
                        R$ 0
                      </div>
                      {{ manager.summary.entradaBolao + manager.summary.betsValue | formatMoney }}
                    </td>
                    <td>
                      ({{ manager.summary.winnersQty }})
                      {{ manager.summary.winnersValue | formatMoney }}
                    </td>
                    <td>
                      <div v-if="detailed">
                        {{ manager.summary.comissaoBolao | formatMoney }}<br>
                        {{ manager.summary.commissions | formatMoney }}<br>
                        R$ 0
                      </div>
                      {{ manager.summary.comissaoBolao + manager.summary.commissions | formatMoney }}
                    </td>
                    <td>{{ manager.summary.expenses | formatMoney }}</td>
                    <td>{{ manager.summary.subtotal | formatMoney }}</td>
                    <td>
                      {{ manager.summary.managerCommission | formatMoney }}
                    </td>
                    <td>{{ manager.summary.payments | formatMoney }}</td>
                    <td>{{ manager.summary.managerUse }}%</td>
                    <td :class="{'text-success': manager.summary.total > 0, 'text-danger': manager.summary.total < 0}">
                        <b>{{ manager.summary.total | formatMoney }}</b>
                    </td>
                </tr>
                </tbody>

                <tbody>
                <tr class="tfoot">
                    <td>
                        <b>Total: ({{ company.managers.length }} G)</b>
                        <b>({{ totalSellers }} V)</b>
                    </td>
                    <td>
                      <div v-if="detailed">
                        Bolão: {{ bolaoQuantidadeTotal }}<br>
                        Bets: {{ totalQty }}<br>
                        Dezenas: 0<br>
                      </div>
                      <span v-if="detailed">Total:</span>
                      {{ bolaoQuantidadeTotal + totalQty }}
                    </td>
                    <td>
                      <div v-if="detailed">
                        {{ bolaoEntradaTotal | formatMoney }}<br>
                        {{ totalBetValues | formatMoney }}<br>
                        R$ 0<br>
                      </div>
                      {{ bolaoEntradaTotal + totalBetValues | formatMoney }}
                    </td>
                    <td>
                      ({{ totalWinnersQty }})
                      {{ totalWinnersValue | formatMoney }}
                    </td>
                    <td>
                      <div v-if="detailed">
                        {{ bolaoComissaoTotal | formatMoney }}<br>
                        {{ totalCommissions | formatMoney }}<br>
                        R$ 0<br>
                      </div>
                      {{ bolaoComissaoTotal + totalCommissions | formatMoney }}
                    </td>
                    <td>{{ totalExpenses | formatMoney }}</td>
                    <td>{{ subtotal | formatMoney }}</td>
                    <td>{{ totalManagersCommissions | formatMoney }}</td>
                    <td>{{ totalPayments | formatMoney }}</td>
                    <td>{{ totalManagersUse }}%</td>
                    <td :class="{'text-success': total > 0, 'text-danger': total < 0}">
                        <b>{{ total | formatMoney }}</b>
                    </td>
                </tr>
                </tbody>

                <tbody>
                <tr class="text-left2">
                    <td colspan="12">
                        <span v-for="supervisor in company.company.supervisors" :key="supervisor.id">
                            ({{ supervisor.name }}
                            {{ supervisor.pivot.percentage }}%
                            <b :class="{'text-success': (supervisor.pivot.percentage / 100) * total > 0, 'text-danger': (supervisor.pivot.percentage / 100) * total < 0 }">
                                {{ ((supervisor.pivot.percentage / 100) * total) | formatMoney
                                }}
                            </b>)
                            &nbsp;&nbsp;
                        </span>
                    </td>
                </tr>
                </tbody>

                <tr>
                    <td colspan="12" class="text-center" style="text-transform: uppercase;">
                        <h5><b>Não fizeram apostas</b></h5>
                    </td>
                </tr>
                <tr v-for="seller in company.sellersNotBet" :key="seller.id">
                    <td>{{ seller.name }}</td>
                    <td colspan="11"></td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      company: {
        type: Object,
        required: true,
        default: {}
      },
      showSellers: {
        type: Boolean,
        required: false,
        default: false
      },
      detailed: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    computed: {
      totalQty() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseInt(manager.summary.betsQty)
        }, 0)
      },
      bolaoQuantidadeTotal() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseInt(manager.summary.bolaoQtd)
        }, 0)
      },
      bolaoEntradaTotal() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.entradaBolao)
        }, 0)
      },
      bolaoComissaoTotal() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.comissaoBolao)
        }, 0)
      },
      totalBetValues() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.betsValue)
        }, 0)
      },
      totalWinnersQty() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseInt(manager.summary.winnersQty)
        }, 0)
      },
      totalWinnersValue() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.winnersValue)
        }, 0)
      },
      totalCommissions() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.commissions)
        }, 0)
      },
      totalExpenses() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.expenses)
        }, 0)
      },
      totalPayments() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.payments)
        }, 0)
      },
      totalManagersCommissions() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.managerCommission)
        }, 0)
      },
      totalManagersUse() {
        const entrada = this.totalBetValues;
        const total = this.total;
        return parseInt((total / entrada) * 100)
      },
      subtotal() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.subtotal)
        }, 0)
      },
      total() {
        const list = this.company.managers
        return _.reduce(list, (sum, manager) => {
          return sum + parseFloat(manager.summary.total)
        }, 0)
      },
      totalSellers() {
        const list = this.company.managers
        return _.sumBy(list, (manager) => {
          return manager.sellers.length
        })
      }
    },
  }
</script>

<style scoped>
    .table .manager.open,
    .table .manager.open:hover,
    .table-hover > tbody > tr.manager.open:hover > td {
        border-bottom: solid 2px #98c1e4;
        border-top: solid 2px #98c1e4;
        background-color: #98c1e4;
        color: #FFFFFF;
    }

    .tfoot > td,
    .tfoot:hover > td {
        background-color: #4678a1;
        color: #FFFFFF;
    }

    td { white-space: nowrap; }
</style>
