@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2><i class="fa fa-bars"></i> Editar categoria de cotação</h2>
        </div>
        {!! Form::model($quotationCategory, ['route' => ['admin.quotations.categories.update', 'id' => $quotationCategory->id], 'class' => 'form-horizontal form-bordered']) !!}

        @include('admin.quotations.categories._form')

        {!! Form::close() !!}
    </div>
@endsection
