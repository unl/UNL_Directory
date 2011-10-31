<?php

// Set the page title and breadcrumbs
UNL_Officefinder::setReplacementData('doctitle', 'UNL | Directory | '.$context->name);
UNL_Officefinder::setReplacementData('breadcrumbs', '
    <ul>
        <li><a href="http://www.unl.edu/" title="University of Nebraska–Lincoln">UNL</a></li>
        <li><a href="'.UNL_Peoplefinder::getURL().'">Directory</a></li>
        <li>'.$context->name.'</li>
    </ul>');

$userCanEdit = false;

// Check if the user can edit and store this result for later
if ($context->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}
echo $savvy->render($context, 'Officefinder/Department/Summary.tpl.php');

// Get the official org unit if possible
$department = $context->getHRDepartment();
?>
<div class="clear"></div>
<div class="grid8 first">
    <ul class="wdn_tabs">
        <li><a href="#listings">Listings</a></li>
        <?php if ($department && count($department) > 0): ?>
        <li><a href="#all_employees">All Employees <sup><?php echo count($department); ?></sup></a></li>
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
<div class="grid4" id="orgChart">
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
<a id="reportProblem" class="dir_correctionRequest noprint" href="http://www1.unl.edu/comments/">Have a correction?</a>
