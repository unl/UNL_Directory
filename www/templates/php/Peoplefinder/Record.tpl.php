<?php
if (isset($_GET['unsafe']) && $_GET['unsafe']) {
	echo serialize($context);
} elseif (isset($_GET['multivalue']) && $_GET['multivalue']) {
	echo $context->serialize(UNL_Peoplefinder_Record::SERIALIZE_VERSION_SAFE_MULTIVALUE);
} else {
	echo $context->serialize(UNL_Peoplefinder_Record::SERIALIZE_VERSION_SAFE);
}
