<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Operadores cadastrados</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <button @click.prevent="resetAll(false)" class="btn btn-success">
        <i class="fa fa-repeat"></i> Resetar
      </button>
      <button @click.prevent="zeroToAll" class="btn btn-danger">
        <i class="fa fa-times"></i> Zerar
      </button>
      <button @click.prevent="resetAll(true)" class="btn btn-primary">
        <i class="fa fa-reply"></i> Forçar
      </button>
    </div>
    <div class="form-group">
      <button v-if="isLoading" class="btn btn-default">
        <i class="fa fa-spinner fa-spin"></i> Carregando
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Saldo</b></td>
          <td><b>Limite 1 jogo</b></td>
          <td><b>Salvar</b></td>
          <td><b>Resetar</b></td>
          <td><b>Zerar</b></td>
        </tr>
        </thead>
        <tbody>
        <tr
          v-for="seller in filteredSellers"
          :key="seller.id"
          :class="{'success': seller.reset, 'info': seller.save}">
          <td>
            <b>{{ seller.name }}</b>
            <br/>
            {{ seller.roles[0].label }}
            <br/>
            {{ seller.company ? seller.company.name : '' }}
            <br>
            <span class="label label-default">{{ seller.limit | formatMoney }}</span>
            <span v-if="seller.balance < 1" class="label label-danger">Sem saldo</span>
          </td>
          <td>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
              <input v-model="seller.balance" class="form-control" type="number"
                     placeholder="Adicionar ao saldo">
            </div>
          </td>
          <td>
            <div class="input-group has-error">
              <span class="input-group-addon"><i class="fa fa-usd"></i></span>
              <input v-model="seller.daily_limit" class="form-control" type="number"
                     placeholder="Limite diário">
            </div>
          </td>
          <td>
            <button @click.prevent="saveSeller(seller)" class="btn btn-info">
              <i class="fa fa-save"></i>
            </button>
          </td>
          <td>
            <button @click.prevent="resetBalance(seller)" class="btn btn-success">
              <i class="fa fa-repeat"></i>
            </button>
          </td>
          <td>
            <button @click.prevent="zeroToSeller(seller)" class="btn btn-danger">
              <i class="fa fa-times"></i>
            </button>
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
        isLoading: false,
        sellers: [],
        filter: ''
      }
    },
    computed: {
      filteredSellers() {
        const filter = this.filter
        const list = _.orderBy(this.sellers, 'company.name', 'asc')

        if (filter === '') {
          return list
        }

        return _.filter(list, user => {
          return user.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    methods: {
      getSellers() {
        this.$http.get('/admin/balance/sellers')
          .then(response => this.sellers = response.body.sellers.map(item => {
            item.save = false
            item.reset = false
            return item
          }))
          .catch(() => this.$swal(
            'Erro ao listar operadores!',
            'Ocorreu algum erro ao listar operadores, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      saveSeller(seller) {
        seller.save = false

        this.$http.post('/admin/balance/send', {seller})
          .then(response => {
            let responseSeller = response.body.seller

            seller.save = true
            seller.balance = responseSeller.balance
            seller.daily_limit = responseSeller.daily_limit

            var savedSeller = seller

            setTimeout(function () {
              savedSeller.save = false
            }, 300)
          })
          .catch(() => this.$swal(
            'Erro ao salvar alteração!',
            'Ocorreu algum erro ao salvar, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      resetBalance(seller) {
        this.$http.post('/admin/balance/reset', {seller})
          .then(response => {
            let responseSeller = response.body.seller

            seller.reset = true
            seller.balance = responseSeller.balance

            var savedSeller = seller

            setTimeout(function () {
              savedSeller.reset = false
            }, 300)
          })
          .catch(() => this.$swal(
            'Erro ao salvar alteração!',
            'Ocorreu algum erro ao salvar, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      resetAll(force = false) {
        this.$swal({
          title: 'Você gostaria de reiniciar o saldo de todos?',
          text: 'Esteja certo antes de continuar',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sim, reiniciar!',
          cancelButtonText: 'Não, fechar!'
        }).then(() => {
          this.isLoading = true

          this.$http.post('/admin/balance/reset-all', {force: force})
            .then(response => {
              this.isLoading = false

              this.sellers = response.body.sellers.map(item => {
                item.save = false
                return item
              })

              this.$swal(
                'Saldo reiniciado com sucesso!',
                'O saldo de todos os operadores foi reiniciado.',
                'success'
              )
            })
            .catch(() => {
              this.isLoading = false

              this.$swal(
                'Erro ao salvar alteração!',
                'Ocorreu algum erro ao reiniciar, por favor, tente novamente mais tarde.',
                'error'
              )
            })
        })
      },
      zeroToSeller(seller) {
        seller.balance = 0
        this.saveSeller(seller)
      },
      zeroToAll() {
        this.$swal({
          title: 'Você gostaria de zerar todos os saldos?',
          text: 'Esteja certo antes de continuar',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sim, zerar!',
          cancelButtonText: 'Não, fechar!'
        }).then(() => {
          this.isLoading = true

          this.$http.post('/admin/balance/zero-all')
            .then(() => {
              this.isLoading = false

              this.sellers = this.sellers.map(item => {
                item.balance = 0
                return item
              })

              this.$swal(
                'Saldo zerado com sucesso!',
                'O saldo de todos os operadores foi zerado.',
                'success'
              )
            })
            .catch(() => {
              this.isLoading = false

              this.$swal(
                'Erro ao salvar alteração!',
                'Ocorreu algum erro ao reiniciar, por favor, tente novamente mais tarde.',
                'error'
              )
            })
        })
      }
    },
    created() {
      this.getSellers()
    }
  }
</script>

<style scoped>
    .table .form-control {
        max-width: 100px;
        min-width: 80px;
    }

    .has-error .form-control,
    .has-error .input-group-addon {
        border-color: #fd692f;
        color: #fd692f;
    }

    .has-error .input-group-addon .fa-usd:before {
        color: #fd692f;
    }
</style>
