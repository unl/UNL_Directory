<?php

// Staff/Student with no Knowledge data
if ((isset($context->options['format']) && $context->options['format'] === 'hcard')|| !$context->knowledge->public_web) {
    echo $savvy->render($context->record, 'Peoplefinder/Record.tpl.php');
}
// Faculty member with Knowledge data
else {
    UNL_Peoplefinder::setReplacementData('pagetitle', '<h1>'.$context->record->displayName.'</h1>');
?>
<section class="wdn-grid-set">
    <div class="bp2-wdn-col-two-sevenths">
        <?php // echo $savvy->render($context->record, 'Peoplefinder/Record.tpl.php'); ?>
        <!-- <img class="frame" src="<?php echo $context->record->getImageURL('large'); ?>" alt="<?php echo $context->record->displayName ?>" /> -->
        <h1 class="clear-top"><?php echo $context->record->getPreferredFirstName(); ?> <?php echo $context->record->sn; ?></h1>

        <?php
        if (isset($context->record->unlHROrgUnitNumber)) {
            $roles = $parent->context->getRoles($context->record->dn);
            if (count($roles)) {
                echo $savvy->render($roles);
            }
        }

        echo '<div class="directory-contact">';

        if (isset($context->record->postalAddress)) {
            $address = $context->record->formatPostalAddress();

            echo '<div class="wdn-icon-location wdn-sans-serif"><div class="adr workAdr"> ';
            if ((strpos($address['postal-code'], '6858') === 0) &&
                ($code = $context->record->getUNLBuildingCode())) {
                echo '<span class="street-address">'. str_replace($code, '<a class="location mapurl" href="http://maps.unl.edu/#'.$code.'">'.$code.'</a>', $address['street-address']) . '</span>';
            } else {
                echo '<span class="street-address">'. $address['street-address'] . '</span>';
            }
            echo '<br>
         <span class="locality">' . $address['locality'] . '</span>
         <span class="region">' . $address['region'] . '</span>
         <span class="postal-code">' . $address['postal-code'] . '</span>
        </div></div>'.PHP_EOL;
        }

        if (isset($context->record->telephoneNumber)) {
            echo '<div class="wdn-icon-phone wdn-sans-serif"><div class="tel workTel">
             <span class="voice">
             <span class="value">'.$savvy->render($context->record->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>
             </span>
            </div></div>'.PHP_EOL;
        }

        $displayEmail = false;
        if (isset($context->record->mail)
            && ($context->record->eduPersonPrimaryAffiliation != 'student')) {
            $displayEmail = true;
        }
        if ($displayEmail) {
            echo "<div class='wdn-icon-mail wdn-sans-serif'><span class='email'><a class='email' href='mailto:{$context->record->mail}'>{$context->record->mail}</a></span></div>\n";
        }
        echo '</div>';
        ?>


    </div>
    <div class="bp2-wdn-col-five-sevenths">
        <div class="directory-knowledge">
            <?php echo $savvy->render($context->knowledge); ?>
        </div>
    </div>
</section>

    <style>
        #wdn_content_wrapper {background: #eeebe4; }
        #pagetitle {display: none;}

        .directory-knowledge {
            background:white;
            border-top: 4px solid #CC0000;
            padding: 3em 4em;
            font-size: .95em;
        }

        .org {
            color: rgba(91, 91, 90, 0.9);
            display: block;
            font-family: "Gotham SSm A","Gotham SSm B",Verdana,"Verdana Ref",Geneva,Tahoma,"Lucida Grande","Lucida Sans Unicode","Lucida Sans","DejaVu Sans","Bitstream Vera Sans","Liberation Sans",sans-serif;
            font-style: normal;
            font-weight: 400;
            letter-spacing: 0.02em;
            line-height: 1.333;
            margin: 1em 0;
            text-transform: uppercase;
        }
        .org .title {display: block}
        .organization-unit {font-size:.8em}
        .organization-name {display:none}

        [class^="wdn-icon-"]:before, [class*=" wdn-icon-"]:before {
            vertical-align: top;
        }
        .directory-knowledge [class^="wdn-icon-"]:before, [class*=" wdn-icon-"]:before {
            color: #CC0000;
        }

        .adr, .tel {display: inline-block;}
        #maincontent .on-campus-dialing {display: block;}

        .directory-contact {
            border-top: 1px solid #aaa;
            padding-top: 1em;
        }
        .directory-contact > div {
            margin-bottom: 10px;
            font-size: .85em;
        }
    </style>


<?php } ?>

