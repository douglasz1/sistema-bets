@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolões</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-borderless table-vcenter table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Mostrar</b></td>
                    <td><b>Informações</b></td>
                    <td><b>Valor</b></td>
                    <td><b>Apostas até</b></td>
                    <td><b>Situação</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($boloes as $bolao)
                    <tr>
                        <td>
                            @if(now() < $bolao->data_limite && $bolao->situacao === 'pendente')
                                <a href="{{ route('bolao.jogar', ['id' => $bolao->id]) }}" class="btn btn-info">
                                    <i class="fa fa-folder-open"></i>
                                </a>
                            @endif
                        </td>
                        <td>{{ $bolao->nome }}</td>
                        <td>R$ {{ moneyBR($bolao->valor) }}</td>
                        <td>
                            {{ dateToBrazil($bolao->data_limite) }}
                        </td>
                        <td>
                            @if($bolao->situacao === 'finalizado')
                                <span class="label label-info">Finalizado</span>
                            @elseif(now() < $bolao->data_limite)
                                <span class="label label-success">Permitindo Apostas</span>
                            @elseif(now() > $bolao->data_limite)
                                <span class="label label-danger">Apostas encerradas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <b>Nenhum bolão encontrado</b>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolões encerrados</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-borderless table-vcenter table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Resultado</b></td>
                    <td><b>Informações</b></td>
                    <td><b>Valor</b></td>
                    <td><b>Encerrado em</b></td>
                    <td><b>Situação</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($boloesEncerrados as $bolao)
                    <tr>
                        <td>
                            <a href="{{ route('bolao.resultado', ['id' => $bolao->id]) }}" class="btn btn-info">
                                Resultado
                            </a>
                        </td>
                        <td>{{ $bolao->nome }}</td>
                        <td>R$ {{ moneyBR($bolao->valor) }}</td>
                        <td>
                            {{ dateToBrazil($bolao->data_finalizar) }}
                        </td>
                        <td>
                            @if($bolao->situacao === 'finalizado')
                                <span class="label label-info">Finalizado</span>
                            @else
                                <span class="label label-danger">Apostas encerradas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <b>Nenhum bolão encontrado</b>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
