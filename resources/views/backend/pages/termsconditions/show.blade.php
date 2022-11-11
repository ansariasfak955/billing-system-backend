@extends('backend.layout.master')

@section('css')
       
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show Page</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('termsconditions.index') }}"> Back</a>
            </div>
        </div>
    </div>
   
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Title:</strong>
                {{ $termscondition->title }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {{ $termscondition->description }}
            </div>
        </div>
    </div>
@endsection


@section('script')
  
@stop