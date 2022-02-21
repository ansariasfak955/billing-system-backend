@extends('backend.layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Tables</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data Table</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Users</h6>
        <div class="table-responsive">
          <table id="user_data" class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
     $(function() {
/* datatable for  monthly deviations */
            $('#user_data').DataTable({
                lengthMenu: [10, 20, 100, 500, 1000],
                pageLength: 10,
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('getUserdata') }}",
                    "type": 'GET',
                    "data":{}
                },
                columns: [
                  
                    { data: 'name', name: 'Name', render: function(data, type, full, meta) {
                        return data
                    }},
                    { data: 'email', name: 'Email', render: function(data, type, full, meta) {
                        return data
                    }},
                    { data: 'mobile_number', name: 'Phone', render: function(data, type, full, meta) {
                        return data
                    }},
                    { data: 'country', name: 'Country', render: function(data, type, full, meta) {
                        return data
                    }}
                ],
                destroy: true
            });
        });
</script>