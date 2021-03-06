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
            <td><b>Data</b></td>
            <td><b>Cancelar</b></td>
          </tr>
          </thead>
          <tbody>
          <tr
            v-for="aposta in apostas"
            :key="aposta.id"
            :class="{'warning': aposta.situacao === 'cancelado'}">
            <td>
              <a
                v-if="aposta.situacao !== 'cancelado'"
                :href="`/bolao/bilhete/${aposta.id}`"
                class="btn btn-info">
                <i class="fa fa-print"></i>
              </a>
            </td>
            <td>
              <span class="label label-default">{{ aposta.id }}</span>
            </td>
            <td>
              <strong>{{ aposta.cliente }}</strong>
            </td>
            <td>{{ aposta.valor | formatMoney }}</td>
            <td>{{ formatDate(aposta.created_at) }}</td>
            <td>
              <button
                v-if="aposta.status !== 'canceled' && canCancel(aposta.created_at)"
                @click.prevent="askToCancel(aposta)"
                class="btn btn-danger">
                Cancelar
              </button>
            </td>
          </tr>
          <tr v-if="apostas.length === 0">
            <td colspan="6" class="text-center">
              <strong>Nenhuma aposta encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <ul class="pager">
      <li :class="{'disabled': prevPage === null}" class="previous">
        <a @click.prevent="buscarApostas(prevPage)" href="#">Anterior</a>
      </li>
      <li :class="{'disabled': nextPage === null}" class="next">
        <a @click.prevent="buscarApostas(nextPage)" href="#">Próxima</a>
      </li>
    </ul>
  </div>
</template>

<script>
  import moment from 'moment'
  import modal from '../shared/Modal/BetResume.vue'

  export default {
    name: 'SegundaVia',
    components: {modal},
    data() {
      return {
        cancelTime: process.env.CANCEL_TIME || 0,
        nextPage: null,
        prevPage: null,
        apostas: []
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
      canCancel(date) {
        if (this.cancelTime <= 0) return false;

        return moment().isBefore(moment(date).add(this.cancelTime, 'm'))
      },
      cancelBet(aposta) {
        this.$http.put(`/bolao/cancelar/${aposta.id}`)
          .then(response => {
            aposta.situacao = response.body.aposta.situacao;
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
      buscarApostas(page = 1) {
        this.$http.post('/bolao/segundaVia', {page})
          .then(response => {
            this.apostas = response.body.apostas.data

            if (response.body.apostas.next_page_url) {
              let nextPage = response.body.apostas.next_page_url.split('=', 2)
              this.nextPage = nextPage[1]
            } else {
              this.nextPage = null
            }

            if (response.body.apostas.prev_page_url) {
              let prevPage = response.body.apostas.prev_page_url.split('=', 2)
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
      printBet(id) {
        try {
          return android.getTicket(id + "")
        } catch (err) {
          return this.$swal(
            'Cancelado!',
            `A aposta foi cancelada com sucesso. ${err.message}`,
            'success'
          )
        }
      }
    },
    created() {
      this.buscarApostas()
    }
  }
</script>
