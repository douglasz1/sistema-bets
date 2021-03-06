@extends('layouts.admin')

@section('content')
    <div class="block full bordered">
        <div class="block-title">
            @if(Auth::user()->hasRoles('admin'))
                <div class="block-options pull-right">
                    <button type="button" class="btn btn-effect-ripple btn-success btn-edit">
                        <i class="fa fa-edit"></i> Editar
                    </button>
                </div>
            @endif
            <h2>Regras do site</h2>
        </div>

        <div class="rules p-lr p-tb text-light">
            {!! $rules !!}
        </div>

        @if(Auth::user()->hasRoles('admin'))
            <div class="edit-form p-lr p-tb">
                {!! Form::model($company, ['route' => ['admin.rules.update', 'id' => $company->id]]) !!}

                <div class="form-group{{ $errors->has('rules') ? ' has-error' : '' }}">
                    {!! Form::textarea('rules', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('rules'))
                        <div class="help-block animation-slideUp">{{ $errors->first('rules') }}</div>
                    @endif
                </div>

                <div class="btn-group btn-group-full btn-group-space p-t">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-edit btn-danger">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .edit-form {
            display: none;
        }

        .editing .rules {
            display: none;
        }

        .editing .edit-form {
            display: block;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.1/tinymce.min.js"
            integrity="sha256-DKYEi5Z8g2il/Yheajwj2YOzkwRw4at1jGldGh5dFxI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce-i18n@17.11.4/langs/pt_BR.js"></script>
    <script>
      tinymce.init({
        selector: 'textarea',
        menubar: false,
        height: 500,
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@17.11.4/langs/pt_BR.js',
        plugins: [
          'advlist autolink lists charmap print preview anchor textcolor',
          'searchreplace visualblocks',
          'paste code help'
        ],
        toolbar: 'insert | undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | help',
      })
      $('.btn-edit').on('click', function () {
        $('.block').toggleClass('editing')
      })
    </script>
@endsection
