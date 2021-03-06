<template>
  <div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8">
      <SportsSelect :sports="sports" :logged="true" :aoVivo="true" @selectSport="selectSport"/>
      <div class="row">
        <!-- listagem de partidas de todas as ligas -->
        <ListMatches
          ref="listMatches"
          :alterarCotacoes="alterarCotacoes"
          :eventoSelecionado="eventoSelecionado"
          :eventos="ligasOrdenadas"
        />
      </div>
    </div>
    <BetSimulate ref="betSimulate" :maxPrize="maxPrize" :maxPrizeMultiplier="maxPrizeMultiplier"/>
  </div>
</template>

<script>
import BetSimulate from "./BetSimulate";
import ListMatches from "./ListMatches";
import SportsSelect from "../SportsSelect";
import moment from "moment";
import io from "socket.io-client";

export default {
  name: "LiveBets",
  components: { BetSimulate, ListMatches, SportsSelect },
  data() {
    return {
      alterarCotacoes: 0,
      sports: [],
      eventos: [],
      ligasOrdenadas: [],
      ligas_inativas: [],
      partidas_inativas: [],
      maxPrize: 0,
      eventoSelecionado: null,
      maxPrizeMultiplier: 0
    };
  },
  methods: {
    selectSport(sport) {
      window.location = "/home";
    },
    formatDate(date) {
      return moment(date).format("DD/MM HH:mm");
    },
    getConfig() {
      this.$http.post("/configs/simulator").then(response => {
        response.json().then(res => {
          this.alterarCotacoes = parseFloat(res.alterarCotacoes) - 0.05;
          this.maxPrize = res.maxPrize;
          this.maxPrizeMultiplier = res.maxPrizeMultiplier;
          this.sports = res.sports;
        });
      });
    },
    pegarInativos() {
      this.$http.get("/live/matches").then(response => {
        response.json().then(res => {
          this.partidas_inativas = res.partidas_inativas;
          this.ligas_inativas = res.ligas_inativas;
          this.organizarLigas();
        });
      });
    },
    limparLigas() {
      setInterval(() => {
        this.organizarLigas();
      }, 20000);
    },
    organizarLigas(eventos = []) {
      const dataAtual = moment()
        .subtract(60, "seconds")
        .format("X");
      const listaAtual = this.eventos;
      const ligasInativas = this.ligas_inativas;
      const partidasInativas = this.partidas_inativas;
      const alterarCotacoes = valor => {
        const odd = parseFloat(valor) + valor * this.alterarCotacoes;
        if (odd < 1.01) return 0;
        if (odd > 100) return 100;
        return parseFloat(odd).toFixed(2);
      };

      if (eventos.length > 0) {
        eventos.map(partida => {
          partida.odds = partida.odds.map(odd => {
            odd.value = alterarCotacoes(odd.value);
            odd.league_name = `${partida.pais.nome}: ${partida.liga.nome}`;

            return odd;
          });
          const indexPartida = _.findIndex(
            listaAtual,
            item => item.id === partida.id
          );
          if (indexPartida >= 0) {
            listaAtual[indexPartida] = partida;
          } else {
            listaAtual.push(partida);
          }
        });
        this.eventos = listaAtual;
      }

      this.ligasOrdenadas = _.chain(listaAtual)
        .filter(({ tempo }) => {
          return (
            tempo.atualizado_em > dataAtual &&
            tempo.minutos >= 1 &&
            tempo.minutos <= 85
          );
        })
        .filter(({ liga }) => {
          return _.findIndex(ligasInativas, item => item === liga.id) < 0;
        })
        .filter(({ id }) => {
          return _.findIndex(partidasInativas, item => item === id) < 0;
        })
        .groupBy(evento => evento.liga.id)
        .sortBy([e => e[0].pais.nome, e => e[0].liga.nome])
        .value();
    },
    conectarServidor() {
      const socket = io("https://live.simuladoraqui.com.br:8080");
      socket.on("eventos-ao-vivo", dados => {
        this.organizarLigas(dados.eventos);
      });
    },
    loadMoreQuotations(match) {
      let Child = this.$options.components["load-live-quotations"];
      let child = new Child({
        el: this.$el.querySelector("#match-" + match.id + " > div"),
        parent: this,
        data() {
          return {
            match: match
          };
        }
      });
    }
  },
  created() {
    this.conectarServidor();
    this.pegarInativos();
    this.getConfig();
    this.limparLigas();
  }
};
</script>
