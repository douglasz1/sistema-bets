<div class="form-group">
    {!! Form::label('name', 'Nome da liga', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('order', 'Ordenação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::number('order', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('flag', 'Bandeira', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::file('flag', ["class" => "form-control", "accept" => "image/*"]) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label">Bandeira atual</label>
    <div class="col-md-5">
        <img src="/storage/{{ $league->flag }}">
    </div>
</div>

<div class="btn-group btn-group-full btn-group-space p-lr p-tb">
    <a href="{{ route('admin.leagues.index') }}" class="btn btn-danger">
        <i class="fa fa-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Salvar
    </button>
</div>
