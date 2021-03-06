@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Criar supervisor</h2>
        </div>
        {!! Form::model($supervisor, ['route' => 'admin.supervisors.store', 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.supervisors._form')

        {!! Form::close() !!}
    </div>
@endsection
