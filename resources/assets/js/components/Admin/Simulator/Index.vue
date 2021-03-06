<template>
  <div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8">
      <SportsSelect
        :sports="sports"
        :logged="true"
        :aoVivo.sync="aoVivoDisponivel"
        @selectSport="selectSport"
      />
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="row">
            <div class="col-md-8 col-sm-6">
              <div class="match-buttons">
                <button
                  @click.prevent="getLeagues('today')"
                  class="btn btn-primary"
                >Hoje</button>
                <button
                  @click.prevent="getLeagues('tomorrow')"
                  class="btn btn-primary"
                >Amanhã</button>
                <button
                  @click.prevent="getLeagues('after')"
                  class="btn btn-primary"
                >{{ afterTomorrowDate() }}</button>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 match-search">
              <form @submit.prevent="searchLeagues" action="javascript:" class="input-group">
                <input
                  v-model="matchFilter"
                  type="text"
                  class="form-control"
                  placeholder="Buscar partida"
                  minlength="3"
                >
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-primary">
                    <i v-if="searching" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-search"></i>
                  </button>
                </span>
              </form>
            </div>
          </div>
        </div>
        <div v-show="loading" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center loading">
          <i class="fa fa-spinner fa-2x fa-spin text-info"></i>
        </div>
        <!-- listagem de partidas pesquisada ou da liga selecionada -->
        <ListMatches
          ref="listSelectedMatches"
          :leagues="selectedLeague"
          :pageTitle="'Liga selecionada'"
        />

        <!-- listagem de partidas de todas as ligas -->
        <ListMatches ref="listMatches" :leagues="leagues" :pageTitle="'Jogos disponíveis'"/>
      </div>
    </div>
    <BetSimulate ref="betSimulate" :maxPrize="maxPrize" :maxPrizeMultiplier="maxPrizeMultiplier"/>
  </div>
</template>

<script>
import BetSimulate from "../../Simulator/BetSimulate";
import ListMatches from "./ListMatches";
import SportsSelect from "../../Simulator/SportsSelect";
import { showToast } from "../../../functions/showToast";

export default {
  components: { BetSimulate, ListMatches, SportsSelect },
  data() {
    return {
      aoVivoDisponivel: false,
      sport: null,
      sports: [],
      leagues: [],
      leaguesHaveMatches: [],
      selectedLeague: [],
      loading: true,
      maxPrize: 0,
      maxPrizeMultiplier: 0,
      matchFilter: "",
      searching: false
    };
  },
  methods: {
    getConfig() {
      this.$http.post("/configs/simulator").then(
        response => {
          response.json().then(res => {
            this.maxPrize = res.maxPrize;
            this.maxPrizeMultiplier = res.maxPrizeMultiplier;
            this.aoVivoDisponivel = res.ao_vivo;
            this.sports = res.sports;
          });
        },
        err => {
          console.log(err);
        }
      );
    },
    getLeagues(date) {
      this.loading = true;
      this.$http
        .get("/leagues/matches", {
          params: {
            sport_id: this.sport,
            date: date === undefined || date === null ? "all" : date
          }
        })
        .then(
          response => {
            response.json().then(res => {
              this.leagues = res.leagues.map(liga => {
                liga.active = true;
                return liga;
              });
              this.loading = false;
            });
          },
          err => {
            console.log(err);
            this.loading = false;
          }
        );
    },
    getSelectedLeague(leagueId) {
      this.loading = leagueId !== "";
      this.selectedLeague = [];
      window.scrollTo(0, 0);

      if (leagueId !== "") {
        this.$http
          .get("/leagues/matches", { params: { league_id: leagueId } })
          .then(
            response => {
              response.json().then(res => {
                this.selectedLeague = res.leagues;
                this.loading = false;
              });
            },
            error => {
              console.log(error);
              this.loading = false;
            }
          );
      }
    },
    searchLeagues() {
      if (this.matchFilter.length >= 3) {
        this.searching = true;
        this.$http.post("/leagues/search", { q: this.matchFilter }).then(
          response => {
            response.json().then(res => {
              this.selectedLeague = res.leagues;
              this.searching = false;
            });
          },
          err => {
            showToast(err.data.message);
            this.searching = false;
          }
        );
        return true;
      }
      return showToast("Digite pelo menos 3 letras");
    },
    mudarStatusLiga(liga) {
      this.$http.get("/admin/leagues/status/" + liga.id).then(
        response => {
          liga.active = !liga.active;
        },
        error => {
          console.log(error);
        }
      );
    },
    mudarStatusPartida(partida) {
      this.$http.get("/admin/matches/status/" + partida.id).then(
        response => {
          partida.active = !partida.active;
        },
        error => {
          console.log(error);
        }
      );
    },
    loadMoreQuotations(matchId, quotationsUrl) {
      let Child = this.$options.components["load-quotations"];
      let child = new Child({
        el: this.$el.querySelector("#match-" + matchId + " > div"), // define the el of component
        parent: this, // define parent of component
        data() {
          return {
            matchId: matchId,
            quotationsUrl: quotationsUrl
          };
        }
      });
    },
    afterTomorrowDate() {
      let dat = new Date();
      dat.setDate(dat.getDate() + 2);
      let mm = dat.getMonth() + 1;
      let dd = dat.getDate();

      return (dd > 9 ? "" : "0") + dd + "/" + (mm > 9 ? "" : "0") + mm;
    },
    selectSport(sport) {
      this.sport = sport.id;
      this.getLeagues("today");
    }
  },
  created() {
    this.getConfig();
    this.getLeagues("today");
  }
};
</script>

<style scoped>
.match-buttons {
  /*margin-top: 5px;*/
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.match-buttons .btn {
  font-size: 1em;
  width: 32%;
}

.loading {
  margin: 5px 0 15px 0;
}

.match-search {
  margin-bottom: 15px;
}

.input-group input {
  border-color: #01567d;
}
</style>
