@extends('layouts.admin')

@section('content')
    <div class="block full">
        <div class="block-title">
            <div class="block-options pull-right">
                <button id="btn-edit-bet" type="button" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-original-title="Editar">
                    <i class="fa fa-edit"></i> Editar
                </button>
            </div>
            <h2><i class="fa fa-bars"></i> Resumo da aposta</h2>
        </div>
        <div class="row">
        {!! Form::model($bet, ['route' => ['admin.bets.update', $bet->id], 'class' => 'form-inline form-bet']) !!}
            <div class="col-md-6">
                <h4>Código: <span class="text-muted">{{ $bet->id }}</span></h4>
                <h4>Operador: <span class="text-muted">{{ $bet->seller->name }}</span></h4>
                <h4>Apostador:
                    <span class="text-muted hide-on-edit">{{ $bet->client_name }}</span>
                    {!! Form::text('client_name', null, ['class' => 'form-control']) !!}
                </h4>
                <h4>Situação:
                    <span class="text-muted hide-on-edit">
                        @if($bet->status === 'win')
                            <span class="text-success">Vencedor</span>
                        @elseif($bet->status === 'lose')
                            <span class="text-danger">Perdedor</span>
                        @elseif($bet->status === 'canceled')
                            <span class="text-warning">Cancelado</span>
                        @else
                            <span>Andamento</span>
                        @endif
                    </span>
                    {!! Form::select('status', ['win' => 'Vencedor', 'lose' => 'Perdedor', 'canceled' => 'Cancelado', 'pending' => 'Andamento'], null, ['class' => 'form-control']) !!}
                </h4>
            </div>
            <div class="col-md-6">
                <h4>Data:
                    <span class="text-muted hide-on-edit">{{ $bet->created_at->format('d/m/Y H:i') }}</span>
                    {!! Form::text('created_at', null, ['class' => 'form-control', 'id' => 'datetimepicker', 'data-date-format' => 'yyyy-mm-dd hh:ii:ss', 'readonly']) !!}
                </h4>
                <h4>Valor:
                    <span class="text-muted">R$ {{ moneyBR($bet->bet_value) }}</span>
                </h4>
                <h4>Prêmio:
                    <span class="text-muted hide-on-edit">R$ {{ moneyBR($bet->prize) }}</span>
                    {!! Form::text('prize', null, ['class' => 'form-control']) !!}
                </h4>
                <h4>Comissão:
                    <span class="text-muted">R$ {{ moneyBR($bet->commission) }}</span>
                </h4>
                @if($bet->status !== 'canceled')
                    <a href="{{ route('admin.bets.cancel', ['id' => $bet->id]) }}"
                       class="btn btn-danger btn-effect-ripple">
                        <i class="fa fa-ban"></i>
                        Cancelar aposta
                    </a>
                @endif
                <button type="submit" class="btn btn-success btn-effect-ripple">
                    <i class="fa fa-save"></i> Salvar
                </button>
            </div>
        {!! Form::close() !!}
        </div>
    </div>
    <div class="block full block-with-table">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Palpites da aposta</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-condensed table-hover">
                <thead>
                <tr>
                    <td><b>Partida</b></td>
                    <td><b>Liga</b></td>
                    <td><b>Data</b></td>
                    <td><b>Palpite</b></td>
                    <td><b>Cota</b></td>
                    <td><b>Situação</b></td>
                    <td><b>1&deg; T</b></td>
                    <td><b>2&deg; T</b></td>
                    <td><b>R. Final</b></td>
                    <td><b>Opções</b></td>
                </tr>
                </thead>
                <tbody>
                @forelse($bet->tips as $tip)
                    <tr>
                        <td>
                            <strong>{{ $tip->match->matchName() ?? $tip->match_id }}</strong>
                        </td>
                        <td>
                            {{ $tip->match->league->name ?? '' }}
                        </td>
                        <td>
                            {{ $tip->match->match_date->format('d/m/Y H:i') ?? '' }}
                        </td>
                        <td>{{ $tip->choice_name }}</td>
                        <td>{{ moneyBR($tip->value) }}</td>
                        <td class="status">
                            @if($tip->status === 'win')
                                <span class="text-success">Vencedor</span>
                            @elseif($tip->status === 'lose')
                                <span class="text-danger">Perdedor</span>
                            @elseif($tip->status === 'canceled')
                                <span class="text-warning">Cancelado</span>
                            @else
                                <span>Andamento</span>
                            @endif
                            {!! Form::model($tip, ['route' => ['admin.tips.update', 'id' => $tip->id], 'class' => 'form-inline']) !!}
                                {!! Form::select('status', ['win' => 'Vencedor', 'lose' => 'Perdedor', 'canceled' => 'Cancelado', 'pending' => 'Andamento'], null, ['class' => 'form-control']) !!}
                                <button type="submit" class="btn-effect-ripple btn-success btn btn-sm">
                                    <i class="fa fa-save"></i>
                                </button>
                            {!! Form::close() !!}
                        </td>
                        <td>{{ @$tip->match->home_1st . "-" . @$tip->match->away_1st }}</td>
                        <td>{{ @$tip->match->home_2nd . "-" . @$tip->match->away_2nd }}</td>
                        <td>{{ @$tip->match->home_final . "-" . @$tip->match->away_final }}</td>
                        <td>
                            <button class="btn btn-sm btn-default btn-effect-ripple btn-edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            @if($tip->status !== 'canceled')
                                <a href="{{ route('admin.tips.cancel', ['id' => $tip->id]) }}"
                                   class="btn btn-danger btn-sm btn-effect-ripple">
                                    <i class="fa fa-ban"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">Nenhuma aposta encontrada</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/js/bootstrap-datetimepicker.min.js" integrity="sha256-KWLvsoTXFF8o3o9zKOjUsYC/NPKjgYmUXbrxNk90F8k=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/js/locales/bootstrap-datetimepicker.pt-BR.js" integrity="sha256-Z8X5Ww6HOCU2fSuQ/RJ0I23PAe2pd5h8ezCJ714EH/Q=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(function() {
            $('.table .btn-edit').on('click', function () {
                $(this).parent().parent().find('.status').toggleClass('editing');
            });

            $('#btn-edit-bet').on('click', function (e) {
                e.preventDefault();
                $('.form-bet').toggleClass('bet-editing');
            });

            $('#datetimepicker').datetimepicker({
                language: 'pt-BR',
                todayHighlight: true,
                todayBtn: true
            });
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/css/bootstrap-datetimepicker.min.css" integrity="sha256-ff4Vuur4aYrm0ZOAEC/me1LBOcid7PJ5oP9xxvJ0AKQ=" crossorigin="anonymous" />
    <style type="text/css">
        .status form,
        .editing span,
        .form-bet:not(.bet-editing) input,
        .form-bet:not(.bet-editing) select,
        .form-bet:not(.bet-editing) button[type="submit"] {
            display: none;
        }

        .editing {
            width: 180px;
        }

        .editing form {
            display: block;
        }

        .bet-editing a.btn, .bet-editing .hide-on-edit {
            display: none;
        }

        .block-with-table tbody > tr > td:last-child {
            min-width: 86px;
        }
    </style>
@endsection