<?php
// Check if the user can edit and store this result for later
$userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
?>
<div class="departmentInfo">
    <?php
    $image_url = 'http://maps.unl.edu/BuildingImages/icon_md.png';
    if (!empty($context->building)) {
        $image_url = 'http://maps.unl.edu/'.$context->building.'/image';
    }
    ?>
    <div id="departmentDisplay">
	    <img alt="Building Image" src="<?php echo $image_url; ?>" width="100" height="100" class="frame photo">
	    <h2 class="fn org">
	       <?php echo $context->name; ?>
	       <?php
               if ($userCanEdit) {
	           
            echo '<ul class="edit_actions">';
                echo '<li><a href="'.$context->getURL().'&amp;format=editing" class="action edit" title="Edit">Edit</a></li>';

                if (!isset($context->org_unit) || UNL_Officefinder::isAdmin(UNL_Officefinder::getUser(true))) {
                    // Only allow Admins to delete "official" SAP departments
                    echo '<li>';
                    include dirname(__FILE__).'/../../editing/Officefinder/Department/DeleteForm.tpl.php';
                    echo '</li>';
                }

            echo '</ul>';
               }
           ?>
	    </h2>
	    <div class="vcardInfo">
	        <div class="adr label">
	             <span class="street-address"><?php echo $context->address; ?></span>
	             <span class="room"><?php echo $context->room.' <a class="location mapurl" href="http://maps.unl.edu/#'.$context->building.'">'.$context->building.'</a>'; ?></span>
	             <span class="locality"><?php echo $context->city; ?></span>
	             <span class="region"><?php echo $context->state; ?></span>
	             <span class="postal-code"><?php echo $context->postal_code; ?><?php echo $context->email; ?></span>
	             <span class="country-name">USA</span>
	        </div>
	        <?php if (isset($context->phone)): ?>
	        <div class="tel">
	            <span class="value">
	                <?php
	                echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
	                ?>
	            </span>
	        </div>
	        <?php endif; ?>
	        <?php if (isset($context->email)): ?>
	        <span class="email">
	           <a class="email" href="mailto:<?php echo $context->email; ?>"><?php echo $context->email; ?></a>
	        </span>
	        <?php endif; ?>
	        <?php if (isset($context->website)): ?>
	        <span class="url">
	           <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
	        </span>
	        <?php endif; ?>
	    </div>
    </div>
    <?php
    if ($userCanEdit) {
        echo '<div id="editBox">';
            // Display all aliases
            echo '<div class="aliases">';
            echo $savvy->render($context->getAliases());
            include dirname(__FILE__).'/../../editing/Officefinder/Department/AddAliasForm.tpl.php';
            echo '</div>';
            
            echo '<div class="users">';
            echo $savvy->render($context->getUsers());
            include dirname(__FILE__).'/../../editing/Officefinder/Department/User/AddForm.tpl.php';
            echo '</div>';
    
        echo '</div>';
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
        <li><a href="#results_faculty">All Employees <sup><?php echo count($department); ?></sup></a>
	        <ul>
	            <li><a href="#results_faculty">Faculty</a></li>
	            <li><a href="#results_staff">Staff</a></li>
                <li><a href="#results_student">Student</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>
    <div class="wdn_tabs_content">
        <div id="listings">
        <?php
        $listings = $context->getUnofficialChildDepartments();
        if (count($listings)) {
            echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php');
        }
        if ($userCanEdit) {
            echo '<a href="'.UNL_Officefinder::getURL(null, array('view'      => 'department',
                                                                  'parent_id' => $context->id)).'&amp;format=editing">Add a new child-listing</a>';
        }
        ?>
        </div>
        <?php
        if ($department && count($department) > 0) {
            // This listing has an official HR department associated with IT
            // render all those HR department details.
            echo $savvy->render($department);
        }
        
        ?>
    </div>
</div>
<div class="two_col right" id="orgChart">
<h2>HR Organization Chart Position</h2>
<?php
if (!$context->isRoot()) {
    $parent = $context->getParent();
    echo '<ul>
            <li><a href="'.$parent->getURL().'">'.$parent->name.'</a>';
}
?>

            <ul>
                <li><?php echo $context->name; ?>
                    <?php if ($context->hasOfficialChildDepartments()): ?>
                    <ul>
                        <?php foreach ($context->getOfficialChildDepartments('name ASC') as $child): ?>
                        <li><a href="<?php echo $child->getURL(); ?>"><?php echo $child->name; ?></a></li>
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