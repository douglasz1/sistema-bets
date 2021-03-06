<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Nome do supervisor', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('name'))
        <div class="help-block animation-slideUp">{{ $errors->first('name') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
    {!! Form::label('username', 'Login', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('username', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('username'))
        <div class="help-block animation-slideUp">{{ $errors->first('username') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    {!! Form::label('password', 'Senha de acesso', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::password('password', ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('password'))
        <div class="help-block animation-slideUp">{{ $errors->first('password') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
    {!! Form::label('password_confirmation', 'Confirmação de senha', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="btn-group btn-group-full btn-group-space p-lr p-tb">
    <a href="{{ route('admin.supervisors.index') }}" class="btn btn-danger">
        <i class="fa fa-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Salvar
    </button>
</div>
