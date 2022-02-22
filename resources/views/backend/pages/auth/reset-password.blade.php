@extends('backend.layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-8 col-xl-6 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-4 pe-md-0">
            <div class="auth-side-wrapper" style="background-image: url({{ asset('assets/images/side-image.jpg') }})">

            </div>
          </div>
          <div class="col-md-8 ps-md-0">
            <div class="auth-form-wrapper px-4 py-5">
              <a href="#" class="noble-ui-logo d-block mb-2" style="color:#fff">Billing<span>System</span></a>
              <h5 class="text-muted fw-normal mb-4">Reset Password</h5>
              <form method="POST" action="{!! url('/reset-password'); !!}" class="forms-sample">
                @csrf
                <div class="mb-3">
                    <label for="emailaddress" class="form-label">Email address</label>
                    <input class="form-control" type="email" name="email" id="emailaddress" required placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
                        <div class="input-group-text" data-password="false">
                            <span class="password-eye"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Password confirmation</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Enter your password confirmation">
                        <div class="input-group-text" data-password="false">
                            <span class="password-eye"></span>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                  <button type="submit" class=" btn btn-primary me-2 mb-2 mb-md-0">Reset Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection


                         