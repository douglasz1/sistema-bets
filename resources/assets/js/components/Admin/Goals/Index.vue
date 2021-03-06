<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Operadores com apostas</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <div class="form-group">
        <input v-model="startDate" type="date" class="form-control">
      </div>
      <div class="form-group">
        <input v-model="finalDate" type="date" class="form-control">
      </div>
      <button v-if="loading" type="button" class="btn btn-primary">
        <i class="fa fa-spinner fa-spin"></i>
        Carregando
      </button>
      <button v-else @click.prevent="getSellers" class="btn btn-success">
        <i class="fa fa-filter"></i> Filtrar
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Meta de vendas</b></td>
          <td><b>Apostas</b></td>
          <td><b>Porcentagem da meta</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="seller in filteredSellersWithBets" :key="seller.id">
          <td>
            <b>{{ seller.name }}</b>
            <br>
            {{ seller.company }}
          </td>
          <td>
            {{ seller.sales_goal | formatMoney }}
          </td>
          <td>
            {{ seller.bets_value | formatMoney }}
          </td>
          <td>
            <div class="progress" style="margin-bottom: 5px;">
              <div class="progress-bar"
                   v-bind:style="{width: seller.goal_percentage + '%'}"
                   :class="{
                                            'progress-bar-danger': seller.goal_percentage <= 30,
                                            'progress-bar-warning': seller.goal_percentage > 30 && seller.goal_percentage < 65,
                                            'progress-bar-info': seller.goal_percentage >= 65 && seller.goal_percentage < 80,
                                            'progress-bar-primary': seller.goal_percentage >= 80 && seller.goal_percentage < 100,
                                            'progress-bar-success': seller.goal_percentage >= 100
                                         }">
                {{ seller.goal_percentage }}%
                <!--sss-->
              </div>
            </div>
          </td>
        </tr>
        <tr v-if="filteredSellersWithBets.length === 0">
          <td colspan="4" class="text-center">
            <b>Nenhum cambista encontrado</b>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="block-title">
      <h2>Cambistas que não fizeram apostas</h2>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="seller in filteredSellersWithoutBets" :key="seller.id">
          <td>
            <b>{{ seller.name }}</b>
            <br>
            {{ seller.company }}
          </td>
        </tr>
        <tr v-if="filteredSellersWithoutBets.length === 0">
          <td colspan="1" class="text-center">
            <b>Nenhum cambista encontrado</b>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        loading: false,
        finalDate: '',
        startDate: '',
        filter: '',
        onlyActives: 'actives',
        sellersWithBets: [],
        sellersWithoutBets: []
      }
    },
    methods: {
      getSellers() {
        this.loading = true
        this.$http
          .post('/admin/goals/all', {startDate: this.startDate, finalDate: this.finalDate})
          .then(response => {
            this.sellersWithBets = response.body.sellersWithBets
            this.sellersWithoutBets = response.body.sellersWithoutBets
          })
          .catch(() => this.$swal(
            'Erro ao listar cambistas!',
            'Ocorreu algum erro ao listar, por favor, tente novamente mais tarde.',
            'error'
          ))
          .finally(() => this.loading = false)
      }
    },
    computed: {
      filteredSellersWithBets() {
        const filter = this.filter
        const list = _.orderBy(this.sellersWithBets, ['company', 'goal_percentage'], ['asc', 'desc'])

        if (filter === '') {
          return list
        }

        return _.filter(list, seller => {
          return seller.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      },
      filteredSellersWithoutBets() {
        const filter = this.filter
        const list = _.orderBy(this.sellersWithoutBets, ['company', 'name'], ['asc', 'asc'])

        if (filter === '') {
          return list
        }

        return _.filter(list, seller => {
          return seller.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    mounted() {
      this.startDate = new Date().toISOString().slice(0, 10)
      this.finalDate = this.startDate
    },
    created() {
      this.getSellers()
    }
  }
</script>
