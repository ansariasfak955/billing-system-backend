<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('activity_type', 'Acitivity Type', ['class' => 'form-label']) !!}
            {!! Form::text('activity_type', isset($activity_type->activity_type) ? $activity_type->activity_type : Request::old('activity_type'), ['class' => 'form-control', 'placeholder' => 'Enter Acitivity Type' , 'required' => true]) !!}
        </div>
    </div>
</div>