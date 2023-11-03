<div class=" dcf-bleed dcf-hero dcf-hero-default">
    <!-- TemplateEndEditable -->
    <div class="dcf-hero-group-1">
        <div class="dcf-breadcrumbs-wrapper">
            <nav class="dcf-breadcrumbs" id="dcf-breadcrumbs" role="navigation" aria-label="breadcrumbs"></nav>
        </div>
        <header class="dcf-page-title" id="dcf-page-title">
            <h1> Personal Info </h1>
        </header>
    </div>
    <div class="dcf-hero-group-2"></div>
</div>

<div class="dcf-main-content">
    <div class="dcf-wrapper">
        <p>Welcome to the University of Nebraska–Lincoln Directory Personal Info Manager.</p>
            <form class="dcf-form dcf-col-75%-end" method="post" enctype="multipart/form-data" action="<?php echo UNL_PersonInfo::getURL() ?>" id="avatar">
                <input type="hidden" value="set_avatar" name="_type" />
                <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>">
                <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                <fieldset>
                    <legend>Your Info</legend>
                    <div class="dcf-grid dcf-grid-halves">
                        <div class="dcf-form-group">
                            <label for="profile_input">Avatar Photo Input</label>
                            <input id="profile_input" name="profile_input" type="file" accept="image/*">
                        </div>
                        <div id="profile_editor" class="dcf-form-group dcf-d-none">
                            <canvas id="profile_image" class="dcf-b-grey dcf-b-2 dcf-b-solid" height="300" width="300" tabindex="0"></canvas>
                            <input id="profile_square_scale" type="range" min="50" max="100" value="100" />
                            <input id="profile_square_size" type="hidden" name="profile_square_size" value="0"/>
                            <input id="profile_square_pos_x" type="hidden" name="profile_square_pos_x" value="0"/>
                            <input id="profile_square_pos_y" type="hidden" name="profile_square_pos_y" value="0"/>
                        </div>
                    </div>
                    <input id="submit_button" class="dcf-btn dcf-btn-primary" form="avatar" type="submit" value="Update Avatar" disabled />
                </fieldset>
            </form>
    </div>
</div>

<?php
    $baseUrl = UNL_Peoplefinder::getURL();
    $version = UNL_Peoplefinder::$staticFileVersion;
    $scriptURL = $baseUrl . 'js/directory-person-info.min.js?v=' . $version;
?>
<script defer src="<?php echo $scriptURL; ?>"></script>