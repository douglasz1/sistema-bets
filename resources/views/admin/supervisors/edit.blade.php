@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Editar supervisor</h2>
        </div>
        {!! Form::model($supervisor, ['route' => ['admin.supervisors.update', 'id' => $supervisor->id], 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('admin.supervisors._form')

        {!! Form::close() !!}
    </div>
@endsection
