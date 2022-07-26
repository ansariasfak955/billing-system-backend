<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('commercial_name', 'Company Name', ['class' => 'form-label']) !!}
            {!! Form::text('commercial_name', isset($company->commercial_name) ? $company->commercial_name : Request::old('commercial_name'), ['class' => 'form-control', 'placeholder' => 'Enter Company Name']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($company_user->name) ? $company_user->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
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
    </div>
</div>