<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Operadores cadastrados</h2>
    </div>
    <div class="form-group p-lr p-tb">
      <input class="form-control" v-model="filter" id="filter" placeholder="Filtrar por nome">
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Descrição</b></td>
          <td><b>Valor</b></td>
          <td><b>Data</b></td>
          <td><b>Salvar</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="seller in filteredSellers"
            :class="{'success': seller.save}"
            :key="seller.id">
          <td>
            <b>{{ seller.name }}</b> <br/>
            {{ seller.roles[0].label }} <br/>
            {{ seller.company ? seller.company.name : '' }}
          </td>
          <td>
            <input v-model="seller.expenses.description" class="form-control" placeholder="Descrição">
          </td>
          <td>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
              <input v-model="seller.expenses.value" class="form-control" type="number" placeholder="Valor">
            </div>
          </td>
          <td>
            <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-calendar"></i>
              </span>
              <input v-model="seller.expenses.date"
                     class="form-control" type="date"
                     placeholder="Data">
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
</template>

<script>
  import {showToast} from '../../../functions/showToast'

  export default {
    data () {
      return {
        date: '',
        isLoading: false,
        sellers: [],
        filter: ''
      }
    },
    computed: {
      filteredSellers() {
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
        let today = new Date().toISOString().slice(0, 10)
        this.$http.get('/admin/expenses/sellers')
          .then(response => this.sellers = response.body.sellers.map(item => {
            item.save = false
            item.expenses = {
              value: 0,
              description: '',
              date: today
            }
            return item
          }))
          .catch(() => this.$swal(
            'Erro ao listar cambistas!',
            'Ocorreu algum, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      saveSeller (seller) {
        seller.save = false

        this.$http
          .post('/admin/expenses/send', {
            seller: {
              seller_id: seller.id,
              company_id: seller.company_id,
              value: seller.expenses.value,
              date: seller.expenses.date,
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
          .catch(() => this.$swal(
            'Erro ao cadastrar gasto!',
            'Ocorreu algum, por favor, tente novamente mais tarde.',
            'error'
          ))
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
