<?php
$userCanEdit = false;
if ($context->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}
?>
<div class="departmentInfo">
    <?php
    $image_url = 'http://maps.unl.edu/images/building/icon_md.png';
    if (!empty($context->building)) {
        $bldgs = new UNL_Common_Building();
        if ($bldgs->buildingExists($context->building)) {
            $image_url = 'http://maps.unl.edu/building/'.urlencode($context->building).'/image/1/md';
        }
    }
    ?>
    <div id="departmentDisplay" class="vcard office">
        <img alt="Building Image" src="<?php echo $image_url; ?>" width="100" height="100" class="frame photo">
        <h2 class="fn org">
            <?php
            echo $context->name;
            if (!empty($context->org_unit)) {
                echo ' <span class="unl-hr-org-unit-number">('.$context->org_unit.')</span>';
            }
            if ($userCanEdit) {
                echo '<ul class="edit_actions">';
                    echo '<li><a href="'.$context->getURL().'?format=editing" class="action edit" title="Edit">Edit</a></li>';
                    if (!isset($context->org_unit) || UNL_Officefinder::isAdmin(UNL_Officefinder::getUser(true))) {
                        // Only allow Admins to delete "official" SAP departments
                        echo '<li>';
                        include dirname(__FILE__).'/../../../editing/Officefinder/Department/DeleteForm.tpl.php';
                        echo '</li>';
                    }
                echo '</ul>';
            }
            ?>
        </h2>
        <div class="vcardInfo">
            <div class="adr label">
                <span class="room"><?php echo $context->room.' <a class="location mapurl" href="http://maps.unl.edu/#'.$context->building.'" onclick="WDN.jQuery.colorbox({href:\'http://maps.unl.edu/'.$context->building.'?format=staticgooglemapsv2&size=400x400&zoom=17\', width:\'460px\', height:\'490px\'});return false;">'.$context->building.'</a>'; ?></span>
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
    <?php
    if ($userCanEdit) {
        echo $savvy->render($context, 'Officefinder/Department/EditBox.tpl.php');
    }
    ?>
</div>