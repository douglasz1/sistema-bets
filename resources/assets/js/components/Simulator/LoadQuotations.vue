<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 xs-all">
        <div class="list-group-item list-group-header clearfix">
          <i v-show="loading" class="fa fa-spinner fa-2x fa-spin text-info pull-left"></i>
          <button class="btn btn-danger pull-right" @click="closePanel(matchId)">
            <i class="fa fa-remove"></i>
          </button>
        </div>
      </div>
      <div
        v-for="quotation in quotationsCategory"
        :key="quotation.name"
        class="col-md-12 col-sm-12 col-xs-12 xs-all"
      >
        <div class="list-group-item list-group-item-info col-md-12">
          <h4 class="cotacoes-titulo">
            <i class="fa fa-th-list"></i>
            {{ quotation.name }}
          </h4>
        </div>
        <div>
          <div v-for="q in quotation.quotations" :key="q.id" class="list-group-item col-md-12">
            <div
              @click="addQuotations(q)"
              :class="{'btn-selected': verifyChoice(q.id) }"
              class="btn-quotation"
            >
              <div class="btn-quotation__flexwrap">
                <div class="btn-quotation__label-wrapper">
                  <span class="btn-quotation__label">{{ q.choice_name }}</span>
                </div>
                <div class="btn-quotation__odds-wrapper">
                  <span class="btn-quotation__odds">{{ q.value | formatQuotation }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <h3 v-if="quotationsCategory.length === 0">Nenhuma cotação nesta partida</h3>
      </div>
    </div>
  </div>
</template>

<script>
import ThreeOptions from "./QuotationsTemplates/ThreeOptions";

export default {
  components: { ThreeOptions },
  data() {
    return {
      quotationsCategory: [],
      loading: true
    };
  },
  methods: {
    getQuotations() {
      if (this.quotationsUrl !== undefined) {
        this.$http.post(this.quotationsUrl).then(
          response => {
            this.quotationsCategory = response.body.quotations;
            this.loading = false;
          },
          err => {
            console.error(err);
          }
        );
      }
    },
    closePanel(matchId) {
      const elementById = document.getElementById("match-" + matchId);
      elementById.innerHTML = "<div></div>";
      window.scrollTo(0, elementById.offsetTop);
      this.$destroy();
    },
    addQuotations(quotation) {
      const { match } = quotation;
      const { league, sport } = match;

      quotation.sport_name = sport.name;
      quotation.league_name = `${league.country.name}: ${league.name}`;

      this.$parent.$refs.betSimulate.addQuotations(quotation);
      this.closePanel(quotation.match_id);
    },
    verifyChoice(choice_id) {
      return this.$parent.$refs.betSimulate.verifyChoice(choice_id);
    }
  },
  created() {
    this.getQuotations();
  }
};
</script>

