<div class="form-group">
    <label class="col-md-3 control-label">Nome da partida</label>
    <div class="col-md-5">
        <span class="form-control">
            {{ $result ? $result->matchName() : '' }}
        </span>
    </div>
</div>

<div class="form-group{{ $errors->has('home_1st') ? ' has-error' : '' }}">
    {!! Form::label('home_1st', 'Placar 1° tempo', ['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::text('home_1st', null, ['class' => 'form-control']) !!}
            <span class="input-group-addon"><i class="fa fa-times"></i></span>
            {!! Form::text('away_1st', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    @if ($errors->has('home_1st'))
        <div class="help-block animation-slideUp">{{ $errors->first('home_1st') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('home_2nd') ? ' has-error' : '' }}">
    {!! Form::label('home_2nd', 'Placar 2° tempo', ['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::text('home_2nd', null, ['class' => 'form-control']) !!}
            <span class="input-group-addon"><i class="fa fa-times"></i></span>
            {!! Form::text('away_2nd', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    @if ($errors->has('home_2nd'))
        <div class="help-block animation-slideUp">{{ $errors->first('home_2nd') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('home_final') ? ' has-error' : '' }}">
    {!! Form::label('home_final', 'Placar final', ['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::text('home_final', null, ['class' => 'form-control']) !!}
            <span class="input-group-addon"><i class="fa fa-times"></i></span>
            {!! Form::text('away_final', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    @if ($errors->has('home_final'))
        <div class="help-block animation-slideUp">{{ $errors->first('home_final') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {!! Form::label('status', 'Situação', ['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-5">
        {!! Form::select('status', ['pending' => 'Em andamento', 'finished' => 'Terminado'], null, ['class' => 'form-control', 'required', 'min' => 0]) !!}
    </div>
    @if ($errors->has('status'))
        <div class="help-block animation-slideUp">{{ $errors->first('status') }}</div>
    @endif
</div>

<div class="form-group form-actions">
    <div class="col-md-9 col-md-offset-3">
        <button type="submit" class="btn btn-effect-ripple btn-primary"
                style="overflow: hidden; position: relative;">
            <i class="fa fa-save"></i> Salvar
        </button>
        <a href="{{ route('admin.results.index') }}" class="btn btn-effect-ripple btn-danger" style="overflow: hidden; position: relative;">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>