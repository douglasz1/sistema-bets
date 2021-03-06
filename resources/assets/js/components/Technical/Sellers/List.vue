<template>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Operadores cadastrados</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
        <div class="form-group">
          <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
        </div>
        <div class="form-group">
          <select class="form-control" v-model="onlyActives">
            <option value="actives">Ativos</option>
            <option value="disabled">Inativos</option>
            <option value="all">Todos</option>
          </select>
        </div>
        <a href="/technical/sellers/create" class="btn btn-info">
          <i class="fa fa-user-plus"></i> Novo operador
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
          <thead>
          <tr>
            <td><b>Nome</b></td>
            <td><b>Saldo</b></td>
            <td><b>Jogos</b></td>
            <td><b>Comissão</b></td>
            <td><b>Valores</b></td>
            <td><b>Opções</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="seller in filteredSellers">
            <td>
              <b>{{ seller.name }}</b> <br/>
              {{ seller.manager ? seller.manager.name : '' }} <br/>
              {{ seller.company ? seller.company.name : '' }}
            </td>
            <td>
              {{ seller.limit }} <br/>
              {{ seller.balance }} <br/>
              {{ seller.quotation_modifier }}%
            </td>
            <td>
              Mín: {{ seller.tips_min }} <br>
              Máx: {{ seller.tips_max }} <br>
              Prê: {{ seller.max_prize }}
            </td>
            <td>
              1: {{ parseFloat(seller.commission1).toFixed(1) }}% <br>
              2: {{ parseFloat(seller.commission2).toFixed(1) }}% <br>
              3: {{ parseFloat(seller.commission3).toFixed(1) }}%
            </td>
            <td>
              {{ seller.value_min1 }}
              / {{ seller.value_max1 }}
              <br>
              {{ seller.value_min2 }}
              / {{ seller.value_max2 }}
              <br>
              {{ seller.value_min3 }}
              / {{ seller.value_max3 }}
            </td>
            <td>
              <a :href="`/technical/sellers/edit/${seller.id}`"
                 class="btn btn-default">
                <i class="fa fa-pencil"></i>
              </a>
              <a v-if="seller.active"
                 :href="`/technical/sellers/status/${seller.id}`"
                 class="btn btn-danger">
                <i class="fa fa-ban"></i> Desativar
              </a>
              <a v-else
                 :href="`/technical/sellers/status/${seller.id}`"
                 class="btn btn-success">
                <i class="fa fa-check"></i> Ativar
              </a>
            </td>
          </tr>
          <tr v-if="filteredSellers.length === 0">
            <td colspan="6" class="text-center">
              <b>Nenhum operador encontrado</b>
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
        filter: '',
        onlyActives: 'actives',
        sellers: []
      }
    },
    methods: {
      getSellers() {
        this.$http.get('/technical/sellers/all').then(response => {
          this.sellers = response.body.sellers.map(item => item)
        }, err => console.log(err))
      }
    },
    computed: {
      filteredSellers() {
        const filter = this.filter
        const list = _.orderBy(this.sellers, 'name', 'asc')

        return _.filter(list, user => {
          if (this.onlyActives === 'actives') {
            return user.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && user.active

          } else if (this.onlyActives === 'disabled') {
            return user.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && !user.active
          }

          return user.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    created() {
      this.getSellers()
    }
  }
</script>

<style scoped>
    @media only screen and (max-width: 640px) {
        table td:nth-child(3),
        table th:nth-child(3),
        table td:nth-child(4),
        table th:nth-child(4),
        table td:nth-child(5),
        table th:nth-child(5) {
            display: none;
        }
    }
</style>
