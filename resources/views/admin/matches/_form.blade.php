<div class="form-group">
    {!! Form::label('match_name', 'Nome da partida', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <span class="form-control">
            {{ $match->home_team ?? '' }} x {{ $match->away_team ?? '' }}
        </span>
    </div>
</div>
<div class="form-group{{ $errors->has('match_date') ? ' has-error' : '' }}">
    {!! Form::label('match_date', 'Data da partida', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        <div class="input-group">
            {!! Form::text('match_date', null, ['class' => 'form-control', 'readonly', 'data-date-format' => 'yyyy-mm-dd hh:ii:ss']) !!}
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    @if ($errors->has('match_date'))
        <div class="help-block animation-slideUp">{{ $errors->first('match_date') }}</div>
    @endif
</div>
<div class="form-group{{ $errors->has('league_id') ? ' has-error' : '' }}">
    {!! Form::label('league_id', 'Liga', ["class" => "col-md-3 control-label"]) !!}
    <div class="col-md-5">
        {!! Form::select('league_id', $leagues, null, ['class' => 'form-control select-chosen']) !!}
    </div>
    @if ($errors->has('league_id'))
        <div class="help-block animation-slideUp">{{ $errors->first('league_id') }}</div>
    @endif
</div>
<div class="form-group form-actions">
    <div class="col-md-9 col-md-offset-3">
        <button type="submit" class="btn btn-effect-ripple btn-primary"
                style="overflow: hidden; position: relative;">
            <i class="fa fa-save"></i> Salvar
        </button>
        <a href="{{ route('admin.quotations.index') }}" class="btn btn-effect-ripple btn-danger" style="overflow: hidden; position: relative;">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/js/bootstrap-datetimepicker.min.js" integrity="sha256-KWLvsoTXFF8o3o9zKOjUsYC/NPKjgYmUXbrxNk90F8k=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/js/locales/bootstrap-datetimepicker.pt-BR.js" integrity="sha256-Z8X5Ww6HOCU2fSuQ/RJ0I23PAe2pd5h8ezCJ714EH/Q=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(function() {
            $('#match_date').datetimepicker({
                language: 'pt-BR',
                todayHighlight: true,
                todayBtn: true
            });
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/css/bootstrap-datetimepicker.min.css" integrity="sha256-ff4Vuur4aYrm0ZOAEC/me1LBOcid7PJ5oP9xxvJ0AKQ=" crossorigin="anonymous" />
@endsection
