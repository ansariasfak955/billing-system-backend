<?php

namespace App\Http\Controllers\Api;
use Stripe\Stripe;
use Stripe\Subscription;
use App\Models\Subscription as SubPlan;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Customer;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Schema;

class StripeController extends Controller
{
    public function createPaymentLink(Request $request){

        $validator = Validator::make($request->all(),[
            // 'price_id' => 'required',
            'plan_id' => 'required',
        ]);

        if($validator->fails()){

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $validator->errors()->first()

            ]);
        }
        $plan =   SubPlan::find($request->plan_id);

        if(!$plan){
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Plan not found!'

            ]);
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe::setApiVersion('2020-08-27');
        //create or check for customer
        if(!\Auth::user()->stripe_customer_id){

            $customerEmail = \Auth::user()->email;

            $customer = Customer::all(['email' => $customerEmail])->data;

            if (count($customer) == 0) {
                // Create a new customer
                $customer = Customer::create([
                    'email' => $customerEmail,
                    'name' => \Auth::user()->name,
                ]);
            } else {
                // Use the existing customer
                $customer = $customer[0];
            }

            $customerId = @$customer->id;
            \Auth::user()->stripe_customer_id = $customerId;
            \Auth::user()->save();
        }else{
            $customerId = \Auth::user()->stripe_customer_id;
        }

        // $price = \Stripe\Price::retrieve($request->price_id);
        if($plan->type == 'monthly'){
            $type = 'month';
        }elseif($plan->type  == 'annually'){
            $type = 'year';
        }else{
            $type = '6 months';
        }
        $session = \Stripe\Checkout\Session::create([
            // 'payment_method_types' => ['card'],
            'customer' => $customerId,
            'line_items' => [[
                'name' => $plan->name,
                'amount' => $plan->price*100,
                'quantity' => 1,
                'currency' => 'usd',
            ]],
            'metadata' => [
                // 'price_id' => $price->id,
                'user_id' => \Auth::id(),
                'company_id' => request()->company_id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'membership' => "1 x ".$plan->name.'(at $'.$plan->price.'/'.$type.')' ,
            ],
            'success_url' => env('WEBSITE_APP_URL').'/membership?payment=success',
            'cancel_url' => env('WEBSITE_APP_URL').'/membership?payment=failed',
        ]);
        return response()->json(['url' => $session->url]);
    }
    public function extendSubscription(Request $request){
        $daysToAdd = getSettingValue('subscription_trail_days') ?? 15;
        // echo $daysToAdd;
        // die();
        if(\Auth::user()->plan_expiry_date){
            \Auth::user()->plan_expiry_date =  date( 'Y-m-d H:i:s', strtotime( \Auth::user()->plan_expiry_date."+ $daysToAdd days" ));
        }else{

            \Auth::user()->plan_expiry_date = date( 'Y-m-d H:i:s', strtotime( date('Y-m-d H:i:s')."+ $daysToAdd days" ));
        }
        \Auth::user()->trial_extended = 1;
        \Auth::user()->save();

        return response()->json([
            'success'   =>  true,
            'data'      =>  '',
            'message'   => 'Subscription extended!' 

        ]);

    }
    public function cancelSubscription(Request $request){
        $validator = Validator::make($request->all(),[
            // 'price_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $validator->errors()->first()

            ]);
        }
        // if(!\Auth::user()->stripe_subscription_id){

        //     return response()->json([
        //         'success' => false,
        //         'data' => [],
        //         'message' => 'Subscription id not found!'

        //     ]);
        // }

        // Stripe::setApiKey(env('STRIPE_SECRET'));
        // try {
        //     $subscription = \Stripe\Subscription::retrieve(\Auth::user()->stripe_subscription_id);
        //     $subscription->cancel();
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success'   =>  false,
        //         'message'   => 'Something went wrong!' 
    
        //     ]);
        // }
        \Auth::user()->subscription_status = $request->status;
        \Auth::user()->save();
        $token  =   \Auth::user()->createToken('api')->accessToken;
        $data   =   User::find(\Auth::id());
        $data['token'] = $token;
        return response()->json([
            'success'   =>  true,
            'data'      =>  $data,
            'message'   => 'Subscription updated!' 

        ]);
    }
    public function getInvoices(Request $request){

        if(!\Auth::user()->stripe_customer_id){

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Customer id not found!'

            ]);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe::setApiVersion('2020-08-27');
        $invoices = \Stripe\Charge::all(['customer' => \Auth::user()->stripe_customer_id]);

        return response()->json([
            'success'   =>  true,
            'data'      =>  $invoices,
            'message'   => '' 
        ]);
    }

    //handle stripe webhook
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => $e->getMessage()], 400);
        }


        if ($event->type === 'checkout.session.completed') {
            // update customer subscription
            $this->updateUserPaymentDetails($event->data);
            
        }
        // elseif($event->type === 'customer.subscription.deleted'){
        //     $this->removeUserPlanDetails($event->data);
        // }elseif($event->type === 'customer.subscription.updated'){
        //     $this->updateUserOnSubscriptionUpdate($event->data);
        // }
        
        // Return a 200 response to acknowledge receipt of the event
        return response()->json(['status' => 'success']);
    }
    
    //update the required payment info in user table
    public function updateUserPaymentDetails($data){
        Stripe::setApiKey(env('STRIPE_SECRET'));
        info($data);
        $user_id = @$data->object->metadata->user_id;
        $company_id = @$data->object->metadata->company_id;
        // $price_id = @$data->object->metadata->price_id;
        $plan_id = @$data->object->metadata->plan_id;
        $subscription = @$data->object->id;
        $customer = @$data->object->customer;
        if($company_id && $user_id  && $plan_id && $subscription && $customer){
            // $subscriptionData = Subscription::retrieve($subscription);
            $usersTables = 'company_'.$company_id.'_users';
            User::setGlobalTable($usersTables);
            $user = User::find($user_id);
            $plan = SubPlan::find($plan_id);
            if(!$user || !$plan){return;}
            // Get all subscriptions for the customer
            // $subscriptions = \Stripe\Subscription::all([
            //     'customer' => $customer,
            //     'status' => 'active',
            // ]);
            // info($subscriptions);
            // // Loop through each subscription and cancel it if it's not the current subscription
            // foreach ($subscriptions->data as $subscription_data) {
            //     if ($subscription_data->id !== $subscription) {
                //         try{
            //             $subscription_data->cancel();
            //         }catch(\Exception $e){
            //             continue;
            //         }
            //     }
            // }
            // $user->stripe_price_id = $price_id;
            if($plan->type == 'monthly'){
                $monthsToAdd = 1;
            }elseif($plan->type == 'annually'){
                $monthsToAdd = 12;
            }else{
                $monthsToAdd = 6; 
            }
            $expiry_date = date('Y-m-d H:i:s', strtotime($user->plan_expiry_date . " +$monthsToAdd month"));
            $user->stripe_customer_id = $customer;
            $user->stripe_subscription_id = $subscription;
            $user->plan_expiry_date = $expiry_date;
            $user->subscription_status = 'active';
            $user->save();
            // $subscription = \Stripe\Subscription::retrieve($subscription);
            // $subscription->metadata['user_id'] = $user_id;
            // $subscription->metadata['company_id'] = $company_id;
            // $subscription->save();
            //updating the meta in charge
            $paymentIntent = \Stripe\PaymentIntent::retrieve($data->object->payment_intent);
            if(@$paymentIntent->charges->data[0]->id){
                // Update the Charge object with metadata
                $charge = \Stripe\Charge::retrieve($paymentIntent->charges->data[0]->id);
                $charge->metadata = [
                    'date' => date('d F Y'),
                    'membership' => @$data->object->metadata->membership,
                ];
                $charge->save();
            }
            //Create invoice/subscription purchased for customer
            $invoiceTable = 'company_'.$company_id.'_invoices';
            Invoice::setGlobalTable($invoiceTable);
            Invoice::create([
                'plan_id' => @$data->object->metadata->plan_id,
                'user_id' => @$data->object->metadata->user_id,
                'expiry_date' => $expiry_date,
                'type' => $plan->type,
                'amount' => @$data->object->metadata->amount
            ]);
            return true;
        }
    }
    
    //update the required payment info in user table
    public function updateUserOnSubscriptionUpdate($data){
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $subscription = @$data->object->id;
        $user_id = @$data->object->metadata->user_id;
        $company_id = @$data->object->metadata->company_id;
        if( $subscription && $company_id){

            $expiry_date = @$data->object->current_period_end;
            $usersTables = 'company_'.$company_id.'_users';
            User::setGlobalTable($usersTables);
            $user = User::where('stripe_subscription_id',$subscription)->first();
            if(!$user){return;}
            if($expiry_date ){
                $user->plan_expiry_date = date('Y-m-d H:i:s', $expiry_date);
            }
            $user->subscription_status = 'active';
            $user->save();
            return true;
        }
    }
    
    //update the required payment info in user table
    public function removeUserPlanDetails($data){
        
        $subscription = @$data->object->subscription;
        $user_id = @$data->object->metadata->user_id;
        $company_id = @$data->object->metadata->company_id;
        if( $subscription && $company_id){
            $usersTables = 'company_'.$company_id.'_users';
            User::setGlobalTable($usersTables);
            $user = User::where('stripe_subscription_id', $subscription)->first();
            if(!$user){return;}

            // $user->plan_id = null;
            // $user->stripe_subscription_id = null;
            // $user->plan_expiry_date = null;
            $user->subscription_status = 'cancelled';
            $user->save();
            return true;
        }
    }
    
    public function getPurchaseSubscriptions(){
        
        $invoiceTable = 'company_'.request()->company_id.'_invoices';
        Invoice::setGlobalTable($invoiceTable);
        $invoices = Invoice::where('user_id',\Auth::id())->get()->toArray();
        if(!empty($invoices)){

            // Sort the array by 'status' field in ascending order
            usort($invoices, 'compareStatus');
        }
        return response()->json([
            'success'   =>  true,
            'data'      =>  $invoices,
            'message'   => '' 
        ]);
    }
    
}
