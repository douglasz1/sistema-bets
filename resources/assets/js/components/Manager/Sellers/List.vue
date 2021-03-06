<template>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Operadores cadastrados</h2>
        </div>
        <div class="form-group p-tb p-lr">
          <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                    <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
                        <thead>
                        <tr>
                            <td><b>Nome</b></td>
                            <td><b>Saldo</b></td>
                            <td><b>Comissão</b></td>
                            <td><b>Opções</b></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(seller, index) in filteredSellers">
                            <td>
                                <b>{{ seller.name }}</b> <br/>
                                {{ seller.manager ? seller.manager.name : '' }} <br/>
                                {{ seller.company ? seller.company.name : '' }}
                            </td>
                            <td>
                                L: {{ seller.limit | formatMoney }} <br/>
                                S: {{ seller.balance | formatMoney }} <br/>
                            </td>
                            <td>
                                1: {{ parseFloat(seller.commission1).toFixed(1) }}% <br>
                                2: {{ parseFloat(seller.commission2).toFixed(1) }}% <br>
                                3: {{ parseFloat(seller.commission3).toFixed(1) }}%
                            </td>
                            <td>
                                <a v-if="seller.active"
                                   :href="`/manager/sellers/status/${seller.id}`"
                                   class="btn btn-danger btn-effect-ripple">
                                    <i class="fa fa-ban"></i> Desativar
                                </a>
                                <a v-else
                                   :href="`/manager/sellers/status/${seller.id}`"
                                   class="btn btn-success btn-effect-ripple">
                                    <i class="fa fa-check"></i> Ativar
                                </a>
                            </td>
                        </tr>
                        <tr v-if="filteredSellers.length === 0">
                            <td colspan="6" class="text-center">
                                <b>Nenhum cambista encontrado</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import {showToast} from '../../../functions/showToast'
  export default {
    data() {
      return {
        filter: '',
        onlyActives: 'all',
        sellers: []
      }
    },
    methods: {
      getSellers() {
        this.$http.get('/manager/sellers/all')
        .then(response => {
          this.sellers = response.body.sellers.map(item => item)
        })
        .catch(() => showToast('Erro ao listar cambistas'))
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
