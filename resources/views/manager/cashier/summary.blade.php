@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'manager.cashier.summary' ? 'active' : '' }}" href="{{ route('manager.cashier.summary') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'manager.bolao.acompanhamento.pessoal' ? 'active' : '' }}" href="{{ route('manager.bolao.acompanhamento.pessoal') }}">
            Bolão
        </a>
    </div>
    <manager-cashier-summary></manager-cashier-summary>
@endsection
