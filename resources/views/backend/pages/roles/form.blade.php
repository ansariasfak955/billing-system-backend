<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Role', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($role->name) ? $role->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Role']) !!}
        </div>        
    </div>
</div>