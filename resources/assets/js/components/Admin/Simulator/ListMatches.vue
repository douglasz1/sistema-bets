<template>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="leagues.length > 0">
    <div class="block full">
      <div class="block-title hide">
        <h2>
          <i class="fa fa-futbol-o"></i>
          {{ pageTitle }}
        </h2>
      </div>
      <div class="lista-partidas">
        <div v-for="league in campeonatosOrdenados" :key="league.id" class="lista-partidas--liga">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="widget">
                <div class="widget-content themed-background-primary clearfix">
                  <img :src="'/storage/flags/' + league.flag" class="pull-left flag-league">
                  <h4 class="widget-heading h4 text-light">
                    <strong>{{ `${league.country.name}: ${league.name}` }}</strong>
                  </h4>
                  <button
                    @click.prevent="mudarStatusLiga(league)"
                    class="pull-right btn btn-sm"
                    :class="{'btn-success': !league.active, 'btn-danger': league.active}"
                  >
                    <span v-if="!league.active">Ativar</span>
                    <span v-else>Desativar</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-for="match in league.matches" :key="match.id" class="row partida">
            <div class="col-md-6 partida-detalhes">
              <h4>{{ match.match_name }}</h4>
              <h5 class="text-muted">{{ match.human_date }}</h5>
            </div>
            <div class="col-md-6 text-right">
              <!-- soccer events -->
              <div class="btn-group2" role="group">
                <button
                  v-for="quotation in match.quotations"
                  style="width:50px;"
                  :key="quotation.id"
                  :disabled="quotation.value === 0"
                  :class="{'btn-selected': verifyChoice(quotation.id) }"
                  type="button"
                  class="btn btn-default"
                  @click="addQuotations(quotation, league)"
                >
                  <small v-if="quotation.choice_slug === '1'" style="display: block">Casa</small>
                  <small v-else-if="quotation.choice_slug === 'X'" style="display: block">Empate</small>
                  <small v-else-if="quotation.choice_slug === '2'" style="display: block">Fora</small>
                  {{ quotation.value | formatQuotation }}
                </button>
                <button
                  style="width:50px;"
                  :class="{'btn-selected': verifyMatchSelected(match.id) }"
                  type="button"
                  class="btn btn-default"
                  @click="loadMoreQuotations(match.id, match.quotations_url)"
                >
                  <small style="display: block">Outros</small>
                  + {{ match.quotations_qty }}
                </button>
                <button
                  type="button"
                  class="btn"
                  @click.prevent="mudarStatusPartida(match)"
                  :class="{'btn-success': !match.active, 'btn-danger': match.active}"
                >
                  <i v-if="match.active" class="fa fa-ban"></i>
                  <i v-else class="fa fa-check"></i>
                </button>
              </div>
              <!-- end of quotations loop -->
            </div>
            <div class="mais-cotacoes" :id="'match-' + match.id">
              <div></div>
            </div>
            <div class="col-xs-12 xs-all">
              <hr>
            </div>
          </div>
          <div v-if="leagues.length === 0">
            <h3>Nenhuma partida na liga para hoje</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    leagues: {
      required: true,
      type: Array
    },
    pageTitle: {
      required: true,
      type: String
    }
  },
  computed: {
    campeonatosOrdenados() {
      return _.orderBy(
        this.leagues,
        ["order", "country.name"],
        ["desc", "asc"]
      );
    }
  },
  methods: {
    mudarStatusLiga(liga) {
      return this.$parent.mudarStatusLiga(liga);
    },
    mudarStatusPartida(partida) {
      return this.$parent.mudarStatusPartida(partida);
    },
    verifyChoice(choice_id) {
      return this.$parent.$refs.betSimulate.verifyChoice(choice_id);
    },
    addQuotations(quotation, league) {
      quotation.sport_name = league.sport.name;
      quotation.league_name = `${league.country.name}: ${league.name}`;
      this.$parent.$refs.betSimulate.addQuotations(quotation);
    },
    loadMoreQuotations(matchId, quotationsUrl) {
      this.$parent.loadMoreQuotations(matchId, quotationsUrl);
    },
    verifyMatchSelected(matchId) {
      return this.$parent.$refs.betSimulate.verifyMatchSelected(matchId);
    }
  }
};
</script>

<style scoped>
.btn {
  padding: 6px;
}

.btn-group2 .btn {
  margin-right: 0 !important;
}

.btn-group2 .btn-danger,
.btn-group2 .btn-success {
  width: 50px;
  height: 50px;
  line-height: 50%;
  font-size: 1.8em;
  margin-left: 5px;
}

.h4 {
  display: inline-block;
}

span.btn-xs {
  font-size: 0.75em;
  padding: 0;
}

span.btn-xs:nth-child(1) {
  margin-right: 1px;
}

span.btn-xs:nth-child(2) {
  margin-right: 2px;
}

h5 {
  font-size: 14px;
}
</style>
