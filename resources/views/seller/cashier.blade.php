@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'seller.cashier.index' ? 'active' : '' }}" href="{{ route('seller.cashier.index') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'seller.bolao.acompanhamento.index' ? 'active' : '' }}" href="{{ route('seller.bolao.acompanhamento.index') }}">
            Bolão
        </a>
    </div>
    <seller-cashier></seller-cashier>
@endsection
