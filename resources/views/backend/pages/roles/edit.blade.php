@extends('backend.layout.master')

@section('css')
   
@stop

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- end page title -->

    <!-- Breadcrumbs -->
    <nav class="page-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        {!! Form::open(['route' => ['roles.update', $role->id], 'files' => true,  'method' => 'PUT', 'class' => 'forms-sample']) !!}                  
                            @include('backend.pages.roles.form')
                            {!! Form::submit('Update', ['class' => 'btn btn-primary my-2']) !!}
                        {!! Form::close() !!}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-12 -->
    </div> <!-- end row -->

</div> <!-- container -->
@endsection

@section('script')
  
@stop