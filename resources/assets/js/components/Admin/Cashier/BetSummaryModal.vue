<template>
    <div id="modal-regular" class="modal summary modal-analitico" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><strong>Resumo da aposta</strong></h3>
                </div>
                <div class="modal-body" v-if="bet !== null">
                    <div class="block full bordered">
                        <div class="block-title">
                            <div class="block-options pull-right">
                                <button
                                  @click.prevent="bet.editing = !bet.editing"
                                  class="btn btn-success">
                                  <i class="fa fa-edit"></i> Editar
                                </button>
                              <a v-if="bet.status !== 'canceled'" @click.prevent="cancelBet(bet)"
                                 class="btn btn-danger">
                                <i class="fa fa-ban"></i>
                                Cancelar aposta
                              </a>
                            </div>
                            <h2>Resumo da aposta</h2>
                        </div>
                        <div class="row text-light p-lr p-tb">
                            <div class="col-md-6 form-inline" :class="{'editing': bet.editing }">
                                <h4>Código: <span class="text-muted"> {{ bet.id }} </span></h4>
                                <h4>Operador: <span class="text-muted"> {{ bet.seller.name }} </span></h4>
                                <h4>
                                    Apostador:
                                    <span class="text-muted hide-on-edit"> {{ bet.client_name }} </span>
                                    <input type="text" v-model="bet.client_name" class="form-control">
                                </h4>
                                <h4>
                                    Situação:
                                    <span class="hide-on-edit">
                                        <span v-if="bet.status === 'win'" class="text-success">Vencedor</span>
                                        <span v-else-if="bet.status === 'lose'" class="text-danger">Perdedor</span>
                                        <span v-else-if="bet.status === 'canceled'"
                                              class="text-warning">Cancelado</span>
                                        <span v-else class="text-muted">Andamento</span>
                                    </span>
                                    <select v-model="bet.status" class="form-control">
                                        <option value="win">Vencedor</option>
                                        <option value="lose">Perdedor</option>
                                        <option value="canceled">Cancelado</option>
                                        <option value="pending">Andamento</option>
                                    </select>
                                </h4>
                            </div>
                            <div class="col-md-6 form-inline" :class="{'editing': bet.editing }">
                                <h4>Data:
                                    <span class="text-muted">{{ formatDate(bet.created_at) }}</span>
                                </h4>
                                <h4>Valor:
                                    <span class="text-muted hide-on-edit">{{ bet.bet_value | formatMoney }}</span>
                                    <input type="text" v-model="bet.bet_value" class="form-control">
                                </h4>
                                <h4>Prêmio:
                                    <span class="text-muted hide-on-edit">{{ bet.prize | formatMoney }}</span>
                                    <input type="text" v-model="bet.prize" class="form-control">
                                </h4>
                                <h4>Comissão:
                                    <span class="text-muted hide-on-edit">{{ bet.commission | formatMoney }}</span>
                                    <input type="text" v-model="bet.commission" class="form-control">
                                </h4>

                                <button @click.prevent="saveBet(bet)" class="btn btn-success btn-effect-ripple">
                                    <i class="fa fa-save"></i> Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block full bordered m-t">
                        <div class="block-title">
                            <h2>Palpites da aposta</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
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
                                  <td><b>Opções</b></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="tip in bet.tips" :key="tip.id">
                                  <td>
                                    <strong>
                                      {{ tip.match.home_team }}
                                      <br>
                                      {{ tip.match.away_team }}
                                    </strong>
                                  </td>
                                  <td>
                                    <strong>{{ tip.match.sport.name }}:</strong>
                                    <br>
                                    {{ tip.match.league.name }}
                                  </td>
                                  <td>
                                    {{ formatDate(tip.match.match_date, false) }}
                                  </td>
                                  <td>
                                    <b>{{ tip.bet_name }}</b>
                                    <br>
                                    {{ tip.choice_name }}
                                  </td>
                                  <td>{{ tip.value | formatQuotation }}</td>
                                  <td class="tip-status" :class="{'editing': tip.editing }">
                                    <span class="hide-on-edit">
                                      <span v-if="tip.status === 'win'" class="text-success">Vencedor</span>
                                      <span v-else-if="tip.status === 'lose'" class="text-danger">Perdedor</span>
                                      <span v-else-if="tip.status === 'canceled'" class="text-warning">Cancelado</span>
                                      <span v-else>Andamento</span>
                                    </span>
                                    <form @submit.prevent="saveTip(tip)" class="form-inline">
                                      <select v-model="tip.status" class="form-control">
                                        <option value="win">Vencedor</option>
                                        <option value="lose">Perdedor</option>
                                        <option value="canceled">Cancelado</option>
                                        <option value="pending">Andamento</option>
                                      </select>
                                      <button type="submit" class="btn-effect-ripple btn-success btn btn-sm">
                                        <i class="fa fa-save"></i>
                                      </button>
                                    </form>
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
                                  <td>
                                    <button
                                      class="btn btn-sm btn-default btn-effect-ripple"
                                      @click.prevent="tip.editing = !tip.editing">
                                      <i class="fa fa-edit"></i>
                                    </button>
                                    <button
                                      v-if="tip.status !== 'canceled'"
                                      @click.prevent="cancelTip(tip)"
                                      class="btn btn-danger btn-sm btn-effect-ripple">
                                      <i class="fa fa-ban"></i>
                                    </button>
                                  </td>
                                </tr>
                                <tr v-if="bet.tips === null || bet.tips.length === 0">
                                  <td colspan="10">Nenhum palpite encontrado</td>
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
      formatDate(date, includeSeconds = true) {
        if (includeSeconds) {
          return moment(date).format('DD/MM HH:mm:ss')
        }
        return moment(date).format('DD/MM HH:mm')
      },
      getBet(betId) {
        this.$http.get(`/admin/bets/summary/${betId}`)
          .then(response => {
            response.body.bet.editing = false
            response.body.bet.tips.map(item => {
              item.editing = false
              return item
            })
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
          .finally(() => bet.editing = false)
      },
      saveBet(bet) {
        this.$http
          .post(`/admin/bets/update/${bet.id}`, {
            client_name: bet.client_name,
            status: bet.status,
            prize: bet.prize,
            commission: bet.commission,
            bet_value: bet.bet_value
          })
          .then(() => {
            this.$swal(
              'Dados salvos!',
              'A alteração foi salva com sucesso.',
              'success'
            )
          })
          .catch(() => this.$swal(
            'Erro ao salvar dados!',
            'Ocorreu algum erro ao salvar, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => bet.editing = false)
      },
      cancelTip(tip) {
        this.$http.post(`/admin/tips/cancel/${tip.id}`)
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
      saveTip(tip) {
        this.$http.post(`/admin/tips/update/${tip.id}`, {status: tip.status})
          .then(() => {
            tip.editing = false
            this.$swal(
              'Dados salvos!',
              'A alteração foi salva com sucesso.',
              'success'
            )
          })
          .catch(() => this.$swal(
            'Erro ao salvar dados!',
            'Ocorreu algum erro ao salvar, por favor, tente novamente mais tarde',
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

    .tip-status form,
    .editing .hide-on-edit,
    .form-inline:not(.editing) input,
    .form-inline:not(.editing) select,
    .form-inline:not(.editing) button.btn-success {
        display: none;
    }

    .editing .btn-success,
    .tip-status.editing form,
    .tip-status.editing select,
    .tip-status.editing button.btn-success {
        display: inline-block;
    }

    .tip-status.editing {
        width: 180px;
    }

    .table tbody > tr > td:last-child {
        min-width: 86px;
    }
</style>
