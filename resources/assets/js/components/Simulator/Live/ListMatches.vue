<template>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="block full">
      <div class="block-title hide">
        <h2>
          <i class="fa fa-futbol-o"></i> Partidas ao vivo
        </h2>
      </div>
      <div class="lista-partidas">
        <div v-for="(eventosLiga, id) in eventos" :key="id" class="lista-partidas--liga">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="widget">
                <div class="widget-content themed-background-primary clearfix">
                  <img
                    :src="`/storage/flags/${eventosLiga[0].pais.id}.svg`"
                    class="pull-left flag-league"
                  >
                  <h4 class="widget-heading h4 text-light">
                    <strong>{{ `${eventosLiga[0].pais.nome}: ${eventosLiga[0].liga.nome}` }}</strong>
                  </h4>
                </div>
              </div>
            </div>
          </div>
          <div v-for="evento in eventosLiga" :key="evento.id" class="row partida">
            <div class="col-md-4 col-sm-4 col-xs-8 partida-detalhes">
              <h4>{{ `${evento.time_casa} x ${evento.time_fora}` }}</h4>
            </div>
            <div class="col-md-1 col-sm-4 col-xs-2">
              <h4>{{ evento.placar }}</h4>
            </div>
            <div class="col-md-1 col-sm-4 col-xs-2">
              <h4>{{ evento.tempo.minutos }}'</h4>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 text-right">
              <button
                v-for="quotation in filtrarCotacoes(evento.odds)"
                style="width:60px;"
                :key="quotation.id"
                :class="{'btn-selected': verifyChoice(quotation.id) }"
                type="button"
                class="btn btn-default"
                @click="addQuotations(quotation, evento)"
              >
                <small v-if="quotation.choice_slug === '1'" style="display: block">Casa</small>
                <small v-else-if="quotation.choice_slug === 'X'" style="display: block">Empate</small>
                <small v-else-if="quotation.choice_slug === '2'" style="display: block">Fora</small>
                <span v-if="quotation.suspend !== 0" style="display: block">
                  <i class="fa fa-lock"></i>
                </span>
                <span v-else>{{ quotation.value | formatQuotation }}</span>
              </button>
              <button
                style="width:60px;"
                :class="{'btn-selected': verifyMatchSelected(evento.id) }"
                type="button"
                class="btn btn-default"
                @click="openMatch(evento)"
              >
                <small style="display: block">Outros</small>
                + {{ evento.odds.length || 0 }}
              </button>
            </div>
            <LoadQuotations v-if="eventoSelecionado === evento.id" :match="evento"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadQuotations from "./LoadQuotations";

export default {
  components: { LoadQuotations },
  props: {
    alterarCotacoes: {
      required: true
    },
    eventos: {
      required: true
    },
    eventoSelecionado: {
      default: null,
      required: true
    }
  },
  methods: {
    filtrarCotacoes(odds) {
      return odds.filter(odd => odd.bet_slug === "full_time_result");
    },
    alterarCotacao(valor) {
      const odd = parseFloat(valor) + valor * this.alterarCotacoes;
      if (odd < 1) return 1;
      if (odd > 100) return 100;
      return parseFloat(odd).toFixed(2);
    },
    formatDate(match) {
      return this.$parent.formatDate(match.match_date);
    },
    verifyChoice(choice_id) {
      return this.$parent.$refs.betSimulate.verifyChoice(choice_id);
    },
    addQuotations(quotation, evento) {
      quotation.match_id = evento.id;
      quotation.match_date = this.formatDate(new Date());
      quotation.match_name = `${evento.time_casa} x ${evento.time_fora}`;

      this.$parent.$refs.betSimulate.addQuotations(quotation);
    },
    openMatch(match) {
      this.$parent.eventoSelecionado =
        this.eventoSelecionado !== match.id ? match.id : null;
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

h5 {
  font-size: 14px;
}
@media screen and (max-width: 520px) {
  .partida > .col-md-6.col-xs-12 {
    float: left;
  }
}
</style>
