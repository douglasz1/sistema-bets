@extends('layouts.admin')

@section('content')
    {!! Form::model($seller, ['route' => 'admin.sellers.store']) !!}

    @include('admin.sellers._form')

    {!! Form::close() !!}
@endsection
