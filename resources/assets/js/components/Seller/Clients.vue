<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Clientes cadastrados</h2>
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
          <td><b>Deletar</b></td>
        </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td><b class="bold-remove-client">{{client.name}}</b></td>
            <td>
              <button @click.prevent="remove(client.id)" class="btn btn-danger btn-remove-client">
                  <i class="fa fa-trash-o"></i>
            </button>
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
        clients: [],
        filter: ''
      }
    },
    computed: {

    },
    methods: {
      getClients () {
        this.loading = true;
        this.$http.get('/seller/clients/list', {
        }).then(response => {
          response.json().then(res => {
            this.clients = res.clients;
            this.loading = false;
          });
        }, err => {
          console.log(err);
          this.loading = false;
        });
      },
      remove (id) {
        this.$swal({
          title: '',
          text: 'Essa operação não pode ser desfeita, deseja mesmo deletar esse cliente?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',
          confirmButtonText: 'Sim, apagar!',
          cancelButtonText: 'Não apagar!',
          closeOnConfirm: false,
          closeOnCancel: false
              }).then((confirmed) => {
                if (confirmed) {
                    this.loading = true;
                    this.$http.post(`/seller/clients/destroy/${id}`, {
                    }).then(response => {
                      response.json().then(res => {
                        this.clients = res.clients;
                        this.loading = false;
                      });
                    }, err => {
                        this.$swal(
                          '',
                          'Este cliente só pode ser deletado 7 dias após sua ultima aposta.',
                          'error'
                        )
                        this.loading = false;
                    });
                  }
                });
      }
    },
    created() {
      this.getClients();
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

    .btn-remove-client {
      margin-top: 5px;
      margin-bottom: 5px;
    }

    .bold-remove-client{
      margin-left: 5px;
    }

</style>
