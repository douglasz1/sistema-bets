<template>
  <div class="container-fluid mais-cotacoes">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 xs-all">
        <div class="list-group-item list-group-header clearfix">
          <button class="btn btn-danger pull-right" @click="closePanel(match)">
            <i class="fa fa-remove"></i>
          </button>
        </div>
      </div>
      <div
        v-for="categoria in categorias"
        :key="categoria[0].bet_slug"
        class="col-md-12 col-sm-12 col-xs-12 xs-all"
      >
        <div class="list-group-item list-group-item-info col-md-12">
          <h4 class="cotacoes-titulo">
            <i class="fa fa-th-list"></i>
            {{ categoria[0].bet_name }}
          </h4>
        </div>
        <div v-for="q in categoria" :key="q.id" class="list-group-item col-md-12">
          <div v-if="q.suspend === 1 || q.value < 1.01" class="btn-quotation">
            <div class="btn-quotation__flexwrap">
              <div class="btn-quotation__label-wrapper">
                <span class="btn-quotation__label">{{ q.choice_name }}</span>
              </div>
              <div class="btn-quotation__odds-wrapper">
                <span class="btn-quotation__odds">
                  <i class="fa fa-lock"></i>
                </span>
              </div>
            </div>
          </div>
          <div
            v-else
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
    </div>
  </div>
</template>

<script>
export default {
  props: {
    match: {
      required: true
    }
  },
  methods: {
    alterarCotacao(valor) {
      return this.$parent.alterarCotacao(valor);
    },
    closePanel(id) {
      this.$parent.openMatch({ id });
    },
    addQuotations(quotation) {
      const { id } = this.match;
      this.$parent.addQuotations(quotation, {
        time_casa: this.match.time_casa,
        time_fora: this.match.time_fora,
        id
      });
      this.closePanel(id);
    },
    verifyChoice(choice_id) {
      return this.$parent.$parent.$refs.betSimulate.verifyChoice(choice_id);
    }
  },
  computed: {
    categorias() {
      return _.chain(this.match.odds)
        .filter(odd => odd.bet_slug !== "full_time_result")
        .groupBy("bet_slug")
        .value();
    }
  }
};
</script>

