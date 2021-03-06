@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => ['admin.sellers.update', 'id' => $seller->id]]) !!}

    @include('admin.sellers._form')

    {!! Form::close() !!}
@endsection
