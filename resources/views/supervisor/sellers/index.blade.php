@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-tb">
        <a class="btn btn-primary {{ Route::currentRouteName() === 'supervisor.sellers.index' ? 'active' : '' }}" href="{{ route('supervisor.sellers.index') }}">
            Operadores
        </a>
        <a class="btn btn-primary {{ Route::currentRouteName() === 'supervisor.managers.index' ? 'active' : '' }}" href="{{ route('supervisor.managers.index') }}">
            Gerentes
        </a>
    </div>
    <supervisor-sellers-list></supervisor-sellers-list>
@endsection
