<!DOCTYPE html>
<html lang="br">
<head>
    <title>Bilhete</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body {
            color: #000 !important;
            background-color: #fff;
            margin: 0;
            font-family: monospace;
            font-size: 11px;
            font-weight: normal !important;
        }

        hr {
            margin: 1mm 0;
            border-top: 1px dashed #000;
        }

        @page {
            size: 88mm 134mm;
            margin: 1mm 2mm 10mm;
            font-size: 10px;
        }

        .aposta table td:nth-child(1) {
            width: 250px;
        }

        .aposta table td:nth-child(2) {
            padding-left: 20px;
        }

        @media print {
            @page { margin: 0; }

            body { margin: 1.6mm; }

            .aposta table td:nth-child(1) {
                width: auto !important;
            }

            .aposta table td:nth-child(2) {
                display: none;
            }

            p {
                margin: 0 0 1mm 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
<div id="bilhete">
    Data..: {{ $bet->created_at->format('d/m H:i') }}
    <br>
    Login.: {{ $bet->seller->username }}
    <br>
    Nome..: {{ $bet->client_name }}
    <br>
    Codigo: {{ $bet->id }}
    <br>
    @if($bet->status === 'win')
        Situacao: Vencedor
    @elseif($bet->status === 'lose')
        Situacao: Perdedor
    @elseif($bet->status === 'canceled')
        Situacao: Cancelada
    @endif
    <br>
    <br>
    Seus palpites:
    <br>
    @foreach($bet->tips as $tip)
        <div class="aposta">
            <table>
                <tr>
                    <td>
                        {{ $tip->match->league->country->name }}:
                        {{ $tip->match->league->name }}
                        <br>
                        {{ $tip->match->matchName() }}
                        <br>
                        {{ $tip->match->match_date->format('d/m H:i') }}
                        <br>
                        {{ $tip->bet_name }}
                        <br>
                        {{ $tip->choice_name }} - {{ $tip->value }}
                        <br>
                        @if($tip->status === 'win')
                            Situacao: Vencedor
                        @elseif($tip->status === 'lose')
                            Situacao: Perdedor
                        @elseif($tip->status === 'canceled')
                            Situacao: Cancelada
                        @endif
                    </td>
                    <td>
                        @if($tip->match->status === 'finished')
                            1&deg; T.:
                            {{ $tip->match->home_1st }}x{{ $tip->match->away_1st }}
                            <br>
                            2&deg; T.:
                            {{ $tip->match->home_2nd }}x{{ $tip->match->away_2nd }}
                            <br>
                            Final:
                            {{ $tip->match->home_final }}x{{ $tip->match->away_final }}
                        @endif
                    </td>
                </tr>
            </table>
            ----------------------------
            <br>
        </div>
    @endforeach
    <p>Quantidade de Jogos: {{ $bet->tips_quantity }}</p>
    <p>Total Apostado: R$ {{ moneyBR($bet->bet_value) }}</p>
    <p>Possível Retorno: R$ {{ moneyBR($bet->prize) }}</p>
    @if($bet->seller->percentual_premio > 0)
        <p>Cambista Paga: R$ {{ moneyBR($bet->prize - ($bet->seller->percentual_premio / 100 * $bet->prize)) }}</p>
    @endif
    <p><b>Eu li e concordo com as regras e termos da {{ config('app.name') }}</b></p>
</div>
</body>
</html>
