<div class="vcard"></div>
	<h2 class="fn org"><?php echo $context->name; ?></h2>
	<img alt="Generic Icon" src="http://www1.unl.edu/tour/BuildingImages/<?php echo $context->building;?>_01_sm.jpg" width="100" height="100" class="frame photo">
	<div class="vcardInfo">
		<div class="adr label">
		     <span class="street-address"><?php echo $context->address; ?></span>
		     <span class="room"><?php echo $context->room.' <a class="location mapurl" href="http://www1.unl.edu/tour/'.$context->building.'">'.$context->building.'</a>'; ?></span>
		     <span class="locality"><?php echo $context->city; ?></span>
		     <span class="region"><?php echo $context->state; ?></span>
		     <span class="postal-code"><?php echo $context->postal_code; ?><?php echo $context->email; ?></span>
		     <div class="country-name">USA</div>
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
</div>
<div class="clear"></div>
<?php 
echo '<ul style="display:none;">';
foreach ($context as $var=>$value) {
    echo '<li>'.$var.':'.$value.'</li>';
}
echo '</ul>';

if ($context->userCanEdit(UNL_Officefinder::getUser())) {
    echo '<a href="?view=department&amp;id='.$context->id.'&amp;format=editing">Edit</a><br />';
}

// Get the official org unit if possible
$department = $context->getHRDepartment();

?>
<ul class="wdn_tabs">
    <li><a href="#listings">Listings</a></li>
    <?php if ($department): ?>
    <li><a href="#employees">Employees</a></li>
    <?php endif; ?>
</ul>
<div class="wdn_tabs_content">
    <div id="listings">
    <?php
    $listings = $context->getListings();
    if (count($listings)) {
        echo $savvy->render($listings);
    }
    ?>
    </div>
    <div id="employees">
    <?php
    if ($department) {
        // This listing has an official HR department associated with IT
        // render all those HR department details.
        echo $savvy->render($department);
    }
    
    ?>
    </div>
</div>