@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => 'supervisor.managers.store']) !!}

    @include('supervisor.managers._form')

    {!! Form::close() !!}
@endsection
