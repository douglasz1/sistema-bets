@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Editar técnicos</h2>
        </div>
        {!! Form::model($technical, ['route' => ['admin.technical.update', 'id' => $technical->id], 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.technical._form')

        {!! Form::close() !!}
    </div>
@endsection
