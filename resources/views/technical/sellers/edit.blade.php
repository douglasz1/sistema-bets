@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => ['technical.sellers.update', 'id' => $seller->id]]) !!}

    @include('technical.sellers._form')

    {!! Form::close() !!}
@endsection
