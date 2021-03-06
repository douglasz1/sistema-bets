@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'manager.cashier.monitoring' ? 'active' : '' }}" href="{{ route('manager.cashier.monitoring') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'manager.bolao.acompanhamento.index' ? 'active' : '' }}" href="{{ route('manager.bolao.acompanhamento.index') }}">
            Bolão
        </a>
    </div>
    <manager-cashier-monitoring></manager-cashier-monitoring>
@endsection
