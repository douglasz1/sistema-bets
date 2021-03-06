<template>
    <div id="modal-regular" class="modal summary" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><strong>Resumo da aposta</strong></h3>
                </div>
                <div class="modal-body" v-if="bet !== null">
                    <div>
                        <div class="block-title">
                            <h2><i class="fa fa-bars"></i> Resumo da aposta</h2>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Código: <span class="text-muted"> {{ bet.id }} </span></h4>
                                <h4>Operador: <span class="text-muted"> {{ bet.seller.name }} </span></h4>
                                <h4>Apostador: <span class="text-muted"> {{ bet.client_name }} </span></h4>
                                <h4>
                                    Situação:
                                    <span v-if="bet.status === 'win'" class="text-success">Vencedor</span>
                                    <span v-else-if="bet.status === 'lose'" class="text-danger">Perdedor</span>
                                    <span v-else-if="bet.status === 'canceled'" class="text-warning">Cancelado</span>
                                    <span v-else class="text-muted">Andamento</span>
                                </h4>
                            </div>
                            <div class="col-md-6">
                                <h4>Data: <span class="text-muted">{{ formatDate(bet.created_at) }}</span></h4>
                                <h4>Valor: <span class="text-muted ">{{ bet.bet_value | formatMoney }}</span></h4>
                                <h4>Prêmio: <span class="text-muted ">{{ bet.prize | formatMoney }}</span></h4>
                                <h4>Comissão: <span class="text-muted">{{ bet.commission | formatMoney }}</span></h4>

                                <a v-if="bet.status !== 'canceled'" @click.prevent="cancelBet(bet)"
                                   class="btn btn-danger btn-effect-ripple">
                                    <i class="fa fa-ban"></i>
                                    Cancelar aposta
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="block-with-table">
                        <div class="block-title">
                            <h2><i class="fa fa-bars"></i> Palpites da aposta</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-condensed table-hover">
                                <thead>
                                <tr>
                                  <td><b>Partida</b></td>
                                  <td><b>Liga</b></td>
                                  <td width="110"><b>Data</b></td>
                                  <td><b>Palpite</b></td>
                                  <td><b>Cota</b></td>
                                  <td><b>Situação</b></td>
                                  <td width="50"><b>1&deg; T</b></td>
                                  <td width="50"><b>2&deg; T</b></td>
                                  <td width="70"><b>R. Final</b></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="tip in bet.tips" :key="tip.id">
                                  <td>
                                    <strong>
                                      {{ `${tip.match.home_team} x ${tip.match.away_team}` }}
                                    </strong>
                                  </td>
                                  <td>
                                    <strong>{{ tip.match.sport.name }}:</strong>
                                    {{ tip.match.league.name }}
                                  </td>
                                  <td>{{ formatDate(tip.match.match_date) }}</td>
                                  <td> {{ tip.choice_name }}</td>
                                  <td> {{ tip.value | formatQuotation }}</td>
                                  <td>
                                    <span v-if="tip.status === 'win'" class="text-success">Vencedor</span>
                                    <span v-else-if="tip.status === 'lose'" class="text-danger">Perdedor</span>
                                    <span v-else-if="tip.status === 'canceled'" class="text-warning">Cancelado</span>
                                    <span v-else>Andamento</span>
                                  </td>
                                  <td>
                                    {{ tip.match.home_1st }}-{{ tip.match.away_1st }}
                                  </td>
                                  <td>
                                    {{ tip.match.home_2nd }}-{{ tip.match.away_2nd }}
                                  </td>
                                  <td>
                                    {{ tip.match.home_final }}-{{ tip.match.away_final }}
                                  </td>
                                </tr>
                                <tr v-if="bet.tips === null || bet.tips.length === 0">
                                  <td colspan="9">Nenhum palpite encontrado</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button
                    @click.prevent="hideModal"
                    type="button"
                    class="btn btn-effect-ripple btn-danger">
                    <i class="fa fa-remove"></i> Fechar
                  </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import moment from 'moment'

  export default {
    data() {
      return {
        bet: null
      }
    },
    methods: {
      formatDate(date) {
        return moment(date).format('DD/MM HH:mm')
      },
      getBet(betId) {
        this.$http.get(`/supervisor/bets/summary/${betId}`)
          .then(response => {
            this.bet = response.body.bet
            this.showModal()
          })
          .catch(() => this.$swal(
            'Erro ao recuperar dados!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde',
            'error'
          ))
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
          .finally(() => bet.editing = false)
      },
      cancelTip(tip) {
        this.$http.post(`/supervisor/tips/cancel/${tip.id}`)
          .then(() => {
            tip.status = 'canceled'
            this.$swal(
              'Cancelado!',
              'O palpite foi cancelado com sucesso.',
              'success'
            )
          })
          .catch(() => this.$swal(
            'Erro ao cancelar!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => tip.editing = false)
      },
      hideModal() {
        $('.summary').modal('hide')
      },
      showModal() {
        $('.summary').modal('show')
      }
    },
  }
</script>

<style scoped>
    @media screen and (max-width: 767px) {
        .close {
            font-size: 2em;
        }
    }

    .block-title {
        margin: 0 0 5px;
    }

    .block-with-table {
        padding: 20px 0 5px;
    }
</style>
