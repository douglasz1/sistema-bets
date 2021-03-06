<template>
    <div id="modal-regular" class="modal summary modal-analitico" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><strong>Resumo da aposta</strong></h3>
                </div>
                <div class="modal-body" v-if="aposta !== null">
                    <div class="block full bordered">
                        <div class="block-title">
                            <div class="block-options pull-right">
                                <button
                                  @click.prevent="aposta.editing = !aposta.editing"
                                  class="btn btn-success">
                                  <i class="fa fa-edit"></i> Editar
                                </button>
                              <a v-if="aposta.situacao !== 'cancelado'" @click.prevent="perguntarParaCancelar(aposta)"
                                 class="btn btn-danger">
                                <i class="fa fa-ban"></i>
                                Cancelar aposta
                              </a>
                            </div>
                            <h2>Resumo da aposta</h2>
                        </div>
                        <div class="row text-light p-lr p-tb">
                            <div class="col-md-6 form-inline" :class="{'editing': aposta.editing }">
                                <h4>Código: <span class="text-muted"> {{ aposta.id }} </span></h4>
                                <h4>Operador: <span class="text-muted"> {{ aposta.vendedor.name }} </span></h4>
                                <h4>
                                    Cliente:
                                    <span class="text-muted hide-on-edit"> {{ aposta.cliente }} </span>
                                    <input type="text" v-model="aposta.cliente" class="form-control">
                                </h4>
                                <h4>
                                    Situação:
                                    <span class="hide-on-edit">
                                      <span v-if="aposta.situacao === 'cancelado'" class="text-warning">Cancelado</span>
                                      <span v-else class="text-muted">Andamento</span>
                                    </span>
                                    <select v-model="aposta.situacao" class="form-control">
                                      <option value="cancelado">Cancelado</option>
                                      <option value="pendente">Andamento</option>
                                    </select>
                                </h4>
                            </div>
                            <div class="col-md-6 form-inline" :class="{'editing': aposta.editing }">
                                <h4>Data:
                                    <span class="text-muted">{{ formatDate(aposta.created_at) }}</span>
                                </h4>
                                <h4>Valor:
                                    <span class="text-muted hide-on-edit">{{ aposta.valor | formatMoney }}</span>
                                    <input type="text" v-model="aposta.valor" class="form-control">
                                </h4>
                                <h4>Comissão:
                                    <span class="text-muted hide-on-edit">{{ aposta.comissao | formatMoney }}</span>
                                    <input type="text" v-model="aposta.comissao" class="form-control">
                                </h4>

                                <button @click.prevent="saveBet(aposta)" class="btn btn-success btn-effect-ripple">
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
                              <td><b>Palpites</b></td>
                              <td><b>R. Final</b></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(palpite, index) in aposta.palpites" :key="palpite.id">
                              <td>
                                <strong>
                                  {{ aposta.bolao.partidas[index].time_casa }}
                                  &times;
                                  {{ aposta.bolao.partidas[index].time_fora }}
                                </strong>
                              </td>
                              <td>
                                <div v-if="aposta.bolao.tipo_bolao === 'simples'">
                                  <span v-if="palpite.palpite_casa > palpite.palpite_fora">Casa</span>
                                  <span v-else-if="palpite.palpite_casa < palpite.palpite_fora">Fora</span>
                                  <span v-else>Empate</span>
                                </div>
                                <div v-else>
                                  <strong>
                                    {{ palpite.palpite_casa }}
                                    -
                                    {{ palpite.palpite_fora }}
                                  </strong>
                                </div>
                              </td>
                              <td>
                                <strong>
                                  {{ aposta.bolao.partidas[index].placar_casa }}
                                  -
                                  {{ aposta.bolao.partidas[index].placar_fora }}
                                </strong>
                              </td>
                            </tr>
                            <tr v-if="aposta.palpites === null || aposta.palpites.length === 0">
                              <td colspan="3">Nenhum palpite encontrado</td>
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
        aposta: null
      }
    },
    methods: {
      formatDate(date, includeSeconds = true) {
        if (includeSeconds) {
          return moment(date).format('DD/MM HH:mm:ss')
        }
        return moment(date).format('DD/MM HH:mm')
      },
      getBet(apostaId) {
        this.$http.get(`/api/bolao/bilhete/${apostaId}`)
          .then(response => {
            response.body.aposta.editing = false
            this.aposta = response.body.aposta
            this.showModal()
          })
          .catch(() => this.$swal(
            'Erro ao recuperar dados!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde',
            'error'
          ))
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
          if (result) this.cancelarAposta(aposta)
        }).catch(() => false)
      },
      cancelarAposta(aposta) {
        this.$http.put(`/admin/bolao/acompanhamento/cancelar/${aposta.id}`)
          .then(() => {
            aposta.situacao = 'cancelado'
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
      saveBet(aposta) {
        this.$http
          .post(`/admin/apostas/update/${aposta.id}`, {
            cliente: aposta.cliente,
            situacao: aposta.situacao,
            comissao: aposta.comissao,
            valor: aposta.valor
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
          .finally(() => aposta.editing = false)
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

    .palpite-status form,
    .editing .hide-on-edit,
    .form-inline:not(.editing) input,
    .form-inline:not(.editing) select,
    .form-inline:not(.editing) button.btn-success {
        display: none;
    }

    .editing .btn-success,
    .palpite-status.editing form,
    .palpite-status.editing select,
    .palpite-status.editing button.btn-success {
        display: inline-block;
    }

    .palpite-status.editing {
        width: 180px;
    }

    .table tbody > tr > td:last-child {
        min-width: 86px;
    }
</style>
