<!DOCTYPE html>
<html lang="pt-BR"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bilhete</title>

    @if(config('app.env') !== 'production')
        <link href="/css/app.css" rel="stylesheet">
    @else
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    @endif

    <style type="text/css">
        body {
            color: #000 !important;
            background-color: #fff;
            margin: 7px;
            font-family: monospace;
            font-size: 13px;
        }

        hr {
            margin: 1mm 0;
            border-top: 1px dashed #000;
        }

        .aposta table td:nth-child(1) {
            width: 250px;
        }

        .aposta table td:nth-child(2) {
            padding-left: 20px;
        }

        @media print {
            .aposta table td:nth-child(1) {
                width: auto !important;
            }

            .aposta table td:nth-child(2) {
                display: none;
            }
        }
    </style>
</head>
<body>
<div id="bilhete">
    Data: <b>{{ $bet->created_at->format("d/m/Y H:i") }}</b><br>
    Nome: {{ $bet->client_name }}<br>
    Código: {{ $bet->id }}<br>
    Situação :
    @if($bet->status === "win")
        Vencedor
    @elseif($bet->status === "lose")
        Perdedor
    @elseif($bet->status === "canceled")
        Cancelado
    @else
        Andamento
    @endif
    <br>
    ---------------------------------------------
    <br>
    @foreach($bet->tips as $tip)
        <div class="aposta">
            <table cellpadding="10">
                <tr>
                    <td>
                        <b>{{ $tip->match->matchName() }}</b><br>
                        Data: <b>{{ $tip->match->match_date->format("d/m/Y H:i") }}</b>
                        <br>
                        {{ $tip->bet_name }}
                        <br>
                        {{ $tip->choice_name }} -
                        <b>R$ {{ moneyBR($tip->value) }}</b>
                        <br>
                        Situação:
                        @if($tip->status === 'win')
                            Vencedor
                        @elseif($tip->status === 'lose')
                            Perdedor
                        @elseif($tip->status === 'canceled')
                            Cancelada
                        @else
                            Andamento
                        @endif
                    </td>
                    <td>
                        @if($tip->match->status === "finished")
                            1&deg; T.:
                            {{ $tip->match->home_1st }}x{{ $tip->match->away_1st }}
                            <br>
                            2&deg; T.:
                            {{ $tip->match->home_2nd }}x{{ $tip->match->away_2nd }}
                            <br>
                            Final:
                            {{ $tip->match->home_final }}x{{ $tip->match->away_final }}
                        @endif
                        <br>
                    </td>
                </tr>
            </table>
            ---------------------------------------------
            <br>
        </div>
    @endforeach
    <p>Quantidade de Jogos: {{ $bet->tips_quantity }}</p>
    <p>Total Apostado: R$ {{ moneyBR($bet->bet_value) }}</p>
    <p>Possível Retorno: R$ {{ moneyBR($bet->prize) }}</p>
    @if($bet->seller->percentual_premio > 0)
        <p>Cambista Paga: R$ {{ moneyBR($bet->prize - ($bet->seller->percentual_premio / 100 * $bet->prize)) }}</p>
    @endif
    <h4>Eu li e concordo com as regras e termos da {{ config('app.name') }}</h4>
</div>
</body>
</html>
