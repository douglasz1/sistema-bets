<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Adicionar supervisores</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
      <div class="form-group">
        <v-select
          placeholder="Supervisores"
          v-model="supervisor"
          :options="supervisors">
        </v-select>
      </div>
      <button @click.prevent="addSupervisor" class="btn btn-info">
        <i class="fa fa-user-plus"></i> Adicionar
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
        <thead>
        <tr>
          <td><b>Nome</b></td>
          <td style="max-width: 150px; width: 150px"><b>Porcentagem</b></td>
          <td><b>Opções</b></td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(supervisor, index) in selectedList" :key="supervisor.id">
          <td>
            <b>{{ supervisor.name }}</b>
          </td>
          <td>
            <div class="input-group">
              <input v-model="supervisor.percentage" type="number" min="0" max="100" class="form-control">
              <span class="input-group-addon">%</span>
            </div>
          </td>
          <td>
            <button @click.prevent="removeSupervisor(index)" class="btn btn-danger">
              <i class="fa fa-ban"></i> Remover
            </button>
          </td>
        </tr>
        <tr v-if="selectedList.length === 0">
          <td colspan="3">
            <b>Nenhum supervisor adicionado</b>
          </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
          <td><b>Total:</b> {{ selectedList.length }}</td>
          <td :class="{'text-danger': totalPercentage > 100 || totalPercentage < 100}">
            {{ totalPercentage }}%
          </td>
          <td></td>
        </tr>
        </tfoot>
      </table>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-tb p-lr">
      <a href="javascript:history.go(-1)" class="btn btn-danger">
        <i class="fa fa-arrow-left"></i> Voltar
      </a>
      <button
        v-if="totalPercentage === 100 || selectedList.length === 0"
        @click.prevent="save"
        class="btn btn-success">
        <i class="fa fa-save"></i> Salvar
      </button>
    </div>
  </div>
</template>

<script>
  import {showToast} from '../../../functions/showToast'
  import vSelect from 'vue-select'

  export default {
    components: {vSelect},
    props: ['id'],
    data() {
      return {
        supervisors: [],
        selectedList: [],
        supervisor: null,
        company: null
      }
    },
    methods: {
      getCompany() {
        this.$http.get('/admin/companies/get-supervisors/' + this.id)
          .then(response => {
            response.json().then(res => {
              this.company = res.company
              this.selectedList = res.supervisors.map(item => {
                return {
                  id: item.id,
                  name: item.name,
                  percentage: item.pivot.percentage
                }
              })
            })
          })
          .catch(() => this.$swal(
            'Erro ao listar dados!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      getSupervisors() {
        this.$http.get('/admin/supervisors/pluck')
          .then(response => {
            this.supervisors = response.body.supervisors
          })
          .catch(() => this.$swal(
            'Erro ao listar supervisores!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde.',
            'error'
          ))
      },
      addSupervisor() {
        if (this.supervisor !== null) {
          const list = this.selectedList
          let result = list.find(item => this.supervisor.value === item.id)

          if (result === undefined) {
            this.selectedList.push({
              id: this.supervisor.value,
              name: this.supervisor.label,
              percentage: 0
            })
          }
        }
      },
      removeSupervisor(index) {
        this.selectedList.splice(index, 1)
      },
      save() {
        this.$http.post('/admin/companies/save-supervisors/' + this.id, {
          supervisors: this.selectedList
        })
          .then(() => {
            this.$swal(
              'Dados salvos com sucesso!',
              'Tudo salvo, redirecionando...',
              'success'
            ).then(() => {
              window.location = '/admin/companies/'
            })
          })
          .catch(() => this.$swal(
            'Erro ao salvar dados!',
            'Ocorreu algum erro, por favor, tente novamente mais tarde.',
            'error'
          ))
      }
    },
    computed: {
      totalPercentage() {
        const list = this.selectedList
        return list.reduce((acc, item) => acc + parseInt(item.percentage), 0)
      }
    },
    created() {
      this.getCompany()
      this.getSupervisors()
    }
  }
</script>
