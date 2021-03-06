@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => ['admin.managers.update', 'id' => $manager->id]]) !!}

    @include('admin.managers._form')

    {!! Form::close() !!}
@endsection
