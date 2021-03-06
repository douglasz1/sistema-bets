<!DOCTYPE html>
<html lang="br">
<head>
    <title>Bilhete</title>
    <meta charset="utf-8">
    <style type="text/css">
        html, body {
            background-color: #f8ecc2;
            margin: 0;
            padding: 2mm;
            font-weight: 600;
        }

        #bilhete {
            padding-bottom: 5mm;
        }

        body {
            font-family: monospace;
        }

        @page {
            margin: 2mm;
        }

        .hr {
            margin-top: 5mm;
            margin-bottom: 0;
            border-top: 2px dashed #000;
        }

        p {
            margin: 0 0 5px 0;
        }
    </style>
</head>
<body>
<div id="bilhete">
    Data..: {{ $bet->created_at->format('d/m H:i') }}
    <br>
    Login.: {{ $bet->seller->username }}
    <br>
    Nome..: {{ $bet->client_name }}
    <br>
    Codigo: {{ $bet->id }}
    <br>
    <br>
    Seus palpites:
    <br>
    <br>
    @foreach($bet->tips as $tip)
        <div class="aposta">
            {{ $tip->match->league->country->name }}:
            {{ $tip->match->league->name }}
            <br>
            {{ $tip->match->matchName() }}
            <br>
            {{ $tip->match->match_date->format('d/m H:i') }} -
            {{ $tip->choice_name }} - {{ $tip->value }}
            <br>
            <div class="hr">&nbsp;</div>
        </div>
    @endforeach
    <p>Quantidade de Jogos: {{ $bet->tips_quantity }}</p>
    <p>Total Apostado: R$ {{ moneyBR($bet->bet_value) }}</p>
    <p>Possível Retorno: R$ {{ moneyBR($bet->prize) }}</p>
    @if($bet->seller->percentual_premio > 0)
        <p>Cambista Paga: R$ {{ moneyBR($bet->prize - ($bet->seller->percentual_premio / 100 * $bet->prize)) }}</p>
    @endif
    <p>Eu li e concordo com as regras e termos da {{ config('app.name') }}</p>
</div>
</body>
</html>
