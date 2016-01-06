<li>
	<span>{{:name}}</span>
	<form action="{{:url}}" method="post" class="delete">
	    <input type="hidden" name="_type" value="delete_dept_alias" />
	    <input type="hidden" name="department_id" value="{{:department}}" />
	    <input type="hidden" name="name" value="{{:name}}" />
		<button class="icon-trash wdn-button-brand" type="submit"><span class="wdn-text-hidden">Delete</span></button>
	</form>
</li>
