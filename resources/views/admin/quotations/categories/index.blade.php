@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Gerenciar cotações</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-borderless table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>#</b></td>
                    <td><b>Esporte</b></td>
                    <td><b>Mercado</b></td>
                    <td><b>Palpite</b></td>
                    <td><b>Variação</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($quotationCategories as $quotationCategory)
                    <tr>
                        <td>#{{ $loop->index + 1 }}</td>
                        <td>{{ $quotationCategory->esporte }}</td>
                        <td>{{ $quotationCategory->mercado_descricao }}</td>
                        <td>{{ $quotationCategory->palpite_descricao }}</td>
                        <td>{{ $quotationCategory->alterar_cotacao }} %</td>
                        <td>
                            <a href="{{ route('admin.quotations.categories.edit', ['id' => $quotationCategory->id]) }}"
                               class="btn btn-default">
                                <i class="fa fa-pencil"></i>
                                Editar
                            </a>
                            @if($quotationCategory->ativo)
                                <a href="{{ route('admin.quotations.categories.change.status', ['id' => $quotationCategory->id]) }}"
                                   class="btn btn-danger">
                                    <i class="fa fa-ban"></i>
                                    Desativar
                                </a>
                            @else
                                <a href="{{ route('admin.quotations.categories.change.status', ['id' => $quotationCategory->id]) }}"
                                   class="btn btn-info">
                                    <i class="fa fa-check"></i>
                                    Ativar
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
