<ul class="roles">
    <?php foreach ($context as $role): ?>
    <?php
    if (!$org = UNL_Officefinder_Department::getByorg_unit($role->unlRoleHROrgUnitNumber)) {
        // Couldn't retrieve this org's record from officefinder
        continue;
    }

    $renderLinks = $context->isRenderLinks();
    $dept_url = $org->getURL();
    $parentClass = 'unl';
    $parent_name = 'University of Nebraska–Lincoln';
    if ($org->org_unit == '50000094') {
        $parentClass = 'nu';
        $parent_name = 'University of Nebraska';
    }
    ?>
    <li class="org parent-<?php echo $parentClass ?>">
        <span class="title" itemprop="jobTitle"><?php echo $role->description ?></span>
        <span itemprop="worksFor" itemscope itemtype="http://schema.org/Organization">
            <span class="organization-unit">
                <?php if ($renderLinks): ?>
                    <a href="<?php echo $dept_url ?>" itemprop="url">
                <?php endif; ?>
                <span itemprop="name"><?php echo $org->name ?></span>
                <?php if ($renderLinks): ?>
                    </a>
                <?php endif; ?>
            </span>
            <span class="organization-name" itemprop="parentOrganization" itemscope itemtype="http://schema.org/Organization"><span itemprop="name"><?php echo $parent_name ?></span></span>
        </span>
    </li>
    <?php endforeach; ?>
</ul>
