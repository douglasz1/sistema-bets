<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
        <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
          <div class="form-group">
            <input v-model="startDate" type="date" class="form-control">
          </div>
          <div class="form-group">
            <input v-model="finalDate" type="date" class="form-control">
          </div>
          <div class="form-group">
            <v-select
              v-model="seller"
              placeholder="Nome"
              :options="sellersList">
            </v-select>
          </div>
          <button
            @click.prevent="getBets" class="btn"
            :class="{'btn-success': !isLoading, 'btn-primary': isLoading}">
              <i v-if="isLoading" class="fa fa-spinner fa-spin"></i>
              <i class="fa fa-filter" v-else></i> Filtrar
          </button>
        </div>
    </div>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Resumo financeiro por vendedor</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-borderless table-striped table-hover text-right">
                <thead>
                <tr>
                    <td class="text-left"><b>Vendedor</b></td>
                    <td><b>Entrada</b></td>
                    <td><b>Saída</b></td>
                    <td><b>Comissão</b></td>
                    <td><b>Gastos</b></td>
                    <td><b>Subtotal</b></td>
                    <td><b>C. lucro</b></td>
                    <td><b>Depósitos</b></td>
                    <td><b>Total</b></td>
                </tr>
                </thead>
                <tbody>
                <tr v-if="sellers.length === 0">
                    <td colspan="9" class="text-center">
                        <strong>Nenhuma aposta encontrada</strong>
                    </td>
                </tr>
                <tr v-for="seller in sellers" :key="seller.seller.id">
                    <td class="text-left">
                      <i class="fa fa-user"></i>
                      &nbsp;{{ seller.seller.name }}
                    </td>
                    <td>
                      Jogos: {{ seller.betsValue | formatMoney }}<br>
                      Bolão: {{ seller.entradaBolao | formatMoney }}<br>
                      <b class="text-success">Total: {{ seller.betsValue + seller.entradaBolao | formatMoney }}</b>
                    </td>
                    <td>
                      {{ seller.winnersQty }} /
                      {{ seller.winnersValue | formatMoney }}<br>
                      -<br>
                      <b class="text-danger">{{ seller.winnersValue | formatMoney }}</b>
                    </td>
                    <td>
                      {{ seller.commission | formatMoney }}<br>
                      {{ seller.comissaoBolao | formatMoney }}<br>
                      <b class="text-warning">{{ seller.commission + seller.comissaoBolao | formatMoney }}</b>
                    </td>
                    <td>
                      {{ seller.expenses | formatMoney }}<br>
                      R$ 0,00<br>
                      {{ seller.expenses | formatMoney }}
                    </td>
                    <td>
                      {{ seller.subtotal | formatMoney }}<br>
                      {{ seller.bolaoSubtotal | formatMoney }}<br>
                      <b>{{ seller.subtotal + seller.bolaoSubtotal | formatMoney }}</b>
                    </td>
                    <td>
                      {{ seller.profitCommission | formatMoney }}<br>
                      {{ seller.comissaoLucroBolao | formatMoney }}<br>
                      <b>{{ seller.profitCommission + seller.comissaoLucroBolao | formatMoney }}</b>
                    </td>
                    <td>{{ seller.payments | formatMoney }}</td>
                    <td>
                      {{ seller.total | formatMoney }}<br>
                      {{ seller.totalBolao | formatMoney }}<br>
                      <b :class="{'text-success': seller.totalGeral > 0, 'text-danger': seller.totalGeral < 0}">{{ seller.totalGeral | formatMoney }}</b>
                    </td>
                </tr>
                <!-- <tfoot v-if="bets.length !== 0"> -->
                <tr class="tfoot">
                    <td><b>Subtotal:</b></td>
                    <td>{{ totalBetValues | formatMoney }}</td>
                    <td>
                        {{ totalWinnersQty }} /
                        {{ totalWinnersValue | formatMoney }}
                    </td>
                    <td>{{ totalCommission | formatMoney }}</td>
                    <td>{{ totalExpenses | formatMoney }}</td>
                    <td>{{ subtotalValues | formatMoney }}</td>
                    <td>{{ profitCommission + comissaoLucroBolao | formatMoney }}</td>
                    <td>{{ totalPayments | formatMoney }}</td>
                    <td :class="{'text-success': totalValues > 0, 'text-danger': totalValues < 0}">
                        <b>{{ totalValues | formatMoney }}</b>
                    </td>
                </tr>
                <tr class="tfoot">
                    <td><b>Gerente:</b></td>
                    <td colspan="7"></td>
                    <td>{{ managerCommission | formatMoney }}</td>
                </tr>
                <tr class="tfoot bg-total">
                    <td><b>Valor total:</b></td>
                    <td>
                        {{ sellers.length }}
                        <span v-if="sellers.length === 1">vendedor</span>
                        <span v-else>vendedores</span>
                    </td>
                    <td colspan="6"></td>
                    <td :class="{'text-success': totalGeral > 0, 'text-danger': totalGeral < 0}">
                        <b>{{ totalGeral | formatMoney }}</b>
                    </td>
                </tr>
                <!-- </tfoot> -->
                <tr>
                    <td colspan="9" class="text-center" style="text-transform: uppercase;">
                        <h5><b>Não fizeram apostas</b></h5>
                    </td>
                </tr>
                <tr>
                    <td class="text-left"><b>Nome</b></td>
                    <td colspan="8"></td>
                </tr>
                <tr v-for="seller in sellersNotBet" :key="seller.id">
                    <td class="text-left">
                      <i class="fa fa-user"></i>
                      &nbsp;{{ seller.name }}
                    </td>
                    <td colspan="8"></td>
                </tr>
                <tr class="tfoot">
                    <td><b>Total:</b></td>
                    <td>
                        {{ sellersNotBet.length }}
                        <span v-if="sellersNotBet.length === 1">vendedor</span>
                        <span v-else>vendedores</span>
                    </td>
                    <td colspan="7"></td>
                </tr>
                <tr class="tfoot bg-total">
                    <td><b>Total de vendedores:</b></td>
                    <td>
                        {{ sellersNotBet.length + sellers.length }}
                        <span v-if="(sellersNotBet.length + sellers.length) === 1">vendedor</span>
                        <span v-else>vendedores</span>
                    </td>
                    <td colspan="7"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>

