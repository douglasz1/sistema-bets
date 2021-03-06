@extends('layouts.admin')

@section('content')
    <div class="btn-group btn-group-full p-b">
        <a class="btn btn-primary active" href="{{ route('admin.cancelar-palpites.index') }}">
            Cancelar palpites
        </a>
        <a class="btn btn-primary" href="{{ route('admin.cancelar-palpites.cancelados') }}">
            Palpites cancelados
        </a>
    </div>
    <admin-cancelar-palpites />
@endsection
