<?php
    unset($context->unluncwid);
    unset($context->unlSISMajor);
    unset($context->unlSISMinor);
    unset($context->unlSISClassLevel);
?>
<person>
    <?php foreach ($context->jsonSerialize() as $key => $val): ?>
        <?php $savvy->renderXmlNode($key, $val) ?>
    <?php endforeach; ?>
</person>
