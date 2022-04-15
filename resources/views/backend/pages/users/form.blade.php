<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($user->name) ? $user->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>        
        <div class="form-group py-2">
            {!! Form::label('password', 'Password',['class' => 'form-label']) !!}    
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password']) !!}
        </div>
        {{-- {!! Form::hidden('role', $role, ['class' => 'form-control']) !!} --}}
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class' => 'form-label']) !!}
            {!! Form::text('email', isset($user->email) ? $user->email : '', ['class' => 'form-control', isset($user->email) ? 'readonly' : '', 'placeholder' => 'Enter Email']) !!}
        </div>
        <div class="form-group py-2">
            {!! Form::label('role', 'Role', ['class' => 'form-label']) !!}
            {!! Form::select('role', [null => 'Please Select'] + $roles, $role, ['class' => 'form-control']) !!}
        </div>
        {{-- <div class="form-group pt-4">
            <div class="form-check form-switch">
                <label class="form-check-label" for="customSwitch1">Ban/Unban</label>
                @if(isset($user->is_ban))
                    @if($user->is_ban == 1)
                        <input type="checkbox" name="is_ban" class="form-check-input" id="customSwitch1" checked>
                        @else 
                        <input type="checkbox" name="is_ban" class="form-check-input" id="customSwitch1">
                    @endif
                @else
                    <input type="checkbox" name="is_ban" class="form-check-input" id="customSwitch1">
                @endif
            </div>
        </div> --}}
    </div>
</div>



