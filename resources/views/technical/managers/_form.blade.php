<div class="block full bordered">
    <div class="block-title">
        <h2>Dados gerente</h2>
    </div>
    <div class="block-content form-horizontal form-bordered p-b p-lr text-light">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                    {!! Form::select('company_id', $companies, null, ['class' => 'form-control select-chosen']) !!}
                    @if ($errors->has('company_id'))
                        <div class="help-block animation-slideUp">{{ $errors->first('company_id') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nome gerente']) !!}
                    </div>
                    @if ($errors->has('name'))
                        <div class="help-block animation-slideUp">{{ $errors->first('name') }}</div>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                        {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Login']) !!}
                    </div>
                    @if ($errors->has('username'))
                        <div class="help-block animation-slideUp">{{ $errors->first('username') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Senha']) !!}
                    </div>
                    @if ($errors->has('password'))
                        <div class="help-block animation-slideUp">{{ $errors->first('password') }}</div>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirmar de senha']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-group btn-group-full btn-group-space p-t">
            <a href="{{ route('technical.managers.index') }}" class="btn btn-danger">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>

<div class="block full bordered">
    <div class="block-title">
        <h2>Dados palpite</h2>
    </div>
    <div class="block-content form-horizontal form-bordered p-b p-lr text-light">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('quotation_modifier') ? ' has-error' : '' }}">
                    {!! Form::label('quotation_modifier', 'Alterar cotações', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        {!! Form::number('quotation_modifier', null, ['class' => 'form-control', 'min' => -100, 'max' => 100, 'step' => 0.5]) !!}
                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('daily_limit') ? ' has-error' : '' }}">
                    {!! Form::label('daily_limit', 'Limite diário 1 palpite', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                        {!! Form::number('daily_limit', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('limit') ? ' has-error' : '' }}">
                    {!! Form::label('limit', 'Limite', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                        {!! Form::number('limit', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('balance') ? ' has-error' : '' }}">
                    {!! Form::label('balance', 'Saldo', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                        {!! Form::number('balance', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('max_prize_multiplier') ? ' has-error' : '' }}">
                    {!! Form::label('max_prize_multiplier', 'Múltiplicador', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-times"></i></span>
                        {!! Form::number('max_prize_multiplier', null, ['class' => 'form-control', 'min' => 1]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('max_prize') ? ' has-error' : '' }}">
                    {!! Form::label('max_prize', 'Prêmio máximo', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                        {!! Form::number('max_prize', null, ['class' => 'form-control', 'min' => 1]) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('profit_percentage') ? ' has-error' : '' }}">
                    {!! Form::label('profit_percentage', 'Comissão vendedor', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        {!! Form::number('profit_percentage', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5, 'max' => 100]) !!}
                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('manager_commission') ? ' has-error' : '' }}">
                    {!! Form::label('manager_commission', 'Comissão do gerente', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        {!! Form::number('manager_commission', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5, 'max' => 100]) !!}
                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('tips_min') ? ' has-error' : '' }}">
                    {!! Form::label('tips_min', 'Mínimo de jogos', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        {!! Form::number('tips_min', null, ['class' => 'form-control', 'min' => 1]) !!}
                        <span class="input-group-addon">aposta(s)</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group{{ $errors->has('tips_max') ? ' has-error' : '' }}">
                    {!! Form::label('tips_min', 'Máximo de jogos', ["class" => "control-label"]) !!}
                    <div class="input-group">
                        {!! Form::number('tips_max', null, ['class' => 'form-control', 'min' => 1]) !!}
                        <span class="input-group-addon">aposta(s)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group{{ $errors->has('one_tip_quotation_min') ? ' has-error' : '' }}">
                    {!! Form::label('one_tip_quotation_min', 'Cotação mínima para palpites', ["class" => "control-label"]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">1</span>
                        {!! Form::number('one_tip_quotation_min', null, ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => 0.01]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">2</span>
                        {!! Form::number('two_tip_quotation_min', null, ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => 0.01]) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">3</span>
                        {!! Form::number('three_tip_quotation_min', null, ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => 0.01]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block full bordered">
    <div class="block-title">
        <h2>Dados comissão</h2>
    </div>
    <div class="block-content form-horizontal form-bordered p-b p-lr text-light">
        @if(config('app.aoVivoDisponivel'))
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('ao_vivo') ? ' has-error' : '' }}">
                        {!! Form::label('ao_vivo', 'Ao vivo disponível', ["class" => "control-label"]) !!}
                        {!! Form::select('ao_vivo', [true => 'Ativo', false => 'Inativo'], null, ['class' => 'form-control']) !!}
                        @if ($errors->has('ao_vivo'))
                            <div class="help-block animation-slideUp">{{ $errors->first('ao_vivo') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('comissao_ao_vivo') ? ' has-error' : '' }}">
                        {!! Form::label('comissao_ao_vivo', 'Comissão para ao vivo', ["class" => "control-label"]) !!}
                        <div class="input-group">
                            {!! Form::number('comissao_ao_vivo', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                        </div>
                        @if ($errors->has('comissao_ao_vivo'))
                            <div class="help-block animation-slideUp">{{ $errors->first('comissao_ao_vivo') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="form-group{{ $errors->has('commission1') ? ' has-error' : '' }}">
            {!! Form::label('commission1', 'Comissão para 1 jogo', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission1', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min1', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max1', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('commission2') ? ' has-error' : '' }}">
            {!! Form::label('commission2', 'Comissão para 2 jogos', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission2', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min2', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max2', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('commission3') ? ' has-error' : '' }}">
            {!! Form::label('commission3', 'Comissão de 3 a 5 jogos', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission3', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min3', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max3', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('commission6') ? ' has-error' : '' }}">
            {!! Form::label('commission6', 'Comissão de 6 a 10 jogos', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission6', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min6', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max6', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('commission11') ? ' has-error' : '' }}">
            {!! Form::label('commission11', 'Comissão de 11 a 15 jogos', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission11', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min11', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max11', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('commission16') ? ' has-error' : '' }}">
            {!! Form::label('commission16', 'Comissão para 16+ jogos', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-2">
                <div class="input-group">
                    {!! Form::number('commission16', null, ['class' => 'form-control', 'min' => 0, 'step' => 0.5]) !!}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_min16', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">mínimo</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    {!! Form::number('value_max16', null, ['class' => 'form-control', 'min' => 1]) !!}
                    <span class="input-group-addon">máximo</span>
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
            {!! Form::label('comments', 'Descrição', ["class" => "col-md-3 control-label"]) !!}
            <div class="col-md-8">
                {!! Form::textarea('comments', null, ['class' => 'form-control', 'rows' => 5]) !!}
            </div>
            @if ($errors->has('comments'))
                <div class="help-block animation-slideUp">{{ $errors->first('comments') }}</div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.1/tinymce.min.js" integrity="sha256-DKYEi5Z8g2il/Yheajwj2YOzkwRw4at1jGldGh5dFxI=" crossorigin="anonymous"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            menubar: false,
            plugins: [
                'lists charmap',
                'paste'
            ],
            toolbar: 'undo redo | bold italic | bullist numlist outdent indent | removeformat'
        });
    </script>
@endsection
