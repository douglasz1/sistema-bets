<template>
  <div>
    <div class="btn-group btn-group-full p-b">
      <button
        class="btn btn-primary"
        :class="{'active': tipo === 'pre'}"
        @click.prevent="tipo = 'pre'"
      >Ligas para pré-jogo</button>
      <button
        class="btn btn-primary"
        :class="{'active': tipo === 'live'}"
        @click.prevent="tipo = 'live'"
      >Ligas para ao vivo</button>
    </div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Ligas disponíveis</h2>
      </div>
      <div class="form-group p-tb p-lr">
        <input type="text" class="form-control" v-model="filter" placeholder="Nome da liga">
      </div>
      <div class="table-responsive">
        <table
          class="table table-vcenter table-borderless table-striped table-condensed table-hover"
        >
          <thead>
            <tr>
              <td>
                <b>Liga</b>
              </td>
              <td>
                <b>Ordenação</b>
              </td>
              <td>
                <b>Opções</b>
              </td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="league in filteredLeagues" :key="league.id">
              <td>{{ `${league.country.name}: ${league.name}` }}</td>
              <td>#{{ league.order }}</td>
              <td>
                <a :href="`/admin/leagues/edit/${league.id}`" class="btn btn-default">
                  <i class="fa fa-pencil"></i>
                </a>
                <button
                  v-if="league.active"
                  @click.prevent="statusLiga(league)"
                  class="btn btn-danger"
                >
                  <i class="fa fa-ban"></i> Desativar
                </button>
                <button v-else @click.prevent="statusLiga(league)" class="btn btn-success">
                  <i class="fa fa-check"></i> Ativar
                </button>
              </td>
            </tr>
            <tr v-if="filteredLeagues.length === 0">
              <td colspan="3" class="text-center">
                <b>Nenhuma liga encontrada</b>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tipo: "pre",
      leagues: [],
      inativas_pre: [],
      inativas_live: [],
      filter: ""
    };
  },
  methods: {
    getLeagues() {
      this.$http.get("/admin/leagues/all").then(
        response => {
          response.json().then(res => {
            this.leagues = res.ligas;
            this.inativas_pre = res.inativas_pre;
            this.inativas_live = res.inativas_live;
          });
        },
        error => {
          console.log(error);
        }
      );
    },
    statusLiga(liga) {
      this.$http.get(`/admin/leagues/status/${liga.id}?tipo=${this.tipo}`).then(
        response => {
          if (this.tipo === "pre") {
            const index = this.inativas_pre.indexOf(liga.league_id);
            console.log(index);
            return index === -1
              ? this.inativas_pre.push(liga.league_id)
              : this.inativas_pre.splice(index, 1);
          } else {
            const index = this.inativas_live.indexOf(liga.league_id);
            return index === -1
              ? this.inativas_live.push(liga.league_id)
              : this.inativas_live.splice(index, 1);
          }
        },
        error => {
          console.log(error);
        }
      );
    }
  },
  computed: {
    filteredLeagues() {
      const filter = this.filter;
      const listaInativas =
        this.tipo === "pre" ? this.inativas_pre : this.inativas_live;

      const list = _.chain(this.leagues)
        .map(liga => {
          liga.active = listaInativas.indexOf(liga.league_id) < 0;
          return liga;
        })
        .orderBy(["country.name", "name"], ["asc", "asc"])
        .value();

      if (filter === "") {
        return list;
      }

      return _.filter(list, leagues => {
        return (
          leagues.country.name.toLowerCase().indexOf(filter.toLowerCase()) >=
            0 || leagues.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        );
      });
    }
  },
  created() {
    this.getLeagues();
  }
};
</script>
