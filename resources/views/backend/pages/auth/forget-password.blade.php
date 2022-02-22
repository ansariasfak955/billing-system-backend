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
              <form method="POST" action="{!! url('forgot-password'); !!}" class="forms-sample">
              @csrf
                <div class="mb-3">
                  <label for="userEmail" class="form-label">Email address</label>
                  <input type="email"  name="email" class="form-control" id="userEmail" placeholder="Email">
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