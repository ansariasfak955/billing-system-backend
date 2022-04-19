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
        <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        {!! Form::open(['route' => ['users.update', $user->id], 'files' => true,  'method' => 'PUT', 'class' => 'forms-sample']) !!}                  
                            @include('backend.pages.users.form')
                            {!! Form::submit('Update', ['class' => 'btn btn-primary my-2']) !!}
                        {!! Form::close() !!}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>

        @if($companies != NULL)
            <div class="table-responsive pt-3">
                <table class="table table-bordered">
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Address</th>
                    @foreach($companies as $company)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->country }}</td>
                            <td>{{ $company->state }}</td>
                            <td>{{ $company->city }}</td>
                            <td>{{ $company->address }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
        <!-- end col-12 -->
    </div> <!-- end row -->

</div> <!-- container -->
@endsection

@section('script')
  
@stop