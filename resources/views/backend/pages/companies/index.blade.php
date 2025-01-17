@extends('backend.layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Companies</li>
  </ol>
  @if(\Auth::user()->hasRole('Admin'))
    <span class="btn btn-outline-danger grid-batch-delete" aria-current="page" data-ajax-url={{ route('companies.batch-delete') }}>Batch Delete</span>
  @endif  
</nav>
 
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        {{-- @if($users_count > 0)
            <button class="btn btn-primary btn-sm grid-batch-delete mb-2" data-ajax-url="{{ url('/users/delete') }}" data-type="class">Delete All</button>
        @endif --}}
        <div class="table-responsive">
          {!! $dataTable->table() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  {!! $dataTable->scripts() !!}
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/custom.js')}}"></script>
  <script type="text/javascript">
      $(document).ready(function(){
          $(".all_items_checkbox").attr("title", "");
      });
  </script>      
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush