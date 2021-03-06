<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Ligas cadastradas</h2>
    </div>
    <div class="form-group p-lr p-tb">
      <input type="text" class="form-control" v-model="filter" placeholder="Nome da liga">
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-striped table-borderless table-condensed table-hover">
        <thead>
        <tr>
          <td><b>Liga</b></td>
          <td><b>Ordenação</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="league in filteredLeagues">
          <td>{{ league.name }}</td>
          <td>#{{ league.order }}</td>
          <td>
            <a :href="league.edit_link" class="btn btn-default btn-effect-ripple">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="league.active" :href="league.change_status" class="btn btn-danger btn-effect-ripple">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else :href="league.change_status" class="btn btn-success btn-effect-ripple">
              <i class="fa fa-check"></i> Ativar
            </a>
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
</template>

<script>
  export default {
    data () {
      return {
        leagues: [],
        filter: ''
      }
    },
    methods: {
      getLeagues: function () {
        this.$http.get('/technical/leagues/all').then(response => {
          response.json().then(res => this.leagues = res.leagues);
        }, error => {
          console.log(error);
        });
      },
    },
    computed: {
      filteredLeagues: function () {
        const filter = this.filter;
        const list = _.orderBy(this.leagues, 'name', 'asc');

        if (filter === '') {
          return list;
        }

        return _.filter(list, leagues => leagues.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0);
      },
    },
    created() {
      this.getLeagues();
    }
  }
</script>
