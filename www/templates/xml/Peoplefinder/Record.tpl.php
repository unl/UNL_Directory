<?php
echo '<person>';
foreach (get_object_vars($context) as $key=>$val) {
    if ($val) {
        foreach ($val as $value) {
            $value = htmlspecialchars($value);
            echo "<$key>{$value}</$key>\n";
        }
    }
}
echo '</person>'.PHP_EOL;