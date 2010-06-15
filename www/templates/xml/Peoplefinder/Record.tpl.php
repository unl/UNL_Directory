<?php
echo '<person>';
foreach (get_object_vars($context) as $key=>$val) {
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
echo '</person>'.PHP_EOL;