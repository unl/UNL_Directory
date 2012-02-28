<department id="<?php echo $context->id; ?>">
    <?php if (!$context->isRoot()): ?>
    <parent xlink:href="<?php echo UNL_Officefinder::getURL().$context->getParent()->id; ?>?format=xml" />
    <?php endif; ?>
    <?php foreach ($context as $key=>$value):
    if (is_array($value)) {
        continue;
    }
    $value = htmlspecialchars($value);
    ?>
    <?php echo "<$key>$value</$key>\n"; ?>
    <?php endforeach; ?>
<?php foreach ($context->getChildren() as $child): ?>
    <child xlink:href="<?php echo UNL_Officefinder::getURL().$child->id; ?>?format=xml" />
<?php endforeach; ?>
</department>
