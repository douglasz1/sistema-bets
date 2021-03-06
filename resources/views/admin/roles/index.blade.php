@extends('layouts.admin')

@section('content')
    <div class="block full block-with-table">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Gerenciar níveis de usuários</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Nome</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->label }}</td>
                        <td>
                            <a href="{{ route('admin.roles.edit', ['id' => $role->id]) }}"
                               class="btn btn-default btn-effect-ripple">
                                <i class="fa fa-pencil"></i>
                                Editar
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
