@extends('layouts.admin')

@section('content')
    {!! Form::model($manager, ['route' => 'admin.managers.store']) !!}

    @include('admin.managers._form')

    {!! Form::close() !!}
@endsection
