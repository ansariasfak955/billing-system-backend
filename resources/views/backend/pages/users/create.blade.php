@extends('backend.layout.master')


@section('css')
       
@stop

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <!-- Breadcrumbs -->
    <nav class="page-breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                            {!! Form::open(['route' => 'users.store', 'files' => true]) !!}
                                @include('backend.pages.users.form')
                                {!! Form::submit('Create', ['class' => 'btn btn-primary mr-2 my-2']) !!}
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