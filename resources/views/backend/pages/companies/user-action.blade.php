{{-- {!! Form::open([ 'route' => ['companies.destroy', $company->id], 'method' => 'delete', 'class' => 'first', 'id' => 'form-'.$company->id.'']) !!}
    {!! Form::hidden('role', 'admin', ['class' => 'form-control']) !!}
    {!! Form::hidden('item-id', $company->id, ['class' => 'form-control item-id']) !!}
    <a class="" href="{{ route('companies.edit', $company->id) }}">
        <i class="fas fa-edit text-info fa-lg"></i>
    </a>    
    {!! Form::button('<i class="fas fa-trash-alt text-danger font-16"></i>', ['class' => 'btn delete', 'type' => 'submit']) !!}
{!! Form::close() !!} --}}