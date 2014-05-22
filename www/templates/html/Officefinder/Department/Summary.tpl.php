<?php
$userCanEdit = false;
if ($controller->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}
?>
<div class="departmentInfo"
        <?php 
         if (!empty($context->org_unit)) {
                echo ' data-orgUnit="'.$context->org_unit.'"';
            }
        ?>
    >
    <?php
    $image_url = 'http://maps.unl.edu/images/building/icon_md.png';
    if (!empty($context->building)) {
        $bldgs = new UNL_Common_Building();
        if ($bldgs->buildingExists($context->building)) {
            $image_url = 'http://maps.unl.edu/building/'.urlencode($context->building).'/image/1/md';
        }
    }
    ?>
    
    <?php 
    if ($userCanEdit) {
    	echo '<a class="minibutton edit" href="'.$context->getURL().'?format=editing" title="Edit"><span class="icon">Edit<span></a>
        	<div class="action_control">
            <h4>Edit this Department</h4>
    		<div class="form"></div>';
            if (!isset($context->org_unit) || UNL_Officefinder::isAdmin(UNL_Officefinder::getUser(true))) {
            	// Only allow Admins to delete "official" SAP departments
            	include dirname(__FILE__).'/../../../editing/Officefinder/Department/DeleteForm.tpl.php';
            }
        echo '</div>';
    }
    ?>
    <div class="vcard office">
        <img alt="Building Image" src="<?php echo $image_url; ?>" width="100" height="100" class="frame photo">
        <h4 class="fn org">
            <a href="<?php echo $context->getURL();?>">
            <?php
            	echo $context->name;
            ?>
            </a>
            <a class="permalink" href="<?php echo $context->getURL();?>">link</a>
        </h4>
        <div class="vcardInfo">
            <div class="adr label">
                <span class="type">Address</span>
                <span class="room">
                    <?php 
                    if ($context->building) {
                        echo $context->room.' <a class="location mapurl" href="http://maps.unl.edu/#'.$context->building.'" onclick="WDN.jQuery.colorbox({href:\'http://maps.unl.edu/'.$context->building.'?format=staticgooglemapsv2&size=400x400&zoom=17\', width:\'460px\', height:\'490px\'});return false;">'.$context->building.'</a>'; 
                    }
                    ?>
                </span>
                <?php
                if (!empty($context->address)) {
                    echo "<span class='street-address'>" . $context->address . "</span>";
                }
                if (!empty($context->city)) {
                    echo "<span class='locality'>" . $context->city . "</span>";
                }
                if (!empty($context->state)) {
                    echo "<span class='region'>" . $context->state . "</span>";
                }
                if (!empty($context->postal_code)) {
                    echo "<span class='postal-code'>" . $context->postal_code . "</span>";
                }
                ?>
                <span class='country-name'>USA</span>
            </div>
            
            <?php if (isset($context->phone)): ?>
            <div class="tel">
                <span class="voice">Phone:
                    <?php
                    echo $savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
                    ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if (isset($context->fax)): ?>
            <div class="tel">
                <span class="fax">Fax:
                    <?php
                    echo $savvy->render($context->fax, 'Peoplefinder/Record/TelephoneNumber.tpl.php');
                    ?>
                </span>
            </div>
            <?php endif; ?>
            
            
            <?php if (isset($context->email)): ?>
            <div class="email">
                <span class="email">
                   <a class="email" href="mailto:<?php echo $context->email; ?>"><?php echo $context->email; ?></a>
                </span>
            </div>
            <?php endif; ?>
            <?php if (isset($context->website)): ?>
            <div class="url">
                <span class="url">
                   <a class="url" href="<?php echo $context->website; ?>"><?php echo $context->website; ?></a>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>