<picture>
    <source
        media="(-webkit-min-device-pixel-ratio: 2)"
        srcset="
            <?php echo $context->getImageURL(48, 144); ?> 48w,
            <?php echo $context->getImageURL(72, 144); ?> 72w,
            <?php echo $context->getImageURL(100, 144); ?> 100w,
            <?php echo $context->getImageURL(120, 144); ?> 120w,
            <?php echo $context->getImageURL(200, 144); ?> 200w,
            <?php echo $context->getImageURL(240, 144); ?> 240w,
            <?php echo $context->getImageURL(400, 144); ?> 400w,
            <?php echo $context->getImageURL(800, 144); ?> 800w,
        "
    >
    <img
        class="photo profile_pic dcf-ratio-child dcf-circle dcf-d-block dcf-obj-fit-cover"
        itemprop="image"
        src="<?php echo $context->getImageURL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>"
        srcset="
            <?php echo $context->getImageURL(48, 72); ?> 48w,
            <?php echo $context->getImageURL(72, 72); ?> 72w,
            <?php echo $context->getImageURL(100, 72); ?> 100w,
            <?php echo $context->getImageURL(120, 72); ?> 120w,
            <?php echo $context->getImageURL(200, 72); ?> 200w,
            <?php echo $context->getImageURL(240, 72); ?> 240w,
            <?php echo $context->getImageURL(400, 72); ?> 400w,
            <?php echo $context->getImageURL(800, 72); ?> 800w,
        "
        alt=""
    />
</picture>
