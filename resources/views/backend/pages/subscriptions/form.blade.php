<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($subscription->name) ? $subscription->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>        
        <div class="form-group py-2">
            {!! Form::label('description', 'Description',['class' => 'form-label']) !!}
            {!! Form::textarea('description', isset($subscription->description) ? $subscription->description : Request::old('description'), ['class' => 'form-control','rows' => 2, 'cols' => 40, 'placeholder' => 'Enter Description']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('price', 'Price', ['class' => 'form-label']) !!}
            {!! Form::text('price', isset($subscription->price) ? $subscription->price : '', ['class' => 'form-control', isset($subscription->price) ? $subscription->price : '', 'placeholder' => 'Enter Price']) !!}
        </div>
    </div>
</div>