<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Cancelar palpites</h2>
    </div>
    <div class="btn-group btn-group-full p-lr p-tb">
      <div class="form-group">
        <input type="text" class="form-control" v-model="pesquisa" placeholder="Nome do time">
      </div>
      <button v-if="loading" type="button" class="btn btn-default">
        <i class="fa fa-spinner fa-spin"></i> Buscando...
      </button>
      <button v-else @click.prevent="buscarPartidas" type="submit" class="btn btn-success">
        <i class="fa fa-filter"></i> Buscar
      </button>
    </div>
    <div v-if="partidaEscolhida">
      <div class="block-title">
        <h2>Partida escolhida - {{ `${partidaEscolhida.home_team} x ${partidaEscolhida.away_team}` }}</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space tres-botoes p-tb p-lr">
        <div class="form-group">
          <select class="form-control" v-model="cancelarPor">
            <option value="todos">Todos os palpites</option>
            <option value="data">Cancelar à partir de data</option>
          </select>
        </div>
        <div v-if="cancelarPor === 'data'" class="form-group">
          <datetime format="YYYY-MM-DD H:i:s" class="form-control" v-model="dataParaCancelar"></datetime>
        </div>
        <div class="form-group">
          <button @click.prevent="perguntarParaCancelar" type="button" class="btn btn-danger">
            <i class="fa fa-ban"></i>
            Cancelar palpites
          </button>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
        <thead>
          <tr>
            <td>
              <b>Liga</b>
            </td>
            <td>
              <b>Partida</b>
            </td>
            <td>
              <b>Data</b>
            </td>
            <td>
              <b>Opções</b>
            </td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="partida in partidas" :key="partida.id">
            <td>{{ `${partida.league.country.name}: ${partida.league.name}` }}</td>
            <td>{{ `${partida.home_team} x ${partida.away_team}` }}</td>
            <td>{{ formatarData(partida.match_date) }}</td>
            <td>
              <button @click.prevent="escolherPartida(partida)" class="btn btn-success">
                <i class="fa fa-check"></i> Escolher
              </button>
            </td>
          </tr>
          <tr v-if="partidas.length === 0">
            <td colspan="3" class="text-center">
              <b>Nenhuma partida encontrada</b>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import datetime from "vuejs-datetimepicker";

export default {
  components: { datetime },
  name: "cancelar-palpites",
  data() {
    return {
      loading: false,
      pesquisa: "",
      partidas: [],
      partidaEscolhida: null,
      dataParaCancelar: null,
      cancelarPor: "todos"
    };
  },
  methods: {
    formatarData(data) {
      return moment(data).format("DD/MM/YY HH:mm");
    },
    escolherPartida(partida) {
      this.dataParaCancelar = partida.match_date;
      this.partidaEscolhida = partida;
    },
    buscarPartidas() {
      this.loading = true;
      this.partidaEscolhida = null;

      this.$http
        .get("/admin/cancelar-palpites/buscar", {
          params: { pesquisa: this.pesquisa }
        })
        .then(response => {
          this.loading = false;
          this.partidas = response.data.partidas;
        })
        .catch(err => console.log(err));
    },
    perguntarParaCancelar() {
      const partida = this.partidaEscolhida;
      this.$swal({
        title: "Cancelar palpites",
        html: `Deseja cancelar os palpites da partida <b>${
          partida.home_team
        } x ${partida.away_team}</b>?`,
        type: "warning",
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText:
          '<i class="glyphicon glyphicon-chevron-left"></i> Voltar',
        confirmButtonText:
          '<i class="glyphicon glyphicon-ok"></i> Sim, cancelar'
      })
        .then(result => {
          if (result) this.cancelarPalpites();
        })
        .catch(() => false);
    },
    cancelarPalpites() {
      this.$http
        .post("/admin/cancelar-palpites/cancelar", {
          cancelar_por: this.cancelarPor,
          partida: this.partidaEscolhida.match_id,
          data_cancelar: this.dataParaCancelar
        })
        .then(() =>
          this.$swal("Sucesso!", "Palpites cancelados com sucesso", "success")
        )
        .catch(() => this.$swal("Erro!", "Erro ao cancelar plapites", "error"));
    }
  },
  created() {
    this.buscarPartidas();
  }
};
</script>

<style>
#tj-datetime-input {
  background-color: #ffffff;
  color: #000000;
  border: none;
  margin-top: -5px;
}
</style>
