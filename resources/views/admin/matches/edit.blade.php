@extends('layouts.admin')

@section('content')
    <div class="block">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Editar partida</h2>
        </div>
        {!! Form::model($match, ['route' => ['admin.matches.update', 'id' => $match->id], 'class' => 'form-horizontal form-bordered']) !!}

        @include('admin.matches._form')

        {!! Form::close() !!}
    </div>
@endsection
