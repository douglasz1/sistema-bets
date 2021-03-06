@extends('layouts.web')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            <h2>Regras do site</h2>
        </div>

        <div class="rules p-lr p-tb text-light">
            {!! $regras !!}
        </div>
    </div>
@endsection
