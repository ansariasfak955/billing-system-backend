<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('name', 'Role', ['class' => 'form-label']) !!}
            {!! Form::text('name', isset($role->name) ? $role->name : Request::old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Role']) !!}
        </div>        
    </div>
</div>

@foreach($permissions as $permission)
   	<div class="row border-bottom py-4">
   		<div class="col-md-3"><label>{{ $permission->name }}</label></div>
   		<div class="col-md-9">
   			@if(isset($permission->children))
			    <div class="row">
			      	<div class="col-md-7">
			      	</div>
			     	<div class="col-md-5">
			     		@if(count($permission->children[0]->children) != 0)
					        <table class="table fixed-width">
					          	<tr>
						            <th>View</th>
						            <th>Edit</th>
						            <th>Create</th>
						            <th>Delete</th>
						            <th>
						            	@if (strpos($permission->name, 'Connect') !== false)
						            		Send
						            	@endif
						            </th>
					          	</tr>
					        </table>
				        @endif
			      	</div>
			    </div>
			@endif
		
			<div class="row">
		      	@foreach($permission->children as $children)
		      		<div class="col-md-7">
		      			@if($children->is_checkbox != 0)
			      			<input class="p-0 d-inline-block" type="checkbox" name="permissions[]" value="{{$children->id}}" {{ in_array($children->id, $selected_permissions) ? 'checked' : '' }}>&nbsp; {{$children->name}}
			      		@else
			      			{{$children->name}}
			      		@endif
		      		</div>
		      	
			      	<div class="col-md-5">
			      		@if(count($permission->children[0]->children) != 0)
			      		<table class="table p-0 m-0 fixed-width">
				          	<tr>
				          		<td>
						      		@foreach($children->children as $new_child)
					          			@if (strpos($new_child->name, 'view') !== false)
									    	<input class="p-0 d-inline-block" name="permissions[]" type="checkbox" value="{{$new_child->id}}" {{ in_array($new_child->id, $selected_permissions) ? 'checked' : '' }}>
										@endif
									@endforeach
								</td>
								<td>
									@foreach($children->children as $new_child)
										@if (strpos($new_child->name, 'edit') !== false)
									   		<input class="p-0 d-inline-block" name="permissions[]" type="checkbox" value="{{$new_child->id}}" {{ in_array($new_child->id, $selected_permissions) ? 'checked' : '' }}>
										@endif
									@endforeach
								</td>	
								<td>
									@foreach($children->children as $new_child)
										@if (strpos($new_child->name, 'create') !== false)
									    	<input class="p-0 d-inline-block" name="permissions[]" type="checkbox" value="{{$new_child->id}}" {{ in_array($new_child->id, $selected_permissions) ? 'checked' : '' }}>
										@endif
									@endforeach
								</td>
								<td>
									@foreach($children->children as $new_child)
										@if (strpos($new_child->name, 'delete') !== false)
									    	<input class="p-0 d-inline-block" name="permissions[]" type="checkbox" value="{{$new_child->id}}" {{ in_array($new_child->id, $selected_permissions) ? 'checked' : '' }}>
										@endif
									@endforeach
								</td>
								<td>
									@foreach($children->children as $new_child)
										@if (strpos($new_child->name, 'send') !== false)
									    	<input class="p-0 d-inline-block" type="checkbox" name="permissions[]" value="{{$new_child->id}}" {{ in_array($new_child->id, $selected_permissions) ? 'checked' : '' }}>
										@endif
									@endforeach
								</td>
							</tr>
						</table>
						@endif
			      	</div>
		      	@endforeach
		    </div>
	    </div>
   	</div>
@endforeach