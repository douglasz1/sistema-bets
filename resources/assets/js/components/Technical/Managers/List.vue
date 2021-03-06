<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Gerentes cadastrados</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
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
      <a href="/technical/managers/create" class="btn btn-info">
        <i class="fa fa-user-plus"></i> Novo gerente
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Empresa</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="manager in filteredManagers" :key="manager.id">
          <td>{{ manager.name }}</td>
          <td>{{ manager.company ? manager.company.name : '' }}</td>
          <td>
            <a :href="`/technical/managers/edit/${manager.id}`"
               class="btn btn-default">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="manager.active"
               :href="`/technical/managers/status/${manager.id}`"
               class="btn btn-danger">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else
               :href="`/technical/managers/status/${manager.id}`"
               class="btn btn-success">
              <i class="fa fa-check"></i> Ativar
            </a>
          </td>
        </tr>
        <tr v-if="filteredManagers.length === 0">
          <td colspan="3" class="text-center">
            <b>Nenhum gerente encontrado</b>
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
        managers: []
      }
    },
    methods: {
      getManagers() {
        this.$http.get('/technical/managers/all').then(response => {
          this.managers = response.body.managers.map(item => item)
        }, err => console.log(err))
      }
    },
    computed: {
      filteredManagers() {
        const filter = this.filter
        const list = _.orderBy(this.managers, 'name', 'asc')

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
      this.getManagers()
    }
  }
</script>
