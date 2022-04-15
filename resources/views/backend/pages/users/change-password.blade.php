@extends('backend.layout.master')

{{-- Content --}}
@section('content')
<!-- row -->
<div class="container-fluid">
    <nav class="page-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Change Password</li>
      </ol>
    </nav>
        
    <form action="" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row mb-2">
        <div class="col-xs-12 col-sm-4 col-md-4">
          <div class="form-group">
            <strong>Current Password:</strong>
            <input type="password"  name="current_password" class="form-control" placeholder="Enter Current password">
          </div>
        </div>
        @if("current password" != "")
          <div class="col-xs-12 col-sm-4 col-md-4">
            <div class="form-group">
              <strong>New Password:</strong>
              <input type="password" id="password" name="new_password" class="form-control" placeholder="Enter New password">
            </div>
          </div>
          <div class="col-xs-12 col-sm-4 col-md-4">
            <div class="form-group">
              <strong>Confirm Password:</strong>
              <input type="password" id="confirm_password" name="password" class="form-control" placeholder="Enter Confirm password">
              <span id='message'></span>
              @if ($errors->has('password_confirmation'))
                  <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
              @endif
            </div>
          </div>
        @endif 
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 text-right">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
</div>
@endsection

@section('script')
  <script>
    $(document).ready(function (){
      $('#password, #confirm_password').on('keyup', function () {
        if(($('#password').val() != "") && ($('#password').val() != "")){
          if ($('#password').val() == $('#confirm_password').val()) {
            $('#message').html('Matched').css('color', 'green');
          } else {
            $('#message').html('Not Matching').css('color', 'red');
          }
        }else{
          $('#message').html('');
        }
      });
    });
  </script>
@stop     