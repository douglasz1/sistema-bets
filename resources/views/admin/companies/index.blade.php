@extends('layouts.admin')

@section('content')
    @include('admin.usuarios_menu')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Gerenciar empresas</h2>
        </div>
        <div class="btn-group btn-group-full btn-group-space tres-botoes p-lr p-tb">
            <a href="{{ route('admin.companies.quotation-up') }}" class="btn btn-danger">
                <i class="fa fa-arrow-circle-up"></i> Cotação +
            </a>
            <a href="{{ route('admin.companies.quotation-down') }}" class="btn btn-warning">
                <i class="fa fa-arrow-circle-down"></i> Cotação -
            </a>
            <a href="{{ route('admin.companies.create') }}" class="btn btn-info">
                <i class="fa fa-plus"></i> Nova empresa
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <td><b>Nome</b></td>
                    <td><b>Nome na impressão</b></td>
                    <td><b>Cotação</b></td>
                    <td><b>Prêmio máximo</b></td>
                    <td><b>Múltiplicador</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($companies as $company)
                    <tr>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->print_name }}</td>
                        <td>{{ $company->quotation_modifier }}%</td>
                        <td>R$ {{ moneyBR($company->max_prize) }}</td>
                        <td>{{ $company->max_prize_multiplier }}x</td>
                        <td>
                            <a href="{{ route('admin.companies.edit', ['id' => $company->id]) }}"
                               class="btn btn-default">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="{{ route('admin.companies.supervisors', [$company->id]) }}" class="btn btn-primary">
                                <i class="fa fa-users"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
