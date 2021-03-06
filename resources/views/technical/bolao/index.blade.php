@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Listar Bolões</h2>
        </div>
        <div class="text-right p-lr p-tb">
            <a href="{{ route('technical.bolao.criar') }}" class="btn btn-info">
                <i class="fa fa-plus"></i> Novo bolão
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
                <thead>
                <tr>
                    <td><b>N&deg;</b></td>
                    <td><b>Datas</b></td>
                    <td><b>Informações</b></td>
                    <td><b>Apostas</b></td>
                    <td><b>Apurações</b></td>
                    <td><b>Situação</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($boloes as $bolao)
                    <tr>
                        <td>#{{ $bolao->id }}</td>
                        <td>
                            <span class="text-muted">Criado em:</span>
                            {{ $bolao->created_at->format('d/m/Y H:i') }}
                            <br>
                            <span class="text-muted">Apostas até:</span>
                            {{ $bolao->data_limite->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="text-muted">Nome:</span>
                            {{ $bolao->nome }} <br>
                            <span class="text-muted">Tipo:</span>
                            {{ $bolao->tipo_bolao }} <br>
                        </td>
                        <td>
                            {{ $bolao->apostas_count }} apostas
                        </td>
                        <td>
                            <span class="text-muted">Bruto:</span>
                            {{ moneyBR($bolao->acumulado)  }}
                            <br>
                            <span class="text-muted">1º lugar:</span>
                            {{ moneyBR($bolao->acumulado * ($bolao->premio_1 / 100)) }}
                            <br>
                            <span class="text-muted">2º lugar:</span>
                            {{ moneyBR($bolao->acumulado * ($bolao->premio_2 / 100)) }}
                        </td>
                        <td>
                            @if($bolao->situacao === 'finalizado')
                                <span class="label label-info">Finalizado</span>
                            @elseif(\Carbon\Carbon::now() < $bolao->data_limite)
                                <span class="label label-success">Permitindo Apostas</span>
                            @elseif(\Carbon\Carbon::now() > $bolao->data_limite)
                                <span class="label label-danger">Apostas encerradas</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('technical.bolao.detalhes', ['id' => $bolao->id]) }}" class="btn btn-info">
                                Detalhes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <b>Nenhum bolão encontrado</b>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="pagination">
        {{ $boloes->links() }}
    </div>
@endsection
