<template>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Gerentes cadastrados</h2>
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
        <a href="/supervisor/managers/create" class="btn btn-info">
          <i class="fa fa-user-plus"></i> Novo operador
        </a>
      </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-striped table-borderless table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Nome</b></td>
                    <td><b>Empresa</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="manager in filteredManagers">
                    <td>{{ manager.name }}</td>
                    <td>{{ manager.company.name || '' }}</td>
                    <td>
                        <a :href="`/supervisor/managers/edit/${manager.id}`"
                           class="btn btn-default btn-effect-ripple">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a v-if="manager.active" :href="`/supervisor/managers/status/${manager.id}`"
                           class="btn btn-danger btn-effect-ripple">
                            <i class="fa fa-ban"></i> Desativar
                        </a>
                        <a v-else :href="`/supervisor/managers/status/${manager.id}`"
                           class="btn btn-success btn-effect-ripple">
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
      getAllManagers: function () {
        this.$http.get('/supervisor/managers/all').then(response => {
          response.json().then(res => this.managers = res.managers)
        }, error => {
          console.log(error)
        })
      }
    },
    computed: {
      filteredManagers: function () {
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
      this.getAllManagers()
    }
  }
</script>
