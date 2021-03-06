<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Supervisores cadastrados</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <a href="/admin/supervisors/create" class="btn btn-info">
        <i class="fa fa-plus"></i> Novo supervisor
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-striped table-borderless table-condensed table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Empresas</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="supervisor in filteredSupervisors">
          <td>{{ supervisor.name }}</td>
          <td>
            <ul>
              <li v-for="company in supervisor.companies">
                {{ company.name }} - {{ company.pivot.percentage }}%
              </li>
            </ul>
          </td>
          <td>
            <a :href="`/admin/supervisors/edit/${supervisor.id}`"
               class="btn btn-default btn-effect-ripple">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="supervisor.active" :href="`/admin/supervisors/status/${supervisor.id}`"
               class="btn btn-danger btn-effect-ripple">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else :href="`/admin/supervisors/status/${supervisor.id}`"
               class="btn btn-success btn-effect-ripple">
              <i class="fa fa-check"></i> Ativar
            </a>
          </td>
        </tr>
        <tr v-if="filteredSupervisors.length === 0">
          <td colspan="3" class="text-center">
            <b>Nenhum supervisor encontrado</b>
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
        onlyActives: 'all',
        supervisors: []
      }
    },
    methods: {
      getSupervisors: function () {
        this.$http.get('/admin/supervisors/all').then(response => {
          response.json().then(res => this.supervisors = res.supervisors)
        }, err => console.log(err))
      }
    },
    computed: {
      filteredSupervisors: function () {
        const filter = this.filter
        const list = _.orderBy(this.supervisors, 'name', 'asc')

        return _.filter(list, supervisors => {
          if (this.onlyActives === 'actives') {
            return supervisors.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && supervisors.active

          } else if (this.onlyActives === 'disabled') {
            return supervisors.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && !supervisors.active
          }

          return supervisors.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    created() {
      this.getSupervisors()
    }
  }
</script>
