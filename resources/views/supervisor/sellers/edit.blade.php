@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => ['supervisor.sellers.update', 'id' => $seller->id]]) !!}

    @include('supervisor.sellers._form')

    {!! Form::close() !!}
@endsection
