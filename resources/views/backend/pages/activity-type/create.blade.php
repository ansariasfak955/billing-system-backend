@extends('backend.layout.master')

@section('css')
       
@stop

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <!-- Breadcrumbs -->
    <nav class="page-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('activity-type.index')}}">Activity Types</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        {!! Form::open(['route' => 'activity-type.store', 'files' => true , 'id' => 'create_activity_form']) !!}
                            @include('backend.pages.activity-type.form')
                            {!! Form::submit('Create', ['class' => 'btn btn-primary mr-2 my-2', 'id' => 'activity_create_btn']) !!}
                        {!! Form::close() !!}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-12 -->
    </div> <!-- end row -->

</div> <!-- container -->
@endsection
@push('plugin-scripts')
  <script type="text/javascript">
      $(document).ready(function(){
        //disable create button after clicked once
        $(document).on('click' , '#activity_create_btn', function(){
            if( $('#activity_type').val() ){

              $(this).prop('disabled', true);
              $(this).parents('form').submit();

            }
        });
      });
  </script>      
@endpush
@section('script')
  
@stop