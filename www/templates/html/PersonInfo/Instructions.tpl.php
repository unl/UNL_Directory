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
        <svg class="dcf-h-7 dcf-w-7 dcf-circle dcf-fill-current" aria-hidden="true" focusable="false" height="16" width="16" viewBox="0 0 48 48"><path d="M47.9 24C47.9 10.8 37.2.1 24 .1S.1 10.8.1 24c0 6.3 2.5 12.3 6.9 16.8 4.5 4.6 10.6 7.1 17 7.1s12.5-2.5 17-7.1c4.5-4.5 6.9-10.5 6.9-16.8zm-45 0C2.9 12.4 12.4 2.9 24 2.9c11.6 0 21.1 9.5 21.1 21.1 0 5.2-1.9 10.1-5.3 14-2.1-1.2-5-2.2-8.2-3.4-.7-.3-1.5-.5-2.2-.8v-3.1c1.1-.7 2.6-2.4 2.9-5.7.8-.6 1.2-1.6 1.2-2.9 0-1.1-.4-2.1-1-2.7.5-1.6 1.3-4.2.7-6.5-.7-3-4.6-4-7.7-4-2.7 0-5.9.8-7.2 2.8-1.2 0-2 .5-2.4 1-1.6 1.7-.8 4.8-.3 6.6-.6.6-1 1.6-1 2.7 0 1.3.5 2.3 1.2 2.9.3 3.4 1.8 5 2.9 5.7v3.1c-.7.2-1.4.5-2 .7-3.1 1.1-6.2 2.2-8.4 3.5-3.5-3.7-5.4-8.7-5.4-13.9zm7.5 16.1c2-1 4.6-2 7.2-2.9 1-.4 2-.7 3-1.1.5-.2.9-.7.9-1.3v-4.9c0-.6-.4-1.1-.9-1.3-.1 0-2-.8-2-4.5 0-.7-.5-1.2-1.1-1.4-.1-.3-.1-.9 0-1.2.6-.1 1.1-.7 1.1-1.4 0-.3-.1-.6-.2-1.2-.9-3.2-.7-4-.4-4.3.1-.1.4-.1 1 0 .7.1 1.5-.3 1.6-1 .3-1 2.5-1.9 5-1.9s4.7.8 5 1.9c.4 1.7-.4 4.1-.7 5.2-.2.6-.3.9-.3 1.3 0 .7.5 1.2 1.1 1.4.1.3.1.9 0 1.2-.6.1-1.1.7-1.1 1.4 0 3.7-1.9 4.5-2 4.5-.6.2-1 .7-1 1.3v4.9c0 .6.4 1.1.9 1.3 1.1.4 2.1.8 3.2 1.2 2.7 1 5.2 1.9 7.1 2.8-3.8 3.3-8.6 5-13.7 5-5.2 0-9.9-1.8-13.7-5z"></path></svg>
        <form class="dcf-form" method="post" enctype="multipart/form-data" action="<?php echo UNL_PersonInfo::getURL() ?>">
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
                <input id="submit_button" class="dcf-btn dcf-btn-primary" type="submit" value="Update" disabled />
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