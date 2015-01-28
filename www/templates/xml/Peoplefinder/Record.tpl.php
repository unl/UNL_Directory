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
if ($address = $context->formatPostalAddress()) {
    echo '<unlDirectoryAddress>';
    foreach ($address as $key=>$val) {
        echo '<'.$key.'>'.htmlspecialchars($val).'</'.$key.'>';
    }
    echo '</unlDirectoryAddress>';
}
echo '</person>'.PHP_EOL;