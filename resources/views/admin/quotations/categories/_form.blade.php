<div class="form-group">
    {!! Form::label('mercado_descricao', 'Mercado', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <span class="form-control">{{ $quotationCategory->mercado_descricao }}</span>
    </div>
</div>

<div class="form-group">
    {!! Form::label('palpite_descricao', 'Palpite', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <span class="form-control">{{ $quotationCategory->palpite_descricao }}</span>
    </div>
</div>

<div class="form-group{{ $errors->has('alterar_cotacao') ? ' has-error' : '' }}">
    {!! Form::label('alterar_cotacao', 'Alterar valor da cotação', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::number('alterar_cotacao', null, ['class' => 'form-control', 'min' => -100, 'max' => 100, 'step' => 0.5]) !!}
            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
        </div>
    </div>
</div>

<div class="form-group form-actions">
    <div class="col-md-9 col-md-offset-3">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Salvar
        </button>
        <a href="{{ route('admin.quotations.categories.index') }}" class="btn btn-danger">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>
