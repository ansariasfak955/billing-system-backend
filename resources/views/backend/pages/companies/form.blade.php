<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('commercial_name', 'Company Name', ['class' => 'form-label']) !!}
            {!! Form::text('commercial_name', isset($company->commercial_name) ? $company->commercial_name : Request::old('commercial_name'), ['class' => 'form-control', 'placeholder' => 'Enter Company Name']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($company->name) ? $company->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class' => 'form-label']) !!}
            {!! Form::text('email', isset($company->email) ? $company->email : '', ['class' => 'form-control', isset($company->email) ? 'readonly' : '', 'placeholder' => 'Enter Email']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('phone', 'Phone', ['class' => 'form-label']) !!}
            {!! Form::text('phone', isset($company->phone) ? $company->phone : '', ['class' => 'form-control', isset($company->phone) ? 'readonly' : '', 'placeholder' => 'Enter Phone Number']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group py-2">
            {!! Form::label('password', 'Password',['class' => 'form-label']) !!}    
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password']) !!}
        </div>
         <div class="form-group py-2">
            {!! Form::label('emission_point', 'Emission point', ['class' => 'form-label']) !!}
            {!! Form::text('emission_point', isset($company->emission_point) ? $company->emission_point : '', ['class' => 'form-control', 'placeholder' => 'Enter Emission Point']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group py-2">
            {!! Form::label('number_of_establishment', 'Establishment', ['class' => 'form-label']) !!}
            {!! Form::number('number_of_establishment', isset($company->number_of_establishment) ? $company->number_of_establishment : '', ['class' => 'form-control', 'placeholder' => 'Enter Number']) !!}
        </div>
        <div class="form-check form-switch mt-5">
            <input type="checkbox" class="form-check-input" id="formSwitch1" name="rimpe_regime" @if(@$company->rimpe_regime == 'yes') checked @endif>
            <label class="form-check-label" for="formSwitch1">Rimpe Regime</label>
        </div>
    </div>
    <div class=" col-md-6 mb-3">
        <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" id="formSwitch1" name="enable_technical_module" @if(@$company->enable_technical_module == '1') checked @endif>
            <label class="form-check-label" for="formSwitch1">Enable Technical Module</label>
        </div>
    </div>
    <div class=" col-md-6 mb-3">
        <div class="form-check form-switch mb-2">
            <input type="checkbox" class="form-check-input" id="formSwitch1" name="allow_access" @if(@$company->allow_access == 1) checked @endif>
            <label class="form-check-label" for="formSwitch1">Allow Access</label>
        </div>
    </div>
</div>