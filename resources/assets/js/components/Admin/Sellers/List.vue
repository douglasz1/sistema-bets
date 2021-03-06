<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Operadores cadastrados</h2>
    </div>
    <div class="btn-group btn-group-full tres-botoes p-tb p-lr">
      <button :class="{'btn-primary': onlyActives === 'actives'}" @click.prevent="onlyActives = 'actives'" class="btn btn-default">Ativos</button>
      <button :class="{'btn-primary': onlyActives === 'disabled'}" @click.prevent="onlyActives = 'disabled'" class="btn btn-default">Inativos</button>
      <button :class="{'btn-primary': onlyActives === 'all'}" @click.prevent="onlyActives = 'all'" class="btn btn-default">Todos</button>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-b p-lr">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <a href="/admin/sellers/create" class="btn btn-info">
        <i class="fa fa-user-plus"></i> Novo operador
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Saldo</b></td>
          <td><b>Jogos</b></td>
          <td width="290"><b>Descrição</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr
          v-for="(seller, index) in filteredSellers"
          :key="seller.id">
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
            <span v-html="seller.comments"></span>
          </td>
          <td>
            <a :href="`/admin/sellers/edit/${seller.id}`"
               class="btn btn-default btn-effect-ripple">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="seller.active"
               :href="`/admin/sellers/status/${seller.id}`"
               class="btn btn-danger btn-effect-ripple">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else
               :href="`/admin/sellers/status/${seller.id}`"
               class="btn btn-success btn-effect-ripple">
              <i class="fa fa-check"></i> Ativar
            </a>
            <button
              v-if="canDeleteSellers"
              @click.prevent="deleteSeller(seller, index)"
              class="btn btn-warning btn-effect-ripple">
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
        <tr v-if="filteredSellers.length === 0">
          <td colspan="5" class="text-center">
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
        canDeleteSellers: (process.env.ADMIN_DELETE_SELLERS == 'true'),
        filter: '',
        onlyActives: 'actives',
        sellers: []
      }
    },
    methods: {
      getSellers() {
        this.$http.get('/admin/sellers/all').then(response => {
          this.sellers = response.body.sellers.map(item => item)
        }, err => console.log(err))
      },
      deleteSeller(seller, index) {
        this.$http.delete(`/admin/sellers/${seller.id}`)
          .then(() => this.sellers.splice(index, 1))
          .catch(() => showToast('Erro ao apagar vendedor'))
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
