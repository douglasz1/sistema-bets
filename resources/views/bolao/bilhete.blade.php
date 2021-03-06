@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-flat m-t">
                    <div class="panel-heading">
                        <h3 class="text-center">
                            N&deg; {{ "{$aposta->bolao->id} - {$aposta->bolao->nome}" }}
                        </h3>
                    </div>
                    <div class="row2 pt9">
                        <div class="col-xs-5">Código:</div>
                        <div class="col-xs-7">
                            {{ $aposta->id }}
                        </div>
                        <div class="col-xs-5">Apostador:</div>
                        <div class="col-xs-7">
                            {{ $aposta->cliente }}
                        </div>
                        @if($aposta->bolao->imprimir_apurado)
                        <div class="col-xs-5">Arrecadado:</div>
                        <div class="col-xs-7">R$ {{ $aposta->bolao->acumulado }} (Até o momento)</div>
                        @endif
                        <div class="col-xs-5">Prêmio 1º lugar:</div>
                        <div class="col-xs-7">
                            R$ {{ moneyBR(($aposta->bolao->premio_1 / 100) * $aposta->bolao->acumulado) }} (Até o momento)
                        </div>
                        <div class="col-xs-5">Prêmio 2º lugar:</div>
                        <div class="col-xs-7">
                            R$ {{ moneyBR(($aposta->bolao->premio_2 / 100) * $aposta->bolao->acumulado) }} (Até o momento)
                        </div>
                        <div class="col-xs-5">Data finalização:</div>
                        <div class="col-xs-7">{{ dateToBrazil($aposta->bolao->data_finalizar) }}</div>
                        <div class="col-xs-5">Aposte até:</div>
                        <div class="col-xs-7">{{ dateToBrazil($aposta->bolao->data_limite) }}</div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="col-xs-1">Ord.</th>
                            <th colspan="3" class="tc">Seus Palpites</th>
                            @if($aposta->bolao->tipo === 'completo')
                            <th>Pe</th>
                            @endif
                            <th>Pq</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($aposta->palpites as $palpite)
                        <tr class="bbda">
                            <td class="col-xs-1 bbda">
                                j{{ $loop->index + 1 }}
                            </td>
                            <td class="bbda tr line-h pt9 padr2">
                                {{ $aposta->bolao->partidas[$loop->index]->time_casa }}
                            </td>
                            <td class="bbda tc">
                                {{ "({$palpite->palpite_casa}x{$palpite->palpite_fora})" }}
                            </td>
                            <td class="bbda line-h pt9  padl2">
                                {{ $aposta->bolao->partidas[$loop->index]->time_fora }}
                            </td>
                            @if($aposta->bolao->tipo === 'completo')
                            <td class="pad2 pt9">20</td>
                            @endif
                            <td class="pt9">10</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="pt9">
                                <strong>Dica!</strong> Marque sua pontuação nas colunas laterais: <br>
                                Pe = Placar Exato (Pontuação Maior)<br>
                                Pq = Placar Quebrado (Pontuação Menor)
                                <br>
                                <br>
                                Horário da Aposta: {{ dateToBrazil($aposta->created_at) }} <br>
                                Vendedor: {{ $aposta->vendedor->name }} <br>
                                Valor: R$ {{ moneyBR($aposta->valor) }} <br>
                                <br>
                                Confira o regulamento e o resultado do bolão no site
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-group btn-group-full btn-group-space p-b">
                    <a href="{{ route('bolao.jogar', ['id' => $aposta->bolao->id]) }}" class="btn btn-info btn-print">
                        <i class="fa fa-folder-open"></i> Nova aposta
                    </a>

                    <a href="{{ url('/home') }}" class="btn btn-danger btn-print">
                        <i class="fa fa-home"></i> Início
                    </a>

                    <a href="javascript:void(0)" class="btn btn-primary btn-print hidden-sm hidden-xs" onclick="window.print();return false">
                        <i class="fa fa-print"></i>
                        Imprimir ou gerar PDF
                    </a>

                    <a href="javascript:void(0)" onclick="printBet({{ $aposta->id }})" class="btn btn-primary hidden-md hidden-lg btn-print">
                        <i class="fa fa-print"></i> Imprimir
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style type="text/css">
        .col-xs-12 {
            padding-right: 5px;
            padding-left: 5px;
        }

        .btn {
            margin-bottom: 20px;
        }

        .btn:nth-child(3),
        .btn:nth-child(4) {
            margin-right: 0 !important;
        }

        @media print {
            h3 {
                margin-top: 0;
            }

            .m-t {
                margin: 0 !important;
            }

            @page {
                size: portrait;
            }

            .btn-print {
                display: none !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <script type="text/javascript">
        function printBet(id) {
            try {
                return android.buscarBilheteBolao(id + "")
            } catch (err) {}
        }
    </script>
@endsection
