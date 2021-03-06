@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => ['technical.managers.update', 'id' => $manager->id]]) !!}

    @include('technical.managers._form')

    {!! Form::close() !!}
@endsection
