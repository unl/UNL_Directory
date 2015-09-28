
<person>
    <?php foreach ($context->jsonSerialize() as $key => $val): ?>
    <?php if ($val): ?>
        <?php
        $subAttr = false;
        ?>
        <?php if ($val instanceof Traversable): ?>
            <?php foreach ($val as $mkey => $value): ?>
            <?php if (!$subAttr && is_numeric($mkey)): ?>
                <?php $subAttr = false; ?>
                <?php echo '<'.$key.'>'.$value.'</'.$key.'>' ?>
            <?php else: ?>
                <?php if (!$subAttr): ?>
                    <?php echo '<'.$key.'>' ?>
                <?php endif; ?>
                <?php $subAttr = true; ?>
                <?php echo '<'.$mkey.'>'.$value.'</'.$mkey.'>' ?>
            <?php endif; ?>

            <?php endforeach; ?>
            <?php if ($subAttr): ?>
                <?php echo '</'.$key.'>' ?>
            <?php endif; ?>
        <?php else: ?>
            <?php echo '<'.$key.'>'.$val.'</'.$key.'>' ?>

        <?php endif ?>
    <?php endif; ?>
    <?php endforeach; ?>
</person>
