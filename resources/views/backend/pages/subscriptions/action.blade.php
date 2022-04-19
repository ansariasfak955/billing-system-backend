{!! Form::open([ 'route' => ['subscriptions.destroy', $subscription->id], 'method' => 'delete', 'class' => 'first', 'id' => 'form-'.$subscription->id.'']) !!}
    {!! Form::hidden('role', 'admin', ['class' => 'form-control']) !!}
    {!! Form::hidden('item-id', $subscription->id, ['class' => 'form-control item-id']) !!}
    <a class="" href="{{ route('subscriptions.edit', $subscription->id) }}">
        <i class="fas fa-edit text-info fa-lg"></i>
    </a>    
    {!! Form::button('<i class="fas fa-trash-alt text-danger font-16"></i>', ['class' => 'btn delete', 'type' => 'submit']) !!}   
{!! Form::close() !!}