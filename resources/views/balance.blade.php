@extends('layouts.admin')

@section('content')
    <div class="block">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Meu saldo</h2>
        </div>
    </div>
    <div class="row row-reduced-margin">
        <div class="col-sm-6 col-lg-6 col-xs-6">
            <div class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background-warning">
                        <i class="fa fa-money text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h4 text-warning">
                        <strong>
                            R$ {{ moneyBR(auth()->user()->limit) }}
                        </strong>
                    </h2>
                    <span class="text-muted">LIMITE PARA APOSTAS</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6 col-xs-6">
            <div class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background-info">
                        <i class="fa fa-usd text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h4 text-info">
                        <strong>
                            R$ {{ moneyBR(auth()->user()->balance) }}
                        </strong>
                    </h2>
                    <span class="text-muted">SALDO DISPONÍVEL</span>
                </div>
            </div>
        </div>
    </div>
@endsection