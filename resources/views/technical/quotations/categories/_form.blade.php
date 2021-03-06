<div class="form-group{{ $errors->has('label') ? ' has-error' : '' }}">
    {!! Form::label('label', 'Nome da cotação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('label', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('label'))
        <div class="help-block animation-slideUp">{{ $errors->first('label') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('quotation_modifier') ? ' has-error' : '' }}">
    {!! Form::label('quotation_modifier', 'Alterar valor da cotação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::number('quotation_modifier', null, ['class' => 'form-control', 'min' => -100, 'max' => 100, 'step' => 0.5]) !!}
            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('order') ? ' has-error' : '' }}">
    {!! Form::label('order', 'Ordenação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::number('order', null, ['class' => 'form-control', 'min' => 0]) !!}
    </div>
</div>

<div class="btn-group btn-group-full btn-group-space p-lr p-tb">
    <a href="{{ route('technical.quotations.categories.index') }}" class="btn btn-danger">
        <i class="fa fa-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Salvar
    </button>
</div>
