{!! Form::open([ 'route' => ['activity-type.destroy', $activity_type->id], 'method' => 'delete', 'class' => 'first', 'id' => 'form-'.$activity_type->id.'']) !!}
    {!! Form::hidden('role', 'admin', ['class' => 'form-control']) !!}
    {!! Form::hidden('item-id', $activity_type->id, ['class' => 'form-control item-id']) !!}
    <a class="" href="{{ route('activity-type.edit', $activity_type->id) }}">
        <i class="fas fa-edit text-info fa-lg"></i>
    </a>    
    {!! Form::button('<i class="fas fa-trash-alt text-danger font-16"></i>', ['class' => 'btn delete', 'type' => 'submit']) !!}   
{!! Form::close() !!}