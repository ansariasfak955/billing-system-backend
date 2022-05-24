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
            <li class="breadcrumb-item"><a href="{{route('companies.index')}}">Companies</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        {!! Form::open(['route' => ['companies.update', $company->id], 'files' => true,  'method' => 'PUT', 'class' => 'forms-sample']) !!}                  
                            @include('backend.pages.companies.form')
                            {!! Form::submit('Update', ['class' => 'btn btn-primary my-2']) !!}
                        {!! Form::close() !!}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>

        @if($users != NULL)
        <div class="row mt-4">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Users</h6>
                        <div class="table-responsive pt-3">
                            <table class="table table-bordered" id="dataTableExample">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ get_user_role($company->id, $user->role) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- end col-12 -->
    </div> <!-- end row -->

</div> <!-- container -->
@endsection

@section('script')

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
{{-- <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script> --}}
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush

@stop