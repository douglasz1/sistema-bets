<template>
    <div class="block full block-with-table">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Partidas e cotações</h2>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <input class="form-control" v-model="matchName" placeholder="Filtrar por times">
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <v-select
                        v-model="league"
                        :options="leagues"
                        placeholder="Ligas">
                </v-select>
            </div>
            <br/>
            <br/>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <input v-model="startDate" type="date" class="form-control">
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <input v-model="endDate" type="date" class="form-control">
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                    <button @click.prevent="getMatches" class="btn btn-primary">
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
                    <td style="width: 170px;"><b>Liga</b></td>
                    <td><b>Partida</b></td>
                    <td class="text-center"><b>CS</b></td>
                    <td class="text-center"><b>EP</b></td>
                    <td class="text-center"><b>FR</b></td>
                    <td class="text-center"><b>C.E.</b></td>
                    <td class="text-center"><b>F.E.</b></td>
                    <td class="text-center"><b>C.F.</b></td>
                    <td class="text-center"><b>A.M</b></td>
                    <td class="text-center"><b>A.U.M.</b></td>
                    <td colspan="2"><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(match, index) in filteredMatches"
                    :class="{
                        'editing': match.id === editedMatch.id,
                        'saved': match.saved
                    }">
                    <td>{{ match.human_date }}</td>
                    <td>{{ match.league.name }}</td>
                    <td>
                        {{ match.home_team }} <br>
                        {{ match.away_team }}
                    </td>
                    <!-- full_time_result -->
                    <td>
                        <span>
                            {{ match.quotations.home.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.home.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.home.value) | formatQuotation }}
                        </strong>
                    </td>
                    <td>
                        <span>
                            {{ match.quotations.draw.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.draw.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.draw.value) | formatQuotation }}
                        </strong>
                    </td>
                    <td>
                        <span>
                            {{ match.quotations.away.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.away.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.away.value) | formatQuotation }}
                        </strong>
                    </td>
                    <!-- double_chance -->
                    <td>
                        <span>
                            {{ match.quotations.home_draw.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.home_draw.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.home_draw.value) | formatQuotation }}
                        </strong>
                    </td>
                    <td>
                        <span>
                            {{ match.quotations.away_draw.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.away_draw.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.away_draw.value) | formatQuotation }}
                        </strong>
                    </td>
                    <td>
                        <span>
                            {{ match.quotations.home_away.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.home_away.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.home_away.value) | formatQuotation }}
                        </strong>
                    </td>
                    <!-- both_teams_to_score -->
                    <td>
                        <span>
                            {{ match.quotations.both_yes.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.both_yes.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.both_yes.value) | formatQuotation }}
                        </strong>
                    </td>
                    <td>
                        <span>
                            {{ match.quotations.both_no.value | formatQuotation }}
                        </span>
                        <input class="form-control" type="number" step="0.01" v-model="editedMatch.quotations.both_no.value">
                        <br>
                        <strong class="text-muted">
                            {{ calculateModifier(match.quotations.both_no.value) | formatQuotation }}
                        </strong>
                    </td>
                    <!-- end both_teams_to_score -->
                    <td>
                        <button @click.prevent="editMatch(match)"
                                class="btn btn-default btn-effect-ripple btn-edit">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button @click.prevent="saveMatch(match)"
                                class="btn btn-success btn-effect-ripple btn-save">
                            <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                            <i v-else class="fa fa-save"></i>
                        </button>
                    </td>
                    <td>
                        <button @click.prevent="cancelEdit"
                                class="btn btn-danger btn-effect-ripple btn-cancel">
                            <i class="fa fa-remove"></i>
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredMatches.length === 0">
                    <td colspan="12" class="text-center">
                        <b>Nenhuma partida encontrada</b>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
  import {showToast} from '../../../functions/showToast'
  import vSelect from 'vue-select'

  export default {
    components: {vSelect},
    data () {
      return {
        loading: false,
        startDate: null,
        endDate: null,
        league: null,
        matchName: '',
        matches: [],
        leagues: [],
        quotationModifier: 0,
        beforeEdited: {
          id: 0,
          quotations: {
            home: {value: 0},
            draw: {value: 0},
            away: {value: 0},
            home_draw: {value: 0},
            away_draw: {value: 0},
            home_away: {value: 0},
            both_yes: {value: 0},
            both_no: {value: 0},
          }
        },
        editedMatch: {
          id: 0,
          quotations: {
            home: {value: 0},
            draw: {value: 0},
            away: {value: 0},
            home_draw: {value: 0},
            away_draw: {value: 0},
            home_away: {value: 0},
            both_yes: {value: 0},
            both_no: {value: 0},
          }
        },
      }
    },
    methods: {
      getMatches () {
        this.$http
          .get('/technical/quotations/matches', {
            params: {
              start_date: this.startDate,
              end_date: this.endDate,
              league_id: this.league ? this.league.value : null
            }
          })
          .then(response => {
            this.matches = response.body.matches
            this.quotationModifier = response.body.quotationModifier
          })
          .catch(() => showToast('Erro ao listar partidas'))
      },
      getLeagues () {
        this.$http
          .get('/technical/leagues/pluck-by-matches', {
            params: {
              start_date: this.startDate,
              end_date: this.endDate,
              league_id: this.league ? this.league.value : null
            }
          })
          .then(response => {
            this.leagues = response.body.leagues.map(item => item)
            this.leagues.unshift({value: 0, label: 'Todas as ligas'})
          })
          .catch(() => showToast('Erro ao listar ligas'))
      },
      editMatch (match) {
        return this.editedMatch = JSON.parse(JSON.stringify(match));
      },
      saveMatch (match) {
        if (!this.editedMatch.id === 0) {
          return;
        }

        this.loading = true;
        match.saved = false;

        this.$http.post('/technical/quotations/update', {match: this.editedMatch})
          .then(response => {
            response.json().then(res => {
              this.loading = false;
              this.cancelEdit();
              match.quotations = res.match.quotations;
              match.saved = true;
            });
          })
          .catch(response => {
            this.loading = false;
            console.error(response.data);
          });
      },
      cancelEdit () {
        this.editedMatch = this.beforeEdited;
      },
      calculateModifier (quotationValue) {
        let value = parseFloat(quotationValue) + parseFloat(quotationValue) * parseFloat(this.quotationModifier)

        if (value < 1.01 && value > 0) {
          return 1.01
        } else if (value > 100) {
          return 100
        }

        return value
      }
    },
    computed: {
      filteredMatches: function () {
        const filterMatch = this.matchName;
        const list = _.orderBy(this.matches, 'match_date', 'asc');


        if (filterMatch === '') {
          return list;
        }

        return _.filter(list, match => {
          return (match.home_team.toLowerCase().indexOf(filterMatch.toLowerCase()) >= 0) || (match.away_team.toLowerCase().indexOf(filterMatch.toLowerCase()) >= 0);
        });
      },
    },
    created() {
      this.getMatches();
      this.getLeagues();
      this.startDate = new Date().toISOString().slice(0, 10);
      this.endDate = this.startDate;
    }
  }
</script>

<style scoped>
    .form-group > label {
        display: none;
    }

    @keyframes changebg {
        0%, 100% {
            background-color: transparent;
        }

        50% {
            background-color: #1ab51a;
        }
    }

    tr.saved > td,
    tr.saved:hover > td {
        -webkit-animation: changebg .3s;
        -o-animation: changebg .3s;
        animation: changebg .3s;
    }

    tr > td > .btn-save,
    tr > td > .btn-cancel,
    tr > td input,
    tr.editing > td > .btn-edit,
    tr.editing > td > span {
        display: none;
    }

    tr.editing > td > .btn-save,
    tr.editing > td > .btn-cancel,
    tr.editing > td input {
        display: inline-block;
    }

    td input {
        width: 60px;
        padding: 6px 3px;
    }
</style>
