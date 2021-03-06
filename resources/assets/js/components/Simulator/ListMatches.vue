<template>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="leagues.length > 0">
    <div class="block full">
      <div class="block-title hide">
        <h2><i class="fa fa-futbol-o"></i> {{ pageTitle }} </h2>
      </div>
      <div class="lista-partidas">
        <div
          v-for="league in campeonatosOrdenados"
          :key="league.id"
          class="lista-partidas--liga">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="widget">
                <div class="widget-content themed-background-primary clearfix">
                  <img :src="'/storage/flags/' + league.flag" class="pull-left flag-league">
                  <h4 class="widget-heading h4 text-light">
                    <strong>{{ `${league.country.name}: ${league.name}` }}</strong>
                  </h4>
                </div>
              </div>
            </div>
          </div>
          <div
            v-for="match in league.matches"
            :key="match.id"
            class="row partida">
            <div class="col-md-6 col-sm-4 col-xs-12 partida-detalhes">
              <h4>{{ match.match_name }}</h4>
              <h5 class="text-muted">{{ match.human_date }}</h5>
            </div>
            <div class="col-md-6 col-sm-8 col-xs-12 text-right">
              <!-- soccer events -->
              <div class="btn-group2" role="group">
                <button
                  v-for="quotation in match.quotations"
                  style="width:60px;"
                  :key="quotation.id"
                  :disabled="quotation.value === 0"
                  :class="{'btn-selected': verifyChoice(quotation.id) }"
                  type="button"
                  class="btn btn-default"
                  @click="addQuotations(quotation, league)">
                  <small v-if="quotation.choice_slug === '1'" style="display: block">Casa</small>
                  <small v-else-if="quotation.choice_slug === 'X'" style="display: block">Empate</small>
                  <small v-else-if="quotation.choice_slug === '2'" style="display: block">Fora</small>
                  {{ quotation.value | formatQuotation }}
                </button>
                <button
                  style="width:60px;"
                  :class="{'btn-selected': verifyMatchSelected(match.id) }"
                  type="button" class="btn btn-default"
                  @click="loadMoreQuotations(match.id, match.quotations_url)">
                  <small style="display: block">Outros</small>
                  + {{ match.quotations_qty }}
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
        required: true, type: Array
      },
      pageTitle: {
        required: true, type: String
      }
    },
    computed: {
      campeonatosOrdenados() {
        return _.orderBy(this.leagues, ['order', 'country.name'], ['desc', 'asc'])
      }
    },
    methods: {
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
  }
</script>

<style scoped>
  .btn {
    padding: 6px;
  }

  span.btn-xs {
    font-size: .75em;
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
