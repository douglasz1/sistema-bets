@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Gerenciar cotações</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-striped table-borderless table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Ordenação</b></td>
                    <td><b>Nome</b></td>
                    <td><b>Variação</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($quotationCategories as $quotationCategory)
                    <tr>
                        <td>{{ $quotationCategory->order }}</td>
                        <td>{{ $quotationCategory->label }}</td>
                        <td>{{ $quotationCategory->quotation_modifier }} %</td>
                        <td>
                            <a href="{{ route('technical.quotations.categories.edit', ['id' => $quotationCategory->id]) }}"
                               class="btn btn-info">
                                <i class="fa fa-pencil"></i>
                                Editar
                            </a>
                            @if($quotationCategory->active)
                                <a href="{{ route('technical.quotations.categories.change.status', ['id' => $quotationCategory->id]) }}"
                                   class="btn btn-danger">
                                    <i class="fa fa-ban"></i>
                                    Desativar
                                </a>
                            @else
                                <a href="{{ route('technical.quotations.categories.change.status', ['id' => $quotationCategory->id]) }}"
                                   class="btn btn-success">
                                    <i class="fa fa-check"></i>
                                    Ativar
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nenhuma aposta encontrada</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
