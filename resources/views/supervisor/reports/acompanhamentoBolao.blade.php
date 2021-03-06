@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'supervisor.cashier.index' ? 'active' : '' }}" href="{{ route('supervisor.cashier.index') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'supervisor.bolao.acompanhamento.index' ? 'active' : '' }}" href="{{ route('supervisor.bolao.acompanhamento.index') }}">
            Bolão
        </a>
    </div>
    <sp-acompanhamento-bolao></sp-acompanhamento-bolao>
@endsection
