@extends('backend.layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  
@endpush


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Terms and conditions</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('termsconditions.create') }}"> Create Terms and Conditions page</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Description</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($termscondition as $terms)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $terms->title }}</td>
            <td>{{ $terms->description }}</td>
            <td>
                <form action="{{ route('termsconditions.destroy',$terms->id) }}" method="POST">
   
                    <a class="btn btn-info" href="{{ route('termsconditions.show',$terms->id) }}">Show</a>
    
                    <a class="btn btn-primary" href="{{ route('termsconditions.edit',$terms->id) }}">Edit</a>
   
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
    {!! $termscondition->links() !!}
      
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