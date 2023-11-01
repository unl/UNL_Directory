<?php
    unset($context->unluncwid);
?>
<person>
    <?php foreach ($context->jsonSerialize() as $key => $val): ?>
        <?php $savvy->renderXmlNode($key, $val) ?>
    <?php endforeach; ?>
</person>
