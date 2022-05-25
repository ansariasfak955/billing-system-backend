{!! Form::open([ 'url' => 'company/'.$company->id.'/user/delete/'.$user->id.'', 'method' => 'delete', 'class' => 'del-company-user', 'id' => 'form-'.$company->id.'']) !!}
    <a class="" href="/company/{{$company->id}}/user/edit/{{$user->id}}">
        <i class="fas fa-edit text-info fa-lg"></i>
    </a>    
    {!! Form::button('<i class="fas fa-trash-alt text-danger font-16"></i>', ['class' => 'btn delete', 'type' => 'submit']) !!}
{!! Form::close() !!}