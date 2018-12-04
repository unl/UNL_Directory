<li>
	<a href="{{:userUrl}}">{{:uid}}</a>
	<form action="{{:url}}" method="post" class="delete">
	    <input type="hidden" name="_type" value="delete_dept_user" />
	    <input type="hidden" name="department_id" value="{{:department}}" />
	    <input type="hidden" name="uid" value="{{:uid}}" />
	    <button class="icon-trash wdn-button-brand" type="submit"><span class="dcf-sr-only">Delete</span></button>
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
	</form>
</li>
