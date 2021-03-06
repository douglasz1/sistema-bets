@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => ['supervisor.managers.update', 'id' => $manager->id]]) !!}

    @include('supervisor.managers._form')

    {!! Form::close() !!}
@endsection
