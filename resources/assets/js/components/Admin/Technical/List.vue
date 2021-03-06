<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Técnicos cadastrados</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <a href="/admin/technical/create" class="btn btn-info btn-effect-ripple">
        <i class="fa fa-user-plus"></i> Novo técnico
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="technical in filteredTechnical">
          <td>{{ technical.name }}</td>
          <td>
            <a :href="`/admin/technical/edit/${technical.id}`"
               class="btn btn-default btn-effect-ripple">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="technical.active" :href="`/admin/technical/status/${technical.id}`"
               class="btn btn-danger btn-effect-ripple">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else :href="`/admin/technical/status/${technical.id}`"
               class="btn btn-success btn-effect-ripple">
              <i class="fa fa-check"></i> Ativar
            </a>
          </td>
        </tr>
        <tr v-if="filteredTechnical.length === 0">
          <td colspan="2" class="text-center">
            <b>Nenhum técnico encontrado</b>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import { showToast } from '../../../functions/showToast'

  export default {
    data () {
      return {
        filter: '',
        onlyActives: 'actives',
        technical: []
      }
    },
    methods: {
      getSupervisors: function () {
        this.$http.get('/admin/technical/all')
          .then(response => {
            this.technical = response.body.technical
          })
          .catch(() => showToast('Erro ao listar técnicos'))
      }
    },
    computed: {
      filteredTechnical: function () {
        const filter = this.filter
        const list = _.orderBy(this.technical, 'name', 'asc')

        return _.filter(list, technical => {
          if (this.onlyActives === 'actives') {
            return technical.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && technical.active

          } else if (this.onlyActives === 'disabled') {
            return technical.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0 && !technical.active
          }

          return technical.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0
        })
      }
    },
    created () {
      this.getSupervisors()
    }
  }
</script>
