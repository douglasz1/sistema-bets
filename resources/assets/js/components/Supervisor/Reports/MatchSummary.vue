<template>
  <div id="modal-regular" class="modal summary modal-analitico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title" v-if="match">
            <strong>{{ `${match.home_team} x ${match.away_team}` }}</strong>
          </h3>
        </div>
        <div class="modal-body" v-if="match">
          <div class="block full bordered">
            <div class="block-title">
              <h2>Palpites da partida</h2>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter table-borderless table-hover table-striped">
                <thead>
                <tr>
                  <td><b>Palpite</b></td>
                  <td class="link" @click.prevent="changeOrder('quantity')">
                    <div>
                      <b>Qnt.</b>
                      <i
                        class="fa"
                        v-if="sortBy === 'quantity'"
                        :class="{
                        'fa-chevron-down': sortOrder === 'desc',
                        'fa-chevron-up': sortOrder === 'asc'
                      }"></i>
                    </div>
                  </td>
                  <td class="link" @click.prevent="changeOrder('total_value')">
                    <div>
                      <b>Entrada</b>
                      <i
                        class="fa"
                        v-if="sortBy === 'total_value'"
                        :class="{
                        'fa-chevron-down': sortOrder === 'desc',
                        'fa-chevron-up': sortOrder === 'asc'
                      }"></i>
                    </div>
                  </td>
                  <td class="link" @click.prevent="changeOrder('total_prize')">
                    <div>
                      <b>Premiação</b>
                      <i
                        class="fa"
                        v-if="sortBy === 'total_prize'"
                        :class="{
                        'fa-chevron-down': sortOrder === 'desc',
                        'fa-chevron-up': sortOrder === 'asc'
                      }"></i>
                    </div>
                  </td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="tip in sortedTips" :key="tip.id">
                  <td>
                    {{ tip.choice_name }}
                  </td>
                  <td>
                    {{ tip.quantity }}
                  </td>
                  <td>
                    {{ tip.total_value | formatMoney }}
                  </td>
                  <td>
                    <b>{{ tip.total_prize | formatMoney }}</b>
                  </td>
                </tr>
                <tr v-if="tips.length === 0">
                  <td colspan="4">Nenhum palpite encontrado</td>
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
            class="btn btn-danger">
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
    name: 'MatchSummary',
    data() {
      return {
        match: null,
        tips: [],
        sortBy: 'quantity',
        sortOrder: 'desc',
      }
    },
    methods: {
      changeOrder(sortBy) {
        if (this.sortBy === sortBy) {
          this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
          return
        }
        this.sortBy = sortBy
        this.sortOrder = 'desc'
      },
      formatDate(date) {
        return moment(date).format('DD/MM HH:mm')
      },
      getMatch(id) {
        this.$swal({
          title: 'Carregando...',
          onOpen: () => {
            this.$swal.showLoading()
          }
        })
        this.$http
          .get(`/supervisor/reports/analitico/summary/${id}`, {
            params: { status: this.$parent.status }
          })
          .then(response => {
            this.match = response.body.match
            this.tips = response.body.tips
            this.showModal()
          })
          .catch(() => this.$swal(
            'Erro ao recuperar dados!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde',
            'error'
          ))
          .finally(() => this.$swal.close())
      },
      hideModal() {
        $('.summary').modal('hide')
      },
      showModal() {
        $('.summary').modal('show')
      }
    },
    computed: {
      sortedTips() {
        const sortBy = this.sortBy
        const sortOrder = this.sortOrder
        const numericValues = ['total_value', 'quantity', 'total_prize']

        if (numericValues.indexOf(sortBy) < 0){
          return _.orderBy(this.tips, sortBy, sortOrder)
        }
        return _.orderBy(this.tips, tip => parseInt(tip[`${sortBy}`]), sortOrder)
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
  .link {
    cursor: pointer;
  }
  thead > tr div {
    display: flex;
    align-items: center;
  }
  td .fa-chevron-down,
  td .fa-chevron-up {
    font-size: .5em;
    margin-left: 5px;
  }
</style>
