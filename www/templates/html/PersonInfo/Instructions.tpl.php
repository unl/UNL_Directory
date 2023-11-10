<div class=" dcf-bleed dcf-hero dcf-hero-default">
    <!-- TemplateEndEditable -->
    <div class="dcf-hero-group-1">
        <div class="dcf-breadcrumbs-wrapper">
            <nav class="dcf-breadcrumbs" id="dcf-breadcrumbs" role="navigation" aria-label="breadcrumbs"></nav>
        </div>
        <header class="dcf-page-title" id="dcf-page-title">
            <h1> My Info </h1>
        </header>
    </div>
    <div class="dcf-hero-group-2"></div>
</div>

<div class="dcf-main-content">
    <div class="dcf-wrapper">
        <div>
            <p>Your current avatar</p>
            <div class="dcf-d-flex dcf-ai-center dcf-col-gap-vw">
                <div class="card-profile dcf-d-block dcf-mb-3 dcf-h-10 dcf-w-10 dcf-ratio dcf-ratio-1x1">
                    <img class="photo profile_pic dcf-ratio-child dcf-d-block dcf-obj-fit-cover" itemprop="image" src="<?php echo $context->get_avatar_URL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>" alt="" />
                </div>
                <div class="card-profile dcf-d-block dcf-mb-3 dcf-h-10 dcf-w-10 dcf-ratio dcf-ratio-1x1">
                    <img class="photo profile_pic dcf-ratio-child dcf-circle dcf-d-block dcf-obj-fit-cover" itemprop="image" src="<?php echo $context->get_avatar_URL(UNL_Peoplefinder_Record_Avatar::AVATAR_SIZE_LARGE) ?>" alt="" />
                </div>
            </div>
        </div>
        
        <form class="dcf-form dcf-col-75%-end" method="post" enctype="multipart/form-data" action="<?php echo UNL_PersonInfo::getURL() ?>" id="avatar">
            <input type="hidden" value="set_avatar" name="_type" />
            <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>">
            <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
            <fieldset>
                <legend>Upload your avatar</legend>
                <div class="dcf-grid-halves@md dcf-col-gap-vw dcf-row-gap-4">
                    <div>
                        <div class="dcf-form-group">
                            <label for="profile_input">Avatar Photo Input</label>
                            <input id="profile_input" name="profile_input" type="file" accept="image/jpeg, image/png, image/avif" aria-describedby="profile_input_help">
                            <p class="dcf-form-help" id="profile_input_help">Supports .jpg, and .png. Max file upload size is <?php echo 10 * intval($context->file_upload_max_size() / 10000000); ?><abbr title="Megabytes">MB</abbr>.</p>
                        </div>
                        <div class="dcf-form-group">
                            <p id="profile_input_error" class="dcf-d-none dcf-rounded dcf-p-2 dcf-txt-sm unl-bg-scarlet unl-cream" role="alert" aria-live="assertive"></p>
                            <input id="submit_button" class="dcf-btn dcf-btn-primary" form="avatar" type="submit" value="Update Avatar" disabled />
                            <input class="dcf-btn dcf-btn-secondary" form="delete_avatar" type="submit" value="Delete Avatar" />
                        </div>
                    </div>
                    <div id="profile_editor" class="dcf-form-group dcf-d-none">
                        <div id="profile_image_container" role="img" aria-label="Your uploaded image" aria-describedby="instructions" tabindex="0">
                            <canvas id="profile_image" class="dcf-b-grey dcf-b-2 dcf-b-solid dcf-w-100%" height="300" width="300" aria-hidden="true"></canvas>
                            <div class="dcf-input-group dcf-col-gap-vw">
                                <label for="profile_square_scale">Selection Size: </label>
                                <input id="profile_square_scale" type="range" min="50" max="100" value="100" />
                            </div>

                            <p id="instructions" class="dcf-txt-sm dcf-mt-3">
                                To select a portion of your image for your avatar, click and drag the square to position
                                it, or use the arrow keys for precise adjustments. Modify the size of the selected area
                                using the slider or fine-tune with the plus and minus buttons for a personalized fit.
                            </p>
                        </div>
                        <input id="profile_square_size" type="hidden" name="profile_square_size" value="0"/>
                        <input id="profile_square_pos_x" type="hidden" name="profile_square_pos_x" value="0"/>
                        <input id="profile_square_pos_y" type="hidden" name="profile_square_pos_y" value="0"/>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<form id="delete_avatar">
    <input type="hidden" value="delete_avatar" name="_type" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
</form>

<?php
    $baseUrl = UNL_Peoplefinder::getURL();
    $version = UNL_Peoplefinder::$staticFileVersion;
    $scriptURL = $baseUrl . 'js/directory-person-info.min.js?v=' . $version;
    $max_file_upload_size = $context->file_upload_max_size();
?>
<script>const MAX_FILE_UPLOAD_SIZE = <?php echo $max_file_upload_size; ?>;</script>
<script defer src="<?php echo $scriptURL; ?>"></script>