<script>
  import vSelect from 'vue-select'
  import {showToast} from '../../../functions/showToast'

  export default {
    components: {vSelect},
    data() {
      return {
        isLoading: false,
        startDate: null,
        finalDate: null,
        sellersList: [],
        sellers: [],
        sellersNotBet: [],
        seller: null,
        manager: {manager_commission: 0}
      }
    },
    methods: {
      getBets () {
        this.isLoading = true

        this.$http
          .get('/manager/financial/general/report', {
            params: {
              startDate: this.startDate,
              finalDate: this.finalDate,
              sellerId: this.seller ? this.seller.value : null
            }
          })
          .then(response => {
            this.sellers = response.body.sellers.map(item => item)
            this.manager = response.body.manager
            this.sellersNotBet = response.body.sellersNotBet.map(item => item)
            this.isLoading = false
          })
          .catch(() => {
            this.isLoading = false
            showToast('Erro ao carregador apostas')
          })
      },
      getSellers () {
        this.$http.get('/manager/sellers/pluck')
          .then(response => {
            this.sellersList = response.body.sellers.map(item => item)
            this.sellersList.unshift({label: 'Todos', value: 0})
          })
          .catch(() => showToast('Falha ao carregar vendedores'))
      },
    },
    computed: {
      totalBetValues () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.betsValue) + parseFloat(seller.entradaBolao))
      },
      totalWinnersQty () {
        const list = this.sellers
        return _.sumBy(list, seller => parseInt(seller.winnersQty))
      },
      totalWinnersValue () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.winnersValue))
      },
      totalCommission () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.commission) + parseFloat(seller.comissaoBolao))
      },
      totalExpenses () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.expenses))
      },
      subtotalValues () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.subtotal) + parseFloat(seller.bolaoSubtotal))
      },
      totalValues () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.total) + parseFloat(seller.totalBolao))
      },
      profitCommission () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.profitCommission))
      },
      comissaoLucroBolao () {
        const list = this.sellers
        return _.sumBy(list, seller => parseFloat(seller.comissaoLucroBolao))
      },
      totalPayments() {
        const list = this.sellers
        return _.reduce(list, (sum, seller) => {
          return sum + parseFloat(seller.payments)
        }, 0)
      },
      managerCommission () {
        if (parseFloat(this.totalValues) >= 0) {
          return parseFloat(this.totalValues) * (parseFloat(this.manager.manager_commission) / 100)
        }
        return 0
      },
      totalGeral () {
        return parseFloat(this.totalValues) - parseFloat(this.managerCommission) + this.totalPayments
      }
    },
    created() {
      this.getSellers()
      this.startDate = new Date().toISOString().slice(0, 10)
      this.finalDate = this.startDate
      this.getBets()
    }
  }
</script>
