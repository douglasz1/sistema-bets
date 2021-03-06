@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => 'technical.managers.store']) !!}

    @include('technical.managers._form')

    {!! Form::close() !!}
@endsection
