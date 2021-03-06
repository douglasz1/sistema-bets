<template>
    <div>
        <div class="block full bordered">
            <div class="table-responsive">
                <table class="table table-vcenter table-condensed table-striped table-hover table-borderless">
                    <thead>
                    <tr>
                        <td><b>Bilhete</b></td>
                        <td><b>Código</b></td>
                        <td><b>Nome</b></td>
                        <td><b>Valor</b></td>
                        <td><b>Prêmio</b></td>
                        <td><b>Data</b></td>
                        <td><b>Cancelar</b></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                      v-for="bet in bets"
                      :key="bet.id"
                      :class="{'warning': bet.status === 'canceled'}">
                        <td>
                            <a
                              v-if="bet.status !== 'canceled'"
                              @click.prevent="openBet(bet)"
                              class="btn btn-info">
                                <i class="fa fa-print"></i>
                            </a>
                        </td>
                        <td>
                          <span class="label label-default">{{ bet.id }}</span>
                        </td>
                        <td>
                          <strong>{{ bet.client_name }}</strong>
                        </td>
                        <td>{{ bet.bet_value | formatMoney }}</td>
                        <td class="text-success">{{ bet.prize | formatMoney }}</td>
                        <td>{{ formatDate(bet.created_at) }}</td>
                        <td>
                          <button
                            v-if="bet.status !== 'canceled' && bet.can_cancel"
                            @click.prevent="askToCancel(bet)"
                            class="btn btn-danger">
                            Cancelar
                          </button>
                        </td>
                    </tr>
                    <tr v-if="bets.length === 0">
                        <td colspan="7" class="text-center">
                            <strong>Nenhuma aposta encontrada</strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <ul class="pager">
            <li :class="{'disabled': prevPage === null}" class="previous">
                <a @click.prevent="getBets(prevPage)" href="#">Anterior</a>
            </li>
            <li :class="{'disabled': nextPage === null}" class="next">
                <a @click.prevent="getBets(nextPage)" href="#">Próxima</a>
            </li>
        </ul>
        <modal ref="modal" title="Resumo da aposta" :bet.sync="bet" :canPrint="true"></modal>
    </div>
</template>

<script>
  import moment from 'moment'
  import modal from '../shared/Modal/BetResume.vue'
  import { showToast } from '../../functions/showToast'

  export default {
    components: {modal},
    data () {
      return {
        nextPage: null,
        prevPage: null,
        bet: null,
        bets: []
      }
    },
    methods: {
      formatDate(date) {
        return moment(date).format('DD/MM HH:mm')
      },
      askToCancel(bet) {
        this.$swal({
          title: 'Cancelar simulação',
          html: `Deseja cancelar a simulação <b>${bet.id}</b>?`,
          type: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '<i class="glyphicon glyphicon-chevron-left"></i> Voltar',
          confirmButtonText: '<i class="glyphicon glyphicon-ok"></i> Sim, cancelar'
        }).then(result => {
          if (result) this.cancelBet(bet)
        }).catch(() => false)
      },
      cancelBet(bet) {
        this.$http.put(`/bets/cancel/${bet.id}`)
          .then(response => {
            bet.status = response.body.bet.status;
            this.$swal(
              'Cancelado!',
              'A aposta foi cancelada com sucesso.',
              'success'
            )
          })
          .catch(err => this.$swal(
            'Erro ao cancelar!',
            `${err.body.result}`,
            'error'
          ))
      },
      getBets (page = 1) {
        this.$http.post('/bets/reprints', {page})
          .then(response => {
            this.bets = response.body.bets.data

            if (response.body.bets.next_page_url) {
              let nextPage = response.body.bets.next_page_url.split('=', 2)
              this.nextPage = nextPage[1]
            } else {
              this.nextPage = null
            }

            if (response.body.bets.prev_page_url) {
              let prevPage = response.body.bets.prev_page_url.split('=', 2)
              this.prevPage = prevPage[1]
            } else {
              this.prevPage = null
            }
          })
          .catch(() => this.$swal(
            'Erro ao cancelar!',
            'Erro ao carregar apostas',
            'error'))
      },
      openBet (bet) {
        this.bet = null
        this.$http.get('/api/bet/' + bet.id)
          .then(response => {
            this.bet = response.body.bet
            this.$refs.modal.showModal()
          })
          .catch(() => this.$swal(
            'Erro ao cancelar!',
            'Erro ao abrir aposta',
            'error'))
      },
      printBet (id) {
        try {
          return android.getTicket(id + "")
        } catch (err) {
          return showToast(err.message)
        }
      }
    },
    created () {
      this.getBets()
    }
  }
</script>
