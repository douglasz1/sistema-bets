@extends('layouts.admin')

@section('content')
    <div class="block">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Editar partida</h2>
        </div>
        {!! Form::model($result, ['route' => ['technical.results.update', 'id' => $result->id], 'class' => 'form-horizontal form-bordered']) !!}

        @include('technical.results._form')

        {!! Form::close() !!}
    </div>
@endsection
