<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($user->name) ? $user->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('email', 'Email', ['class' => 'form-label']) !!}
            {!! Form::text('email', isset($user->email) ? $user->email : '', ['class' => 'form-control', isset($user->email) ? 'readonly' : '', 'placeholder' => 'Enter Email']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tin', 'Tin', ['class' => 'form-label']) !!}
            {!! Form::text('tin', isset($user->tin) ? $user->tin : Request::old('tin'), ['class' => 'form-control', 'placeholder' => 'Enter Tin']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('position', 'Position', ['class' => 'form-label']) !!}
            {!! Form::text('position', isset($user->position) ? $user->position : Request::old('position'), ['class' => 'form-control', 'placeholder' => 'Enter Position']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('password', 'Password', ['class' => 'form-label']) !!}
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password']) !!}
        </div>
    </div>
</div>