<div class="row">
    <div class="col-md-6">
        <div class="form-group mt-2">
            {!! Form::label('name', 'Name', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($subscription->name) ? $subscription->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Name']) !!}
        </div>        
        <div class="form-group mt-2">
            {!! Form::label('stripe_price_id', 'Stripe Price Id', ['class' => 'form-label stripe_label']) !!}
            {!! Form::text('stripe_price_id', isset($subscription->stripe_price_id) ? $subscription->stripe_price_id : Request::old('stripe_price_id'), ['class' => 'form-control', 'placeholder' => 'Enter Stripe Price Id' , 'required' => true]) !!}
        </div>        
        {{-- <div class="form-group mt-2">
            {!! Form::label('stripe_price_id_yearly', 'Stripe Price Id(Yearly)', ['class' => 'form-label']) !!}
            {!! Form::text('stripe_price_id_yearly', isset($subscription->stripe_price_id_yearly) ? $subscription->stripe_price_id_yearly : Request::old('stripe_price_id_yearly'), ['class' => 'form-control', 'placeholder' => 'Enter Stripe Price Id(Yearly)' , 'required' => true]) !!}
        </div>         --}}
        <div class="form-group mt-2 py-2">
            {!! Form::label('description', 'Description',['class' => 'form-label']) !!}
            {!! Form::textarea('description', isset($subscription->description) ? $subscription->description : Request::old('description'), ['class' => 'form-control','rows' => 5, 'cols' => 40, 'placeholder' => 'Enter Description']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mt-2">
            {!! Form::label('price', 'Price', ['class' => 'form-label price_label']) !!}
            {!! Form::text('price', isset($subscription->price) ? $subscription->price : '', ['class' => 'form-control', isset($subscription->price) ? $subscription->price : '', 'placeholder' => 'Enter Price']) !!}
        </div>
        {{-- <div class="form-group mt-2">
            {!! Form::label('price_yearly', 'Price(Yearly)', ['class' => 'form-label']) !!}
            {!! Form::text('price_yearly', isset($subscription->price_yearly) ? $subscription->price_yearly : '', ['class' => 'form-control', isset($subscription->price_yearly) ? $subscription->price_yearly : '', 'placeholder' => 'Enter Price(Yearly)']) !!}
        </div> --}}
        <div class="form-group mt-2 py-2">
            {!! Form::label('type', 'Type', ['class' => 'form-label']) !!}
            {!! Form::select('type', [ null=> 'Please Select' ] + $types, isset($subscription->type) ? $subscription->type : '', ['class' => 'form-control subscription_type']) !!}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let subscriptionType = document.querySelector('.subscription_type');
        subscriptionType.addEventListener('change', checkForSelectedType);
        
        checkForSelectedType();
        function checkForSelectedType() {
            let subscriptionType = document.querySelector('.subscription_type');
            const selectedOptionText = subscriptionType.options[subscriptionType.selectedIndex].text;
            if (selectedOptionText !== 'Please Select') {
            const priceLabel = document.querySelector('.price_label');
            priceLabel.textContent = `Price (${selectedOptionText})`;
            const stripeLabel = document.querySelector('.stripe_label');
            stripeLabel.textContent = `Stripe Price Id (${selectedOptionText})`;
        }
    }
    });

</script>