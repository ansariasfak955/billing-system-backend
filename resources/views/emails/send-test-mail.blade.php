<div style="border: 1px solid #dfdfdf; padding: 50px;width: 600px;margin: 0 auto;">
	@if(isset($company->logo))
		<img style="width: 150px; height: auto; margin: 0 auto;" src="{{ asset('storage/'.$company->logo) }}">
	@endif

	<h4 style="color: #f86c02">Test email</h4>
	{{-- <hr>
	You have successfully configured your emails.
	<hr>
	You can customize your email signature!<br>

	{{ $company->name }}<br>

	{{ $company->website }}<br>

	{{ $company->address }}<br>
	{{ $company->country }} {{ $company->state }}, {{ $company->city }} {{ $company->pincode }}<br><br> --}}

	{{ $settings->option_value }}

	<div style="margin: 0 auto; text-align: center;">&#169; {{ date('Y') }} Billing System</div>
</div>