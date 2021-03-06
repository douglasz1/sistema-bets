@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Bolão #{{ "{$bolao->id}: {$bolao->nome}" }} </h2>
        </div>
        <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
            <a class="btn btn-primary" href="{{ route('technical.bolao.detalhes', ['id' => $bolao->id]) }}">
                Detalhes
            </a>
            <a class="btn btn-info" href="{{ route('technical.bolao.placares', ['id' => $bolao->id]) }}">
                Inserir placares
            </a>
            <a class="btn btn-success" href="{{ route('technical.bolao.vencedor', ['id' => $bolao->id]) }}">
                Calcular vencedor
            </a>
        </div>
        <div class="block-title">
            <h2>Apostas: {{ $bolao->nome }}</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-vcenter table-borderless table-striped">
                <thead>
                <tr>
                    <td><b>#</b></td>
                    <td><b>Vendedor</b></td>
                    <td><b>Cliente</b></td>
                    <td><b>Jogos</b></td>
                    <td><b>Pontos</b></td>
                    <td><b>Data</b></td>
                </tr>
                </thead>
                <tbody>
                @foreach($apostas as $aposta)
                    <tr>
                        <td>#{{ $loop->index + 1 }}</td>
                        <td>{{ $aposta->vendedor->name }}</td>
                        <td>{{ $aposta->cliente }}</td>
                        <td>
                            @foreach($aposta->palpites as $palpite)
                                j{{ ($loop->index + 1) . "({$palpite->palpite_casa}x{$palpite->palpite_fora})" }}
                            @endforeach
                        </td>
                        <td>{{ $aposta->total_pontos }}</td>
                        <td>{{ dateToBrazil($aposta->created_at) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="pagination">
        {{ $apostas->links() }}
    </div>
@endsection

