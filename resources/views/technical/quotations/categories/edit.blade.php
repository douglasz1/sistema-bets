@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2> Editar categoria de cotação</h2>
        </div>
        {!! Form::model($quotationCategory, ['route' => ['technical.quotations.categories.update', 'id' => $quotationCategory->id], 'class' => 'form-horizontal form-bordered text-light']) !!}

        @include('technical.quotations.categories._form')

        {!! Form::close() !!}
    </div>
@endsection
