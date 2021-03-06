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
    <div class="btn-group btn-group-full btn-group-space p-lr p-b">
      <div class="form-group">
        <input class="form-control" v-model="filter" placeholder="Filtrar por nome">
      </div>
      <a href="/admin/managers/create" class="btn btn-info">
        <i class="fa fa-user-plus"></i> Novo gerente
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td><b>Detalhes</b></td>
          <td width="290"><b>Descrição</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="manager in filteredManagers" :key="manager.id">
          <td>
            {{ manager.name }}
            <br>
            {{ manager.company ? manager.company.name : '' }}
          </td>
          <td>
            {{ manager.manager_commission }}
            <br>
            {{ manager.quotation_modifier }}
          </td>
          <td>
            <span v-html="manager.comments"></span>
          </td>
          <td>
            <a :href="`/admin/managers/edit/${manager.id}`"
               class="btn btn-default btn-effect-ripple">
              <i class="fa fa-pencil"></i>
            </a>
            <a v-if="manager.active"
               :href="`/admin/managers/status/${manager.id}`"
               class="btn btn-danger btn-effect-ripple">
              <i class="fa fa-ban"></i> Desativar
            </a>
            <a v-else
               :href="`/admin/managers/status/${manager.id}`"
               class="btn btn-success btn-effect-ripple">
              <i class="fa fa-check"></i> Ativar
            </a>
          </td>
        </tr>
        <tr v-if="filteredManagers.length === 0">
          <td colspan="4" class="text-center">
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
        this.$http.get('/admin/managers/all').then(response => {
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

<style>
    .table tr p {
        margin-bottom: 0 !important;
    }
</style>
