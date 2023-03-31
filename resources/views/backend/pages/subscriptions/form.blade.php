<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($subscription->name) ? $subscription->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>        
        <div class="form-group">
            {!! Form::label('stripe_price_id', 'Stripe Price Id', ['class' => 'form-label']) !!}
            {!! Form::text('stripe_price_id', isset($subscription->stripe_price_id) ? $subscription->stripe_price_id : Request::old('stripe_price_id'), ['class' => 'form-control', 'placeholder' => 'Enter Stripe Price Id' , 'required' => true]) !!}
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
        <div class="form-group py-2">
            {!! Form::label('type', 'Type', ['class' => 'form-label']) !!}
            {!! Form::select('type', [ null=> 'Please Select' ] + $types, isset($subscription->type) ? $subscription->type : '', ['class' => 'form-control']) !!}
        </div>
    </div>
</div>