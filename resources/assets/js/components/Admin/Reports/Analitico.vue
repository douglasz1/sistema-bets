<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Quantidade de apostas por evento</h2>
      </div>
      <div class="btn-group btn-group-full tres-botoes p-lr p-tb">
        <button :class="{'btn-primary': status === 'pending'}" @click.prevent="setStatus('pending')" class="btn btn-default">Andamento</button>
        <button :class="{'btn-primary': status === 'win'}" @click.prevent="setStatus('win')" class="btn btn-default">Premiadas</button>
        <button :class="{'btn-primary': status === 'all'}" @click.prevent="setStatus('all')" class="btn btn-default">Todos</button>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-b">
        <div class="form-group">
          <input v-model="startDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="finalDate" type="date" class="form-control">
        </div>
        <button
          @click.prevent="getMatches"
          class="btn btn-success m-t-sm">
          <i class="fa fa-filter"></i> Filtrar
        </button>
      </div>
      <div class="table-no-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td class="link" @click.prevent="changeOrder('created_at')">
              <div>
                <b>Hora</b>
                <i
                  v-if="sortBy === 'created_at'"
                  :class="{
                    'fa-chevron-down': sortOrder === 'desc',
                    'fa-chevron-up': sortOrder === 'asc'
                  }"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Partida</b></td>
            <td class="link" @click.prevent="changeOrder('tips_count')">
              <div>
                <b>Qnt.</b>
                <i
                  v-if="sortBy === 'tips_count'"
                  :class="{
                    'fa-chevron-down': sortOrder === 'desc',
                    'fa-chevron-up': sortOrder === 'asc'
                  }"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Opções</b></td>
          </tr>
          </thead>
          <tbody>
          <tr
            v-for="match in sortedMatches"
            :key="match.match_id">
            <td>
              {{ formatDate(match.match_date) }}
            </td>
            <td>
              {{ `${match.home_team} x ${match.away_team}` }}
            </td>
            <td>
              {{ match.tips_count }}
            </td>
            <td>
              <button @click.prevent="openMatch(match)" class="btn btn-default">
                <i class="fa fa-folder-open"></i>
              </button>
            </td>
          </tr>
          <tr v-if="sortedMatches.length === 0">
            <td colspan="4" class="text-center">
              <strong>Nenhuma partida encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <MatchSummary ref="matchSummary" />
  </div>
</template>

<script>
  import moment from 'moment'
  import MatchSummary from './MatchSummary'

  export default {
    name: 'Analitico',
    components: {MatchSummary},
    data() {
      return {
        startDate: null,
        finalDate: null,
        status: 'pending',
        matches: [],
        sortBy: 'tips_count',
        sortOrder: 'desc',
      }
    },
    methods: {
      setStatus(status) {
        this.status = status
        this.getMatches()
      },
      changeOrder(sortBy) {
        if (this.sortBy === sortBy) {
          this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
          return
        }
        this.sortBy = sortBy
        this.sortOrder = 'desc'
      },
      formatDate(date) {
        return moment(date).format('HH:mm')
      },
      getMatches() {
        this.$swal({
          title: 'Carregando...',
          onOpen: () => {
            this.$swal.showLoading()
          }
        })

        this.$http
          .get('/admin/reports/analitico/report', {
            params: {
              status: this.status,
              startDate: this.startDate,
              finalDate: this.finalDate
            }
          })
          .then(response => {
            this.matches = response.body.matches
          })
          .catch(() => this.$swal(
            'Erro!',
            'Ocorreu algum erro ao listar as partidas, por favor, tente novamente',
            'error'
          ))
          .finally(() => this.$swal.close())
      },
      openMatch(match) {
        this.$refs.matchSummary.getMatch(match.id)
      }
    },
    computed: {
      sortedMatches() {
        const sortBy = this.sortBy
        const sortOrder = this.sortOrder
        const matches = this.matches

        if (sortBy === 'tips_count') {
          return _.orderBy(matches, match => parseInt(match[`${sortBy}`]), sortOrder)
        }

        return _.orderBy(matches, match => new Date(match[`${sortBy}`]).getTime(), sortOrder)
      }
    },
    created() {
      this.getMatches()
    },
    mounted() {
      this.startDate = new Date().toISOString().slice(0, 10)
      this.finalDate = this.startDate
    }
  }
</script>

<style scoped>
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
