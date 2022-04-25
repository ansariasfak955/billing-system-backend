{!! Form::open([ 'route' => ['roles.destroy', $role->id], 'method' => 'delete', 'class' => 'first', 'id' => 'form-'.$role->id.'']) !!}
    {!! Form::hidden('role', 'admin', ['class' => 'form-control']) !!}
    {!! Form::hidden('item-id', $role->id, ['class' => 'form-control item-id']) !!}
    <a class="" href="{{ route('roles.edit', $role->id) }}">
        <i class="fas fa-edit text-info fa-lg"></i>
    </a>    
    {!! Form::button('<i class="fas fa-trash-alt text-danger font-16"></i>', ['class' => 'btn delete', 'type' => 'submit']) !!}   
{!! Form::close() !!}