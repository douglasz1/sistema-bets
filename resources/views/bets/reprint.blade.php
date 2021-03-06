@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a href="{{ route('bets.reprints') }}" class="btn btn-primary {{ Route::currentRouteName() === 'bets.reprints' ? 'active' : '' }}">Jogos</a>
        <a href="{{ route('bolao.segundaVia') }}" class="btn btn-primary {{ Route::currentRouteName() === 'bolao.segundaVia' ? 'active' : '' }}">Bolão</a>
    </div>
    <bet-reprints></bet-reprints>
@endsection
