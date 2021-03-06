<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Nome da empresa', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
    </div>
    @if ($errors->has('name'))
        <div class="help-block animation-slideUp">{{ $errors->first('name') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('print_name') ? ' has-error' : '' }}">
    {!! Form::label('print_name', 'Nome na impressão', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::text('print_name', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('print_name'))
        <div class="help-block animation-slideUp">{{ $errors->first('print_name') }}</div>
    @endif
</div>

<div class="form-group{{ $errors->has('quotation_modifier') ? ' has-error' : '' }}">
    {!! Form::label('quotation_modifier', 'Alterar cotações', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::number('quotation_modifier', null, ['class' => 'form-control', 'min' => -100, 'max' => 100, 'step' => 0.5, 'required']) !!}
            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('max_prize') ? ' has-error' : '' }}">
    {!! Form::label('max_prize', 'Prêmio máximo', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
            {!! Form::number('max_prize', null, ['class' => 'form-control', 'min' => 1, 'required']) !!}
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('max_prize_multiplier') ? ' has-error' : '' }}">
    {!! Form::label('max_prize_multiplier', 'Múltiplicador de prêmio máximo', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-times"></i></span>
            {!! Form::number('max_prize_multiplier', null, ['class' => 'form-control', 'min' => 1, 'required']) !!}
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('bets_limit') ? ' has-error' : '' }}">
    {!! Form::label('bets_limit', 'Limite de apostas por palpite', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
            {!! Form::number('bets_limit', null, ['class' => 'form-control', 'min' => 0, 'required']) !!}
        </div>
    </div>
</div>

<div class="btn-group btn-group-full btn-group-space p-lr p-tb">
    <a href="{{ route('admin.companies.index') }}" class="btn btn-danger">
        <i class="fa fa-arrow-left"></i> Voltar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i> Salvar
    </button>
</div>
