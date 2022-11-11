@extends('backend.layout.master')

@section('css')
       
@stop

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


@section('script')
  
@stop