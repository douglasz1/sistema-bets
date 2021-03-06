@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'technical.cashier.index' ? 'active' : '' }}" href="{{ route('technical.cashier.index') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'technical.bolao.acompanhamento.index' ? 'active' : '' }}" href="{{ route('technical.bolao.acompanhamento.index') }}">
            Bolão
        </a>
    </div>
    <technical-reports-cashier></technical-reports-cashier>
@endsection
