@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Editar liga</h2>
        </div>
        {!! Form::model($league, ['route' => ['technical.leagues.update', 'id' => $league->id], 'class' => 'form-horizontal form-bordered text-light', 'files' => true]) !!}

        @include('technical.leagues._form')

        {!! Form::close() !!}
    </div>
@endsection
