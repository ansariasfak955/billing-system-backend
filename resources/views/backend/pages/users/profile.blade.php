@extends('backend.layout.master')

{{-- Content --}}
@section('content')
    <!-- row -->
<div class="container-fluid">

         <!-- Breadcrumbs -->
    <nav class="page-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Profile</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                            <form action="/update-profile/{{ \Auth::user()->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row mt-2">
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Name:</label>
                                        <input type="text" name="name" value="{{ \Auth::user()->name }}" class="form-control" placeholder="Enter Name">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email:</label>
                                        <input type="email" name="email" value="{{ \Auth::user()->email }}" class="form-control" placeholder="Enter Email">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>Mobile Number:</strong>
                                        <input pattern="[1-9]{1}[0-9]{9}" title="Mobile number should be 10 digits and numeric only." type="text" maxlength="10" name="mobile_number" value="{{ \Auth::user()->mobile_number }}" class="form-control" placeholder="Enter Mobile Number">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <strong>Image:</strong>
                                    <div>
                                        @if(\Auth::user()->image != NULL)
                                            <a href="{{asset(\Auth::user()->image)}}"><img class="  alt="" src="{{asset(\Auth::user()->image)}}" height="100" width="100" id="thumb"></a>
                                        @endif
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" name="image" class="custom-file-input form-control" value="{{ \Auth::user()->image }}" onchange="preview()">
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 text-left">
                                    {{-- <a href="/change-password" target="_blank" class="">Click here to update password</a> --}}
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 text-right">
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            </form>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-12 -->
    </div> <!-- end row -->
          
</div>
@endsection

<script type="text/javascript">
$(".custom-file-input").change(function (e) {
    var $this = $(this);
    $this.next().html($this.val().split('\\').pop());
});
    function preview() {
       thumb.src=URL.createObjectURL(event.target.files[0]);
    }
</script>
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="{{ URL::asset('assets/js/custom.js')}}"></script>
    <!-- demo app -->
    <script src="{{asset('assets/js/pages/calendar.init.js')}}"></script>
    <!-- end demo js-->
@stop