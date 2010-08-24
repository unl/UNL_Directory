<div class="departmentInfo">
    <img alt="Building Image" src="http://maps.unl.edu/<?php echo $context->building;?>/image" width="100" height="100" class="frame photo">
    <h2 class="fn org"><?php echo $context->name; ?></h2>
    <div class="vcardInfo">
        <div class="adr label">
             <span class="street-address"><?php echo $context->address; ?></span>
             <span class="room"><?php echo $context->room.' <a class="location mapurl" href="http://www1.unl.edu/tour/'.$context->building.'">'.$context->building.'</a>'; ?></span>
             <span class="locality"><?php echo $context->city; ?></span>
             <span class="region"><?php echo $context->state; ?></span>
             <span class="postal-code"><?php echo $context->postal_code; ?><?php echo $context->email; ?></span>
             <span class="country-name">USA</span>
        </div>
        <div class="tel">
            <span class="value">
                <?php
                echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
                ?>
            </span>
        </div>
        <span class="email">
           <a class="email" href="mailto:<?php echo $context->email; ?>"><?php echo $context->email; ?></a>
        </span>
        <span class="url">
           <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
        </span>
    </div>
    <?php
    if ($context->userCanEdit(UNL_Officefinder::getUser())) {
        echo '<a href="'.UNL_Officefinder::getURL().'?view=department&amp;id='.$context->id.'&amp;format=editing" class="action edit">Edit</a><br />';
        include dirname(__FILE__).'/../../editing/Officefinder/Department/DeleteForm.tpl.php';
    }

    // Get the official org unit if possible
    $department = $context->getHRDepartment();

    ?>
</div>
<div class="clear"></div>
<div class="two_col left">
    <ul class="wdn_tabs">
        <li><a href="#listings">Listings</a></li>
        <?php if ($department && count($department) > 0): ?>
        <li><a href="#employees">All Employees <sup><?php echo count($department); ?></sup></a></li>
        <?php endif; ?>
    </ul>
    <div class="wdn_tabs_content">
        <div id="listings">
        <?php
        $listings = $context->getChildren();
        if (count($listings)) {
            echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php');
        }
        ?>
        </div>
        <div id="employees">
        <?php
        if ($department && count($department) > 0) {
            // This listing has an official HR department associated with IT
            // render all those HR department details.
            echo $savvy->render($department);
        }
        
        ?>
        </div>
    </div>
</div>
<div class="two_col right" id="orgChart">
<h2>HR Organization Chart Position</h2>
<?php
if (!$context->isRoot()) {
    $parent = $context->getParent();
    echo '<ul>
            <li><a href="?view=department&amp;id='.$parent->id.'">'.$parent->name.'</a>';
}
?>

            <ul>
                <li><?php echo $context->name; ?>
                    <?php if ($context->hasOfficialChildDepartments()): ?>
                    <ul>
                        <?php foreach ($context->getOfficialChildDepartments('name ASC') as $child): ?>
                        <li><a href="?view=department&amp;id=<?php echo $child->id; ?>"><?php echo $child->name; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
            </ul>
<?php
if (!$context->isRoot()) {
        echo '</li>
    </ul>';
}
?>
</div>