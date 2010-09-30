<script type="text/javascript">
//<![CDATA[
    WDN.loadCSS('<?php echo UNL_Peoplefinder::getURL(); ?>css/zenform_anywhere.css.php');
//]]>
</script>
<h3 class="zenform">Edit Department Record</h3>
<form class="zenform" action="<?php echo $context->getURL(); ?>" method="post">
    <input type="hidden" name="_type" value="department" />
    <?php if (isset($context->id)): ?>
    <input type="hidden" name="id" value="<?php echo $context->id; ?>" />
    <?php elseif (isset($context->options['parent_id'])) : ?>
    <input type="hidden" name="parent_id" value="<?php echo (int)$context->options['parent_id']; ?>" />
    <?php else :
            throw new Exception('You must edit a record, or set a new parent.'); 
          endif; ?>
    <fieldset>
        <legend>Department Details</legend>
        <ol>
            <li>
                <label for="name">
                    <span class="required">*</span>
                    Name
                    <span class="helper">The official name of this entity</span>
                </label>
                <input type="text" id="name" name="name" value="<?php echo $context->name; ?>" />
            </li>
            <li>
                <label for="building">Campus Building</label>
                <select name="building" id="building">
                    <option value="">N/A</option>
                    <?php
                    $found = false;
                    foreach (array('City', 'East') as $campus): ?>
                    <optgroup label="<?php echo $campus; ?> Campus">
                        <?php
                        $class = 'UNL_Common_Building_'.$campus;
                        $buildings = new $class();
                        asort($buildings->codes);
                        foreach ($buildings->codes as $code=>$name):
                            $selected = '';
                            if ($code == $context->building) {
                                $found = true;
                                $selected = 'selected="selected"';
                            }
                        ?>
                        <option value="<?php echo htmlspecialchars($code); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($name).' ('.htmlspecialchars($code).')'; ?></option>
                    
                    <?php endforeach; ?>
                    </optgroup>
                    <?php 
                    endforeach;
                    if (!$found) {
                        echo '<option value="'.$context->building.'" selected="selected">'.$context->building.' (Unknown building. Please correct if possible.)</option>'.PHP_EOL;
                    }
                    ?>
                </select>
            </li>
            <?php foreach (array('room'        => '',
                                 'address'     => 'Street address',
                                 'city'        => '',
                                 'state'       => '',
                                 'postal_code' => '10 characters max, eg 68588-0424',
                                 'phone'       => 'Use the full number with area code',
                                 'fax'         => 'Use the full number with area code',
                                 'email'       => '',
                                 'website'     => 'Always include http://') as $var=>$description): ?>
            <li>
                <label for="<?php echo $var; ?>">
                    <?php
                    echo ucwords(str_replace('_', ' ', $var));
                    if (!empty($description)) {
                        echo '<span class="helper">'.$description.'</span>';
                    }
                    ?>
                </label>
                <input type="text" id="<?php echo $var; ?>" name="<?php echo $var; ?>" value="<?php echo $context->$var; ?>" />
            </li>
            <?php endforeach; ?>
        </ol>
    </fieldset>
    <input type="submit" name="submit" value="Submit" />
</form>
<?php
// Disable editing sublistings for now.
//$listings = $context->getChildLeafNodes();
//if (count($listings)) {
//    echo $savvy->render($listings);
//}
?>
