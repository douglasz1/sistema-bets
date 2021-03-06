@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.cashier.index' ? 'active' : '' }}" href="{{ route('admin.cashier.index') }}">
            Jogos
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'admin.bolao.acompanhamento.index' ? 'active' : '' }}" href="{{ route('admin.bolao.acompanhamento.index') }}">
            Bolão
        </a>
    </div>
    <admin-cashier-monitoring></admin-cashier-monitoring>
@endsection
