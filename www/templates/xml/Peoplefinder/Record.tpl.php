<?php
echo '<person>';
$rawObject = $context;

if ($context instanceof Savvy_ObjectProxy) {
    $rawObject = $context->getRawObject();
}

foreach (get_object_vars($rawObject) as $key=>$val) {
    if ($val) {
        if ($val instanceof Traversable) {
            foreach ($val as $mkey=>$value) {
                $value = htmlspecialchars($value);
                echo "<$key>{$value}</$key>\n";
            }
        } else {
            $value = htmlspecialchars($val);
            echo "<$key>{$value}</$key>\n";
        }
    }
}
if (isset($context->postalAddress) || isset($context->unlHRAddress)) {
    echo '<unlDirectoryAddress>';
    foreach ($context->formatPostalAddress() as $key=>$val) {
        echo '<'.$key.'>'.htmlspecialchars($val).'</'.$key.'>';
    }
    if ($buildingCode = $context->getUNLBuildingCode()) {
        echo '<unlBuildingCode>'.htmlspecialchars($buildingCode).'</unlBuildingCode>';
    }
    echo '</unlDirectoryAddress>';
}
echo '</person>'.PHP_EOL;