@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Editar liga</h2>
        </div>
        {!! Form::model($league, ['route' => ['admin.leagues.update', 'id' => $league->id], 'class' => 'form-horizontal form-bordered', 'files' => true]) !!}

        @include('admin.leagues._form')

        {!! Form::close() !!}
    </div>
@endsection
