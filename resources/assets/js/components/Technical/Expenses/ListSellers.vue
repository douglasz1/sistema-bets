<template>
    <div class="block full block-with-table2">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Cambistas cadastrados</h2>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <input class="form-control" v-model="filter" id="filter" placeholder="Filtrar por nome">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <br>
                <div class="table-responsive">
                    <table class="table table-vcenter table-condensed table-striped table-hover">
                        <thead>
                        <tr>
                            <td><b>Nome</b></td>
                            <td><b>Descrição</b></td>
                            <td><b>Valor</b></td>
                            <td><b>Salvar</b></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="seller in filteredSellers" :class="{'success': seller.save}">
                            <td>
                                <b>{{ seller.name }}</b> <br/>
                                {{ seller.roles[0].label }} <br/>
                                {{ seller.company ? seller.company.name : '' }}
                            </td>
                            <td>
                                <input v-model="seller.expenses.description" class="form-control"
                                       placeholder="Descrição">
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                    <input v-model="seller.expenses.value" class="form-control" type="number"
                                           placeholder="Valor">
                                </div>
                            </td>
                            <td>
                                <button @click.prevent="saveSeller(seller)" class="btn btn-default btn-effect-ripple">
                                    <i class="fa fa-save"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredSellers.length === 0">
                            <td colspan="4" class="text-center">
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
  import { showToast } from '../../../functions/showToast'

  export default {
    data () {
      return {
        isLoading: false,
        sellers: [],
        filter: ''
      }
    },
    computed: {
      filteredSellers () {
        const filter = this.filter
        const list = _.orderBy(this.sellers, 'name', 'asc')

        if (filter === '') {
          return list
        }

        return _.filter(list, user => {
          return user.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    methods: {
      getSellers () {
        this.$http.get('/technical/expenses/sellers')
          .then(response => this.sellers = response.body.sellers.map(item => {
            item.save = false
            item.expenses = {
              value: 0,
              description: ''
            }
            return item
          }))
          .catch(() => showToast('Erro ao listar cambistas'))
      },
      saveSeller (seller) {
        seller.save = false

        this.$http
          .post('/technical/expenses/send', {
            seller: {
              seller_id: seller.id,
              company_id: seller.company_id,
              value: seller.expenses.value,
              description: seller.expenses.description
            }
          })
          .then(() => {
            seller.save = true

            var savedSeller = seller

            setTimeout(function () {
              savedSeller.save = false
            }, 300)
          })
          .catch(() => showToast('Erro ao cadastrar gasto!'))
      }
    },
    created () {
      this.getSellers()
    }
  }
</script>

<style scoped>
    .table .form-control {
        max-width: 200px;
    }
</style>
