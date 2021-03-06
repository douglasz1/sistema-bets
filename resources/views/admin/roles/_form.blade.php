<div class="form-group{{ $errors->has('label') ? ' has-error' : '' }}">
    {!! Form::label('label', 'Nome do nível', ['class' => 'col-md-3 control-label']) !!}
    <div class="col-md-5">
        {!! Form::text('label', null, ['class' => 'form-control']) !!}
    </div>
    @if ($errors->has('label'))
        <div class="help-block animation-slideUp">{{ $errors->first('label') }}</div>
    @endif
</div>

<div class="form-group">
    <label class="col-md-3 control-label">Permissões</label>
    <div class="col-md-5">
        @foreach($permissions as $permission)
            <label>
                <input type="checkbox" name="permissions[]"
                       {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                       value="{{ $permission->id }}">
                {{ $permission->label }}
            </label>
            <br>
        @endforeach
    </div>
</div>
