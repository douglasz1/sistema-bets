@for($i = 1; $i <= 10; $i++)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('time_casa', "#{$i}", ["class" => "col-md-1 control-label"]) !!}
                <div class="col-md-6">
                    <div class="input-group">
                        {!! Form::text('partidas[time_casa][]', null, ['class' => 'form-control', 'required', 'placeholder' => 'Time da casa']) !!}
                        <span class="input-group-addon">X</span>
                        {!! Form::text('partidas[time_fora][]', null, ['class' => 'form-control', 'required', 'placeholder' => 'Time visitante']) !!}
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        {!! Form::text('partidas[data_partida][]', null, ['class' => 'form-control date_time', 'required', 'placeholder' => '10/10/2018 00:00:00']) !!}
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endfor

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.date_time').mask('99/99/9999 99:99', {
                placeholder: "__/__/____ __:__",
                clearIfNotMatch: true
            });
        });
    </script>
@endsection
