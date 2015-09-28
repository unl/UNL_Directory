<ul class="roles">
    <?php foreach ($context as $role): ?>
    <?php
    if (!$org = UNL_Officefinder_Department::getByorg_unit($role->unlRoleHROrgUnitNumber)) {
        // Couldn't retrieve this org's record from officefinder
        continue;
    }

    $dept_url = $org->getURL();
    $parentClass = 'unl';
    $parent_name = 'University of Nebraska–Lincoln';
    if ($org->org_unit == '50000094') {
        $parentClass = 'nu';
        $parent_name = 'University of Nebraska';
    }
    ?>
    <li class="org parent-<?php echo $parentClass ?>">
        <span class="title"><?php echo $role->description ?></span>
        <span class="organization-unit"><a href="<?php echo $dept_url ?>"><?php echo $org->name ?></a></span>
        <span class="organization-name"><?php echo $parent_name ?></span>
    </li>
    <?php endforeach; ?>
</ul>
