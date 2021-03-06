@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => 'technical.sellers.store']) !!}

    @include('technical.sellers._form')

    {!! Form::close() !!}
@endsection
