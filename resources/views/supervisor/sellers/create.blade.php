@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => 'supervisor.sellers.store']) !!}

    @include('supervisor.sellers._form')

    {!! Form::close() !!}
@endsection
