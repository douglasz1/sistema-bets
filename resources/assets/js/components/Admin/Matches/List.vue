<template>
    <div class="block full block-with-table">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Partidas cadastradas</h2>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4">
                <input type="text" class="form-control" v-model="matchName" placeholder="Filtrar por times">
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <select class="form-control" v-model="onlyActives" id="onlyActives">
                    <option value="actives">Ativos</option>
                    <option value="disabled">Inativos</option>
                    <option value="all">Todos</option>
                </select>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <v-select
                        v-model="sport"
                        :options="sports"
                        placeholder="Esportes">
                </v-select>
            </div>
            <br/>
            <br/>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <input v-model="startDate" type="date" name="start_date" id="start_date"
                           class="form-control">
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <input v-model="endDate" type="date" name="end_date" id="end_date" class="form-control">
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <button @click="getMatches" type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <td style="width: 60px;"><b>Data</b></td>
                    <td style="width: 70px;"><b>Esporte</b></td>
                    <td style="width: 170px;"><b>Liga</b></td>
                    <td><b>Partida</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="match in filteredMatches" :key="match.id">
                    <td>{{ match.human_date }}</td>
                    <td>{{ match.sport.name }}</td>
                    <td>{{ match.league.name }}</td>
                    <td>
                        {{ match.home_team }} <br>
                        {{ match.away_team }}
                    </td>
                    <td>
                        <a :href="match.edit_link" class="btn btn-default btn-effect-ripple">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a v-if="match.active" :href="match.change_status" class="btn btn-danger btn-effect-ripple">
                            <i class="fa fa-ban"></i> Desativar
                        </a>
                        <a v-else :href="match.change_status" class="btn btn-success btn-effect-ripple">
                            <i class="fa fa-check"></i> Ativar
                        </a>
                    </td>
                </tr>
                <tr v-if="filteredMatches.length === 0">
                    <td colspan="5" class="text-center">
                        <b>Nenhuma partida encontrada</b>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
  import vSelect from 'vue-select'

  export default {
    components: {vSelect},
    data () {
      return {
        startDate: null,
        endDate: null,
        onlyActives: 'actives',
        sport: null,
        matchName: '',
        matches: [],
        sports: []
      }
    },
    methods: {
      getMatches() {
        this.$http.get('/admin/matches/all', {
          params: {
            start_date: this.startDate,
            end_date: this.endDate,
            sport_id: this.sport ? this.sport.value : null
          }
        }).then(response => {
          response.json().then(res => this.matches = res.matches);
        }, error => {
          console.log(error);
        });
      },
      getSports() {
        this.$http.get('/admin/sports/all').then(response => {
          response.json()
          .then(res => {
            res.sports.unshift({value: 0, label: 'Todas os esportes'});
            this.sports = res.sports;
          });
        }, error => {
          console.log(error);
        });
      },
    },
    computed: {
      filteredMatches() {
        const filter = this.matchName;
        const list = _.orderBy(this.matches, 'match_date', 'asc');

        return _.filter(list, match => {
          if (this.onlyActives === 'actives') {
            return match.match_name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && match.active

          } else if (this.onlyActives === 'disabled') {
            return match.match_name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && !match.active
          }

          return match.match_name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      },
    },
    created() {
      this.getMatches()
      this.getSports()
      this.startDate = new Date().toISOString().slice(0, 10)
      this.endDate = this.startDate
    }
  }
</script>
