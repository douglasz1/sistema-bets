<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Nome da liga', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('name'))
        <div class="help-block animation-slideUp">{{ $errors->first('name') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
    {!! Form::label('name', 'País', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::select('country_id', $countries, null, ['class' => 'form-control select-chosen']) !!}
    </div>
    @if ($errors->has('country_id'))
        <div class="help-block animation-slideUp">{{ $errors->first('country_id') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('order') ? ' has-error' : '' }}">
    {!! Form::label('order', 'Ordenação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::number('order', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('order'))
        <div class="help-block animation-slideUp">{{ $errors->first('order') }}</div>
    @endif
</div>

<div class="form-group form-actions">
    <div class="col-md-9 col-md-offset-3">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Salvar
        </button>
        <a href="{{ route('admin.leagues.index') }}" class="btn btn-danger">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>
