<?php
    // var_dump($context->getUser());
?>
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

<script defer>
    const profile_input = document.getElementById('profile_input');
    const profile_editor = document.getElementById('profile_editor');
    const profile_canvas = document.getElementById('profile_image');
    const profile_canvas_context = profile_canvas.getContext('2d');
    const profile_square_scale = document.getElementById('profile_square_scale');
    const profile_square_pos_x = document.getElementById('profile_square_pos_x');
    const profile_square_pos_y = document.getElementById('profile_square_pos_y');
    const profile_square_size = document.getElementById('profile_square_size');
    const submit_button = document.getElementById('submit_button');

    let profile_image = new Image();
    let square_size_max = 300;
    let square_size = 300;
    let square_x_pos = 0;
    let square_y_pos = 0;

    let pointer_start_x = -1;
    let pointer_start_y = -1;

    profile_input.addEventListener('change', () => {
        if (profile_input.files[0] === undefined) { return }

        profile_image = new Image();
        let profile_file = profile_input.files[0];
        let profile_image_url = URL.createObjectURL(profile_file);
        profile_image.onload = function () {
            URL.revokeObjectURL(profile_image_url);

            const aspect_ratio = profile_image.width / profile_image.height;

            profile_canvas.width = 300;
            profile_canvas.height = 300 / aspect_ratio;
            square_size = Math.min(profile_canvas.width, profile_canvas.height);
            square_size_max = Math.min(profile_canvas.width, profile_canvas.height);
            square_x_pos = 0;
            square_y_pos = 0;

            set_profile_square_size();
            set_profile_square_pos();
            draw();

            profile_editor.classList.remove('dcf-d-none');
            submit_button.removeAttribute('disabled');
        };
        profile_image.src = profile_image_url;
    });

    profile_square_scale.addEventListener('input', () => {
        square_size = (profile_square_scale.value / 100) * square_size_max;

        check_and_set_square_bounds();

        set_profile_square_size();
        set_profile_square_pos();
        draw();
    });
    profile_square_scale.addEventListener('change', () => {
        square_size = (profile_square_scale.value / 100) * square_size_max;

        check_and_set_square_bounds();

        set_profile_square_size();
        set_profile_square_pos();
        draw();
    });

    profile_canvas.addEventListener('mousedown', (e) => {
        pointer_start_x = e.clientX;
        pointer_start_y = e.clientY;
    });

    profile_canvas.addEventListener('mousemove', (e) => {
        if (pointer_start_x === -1 || pointer_start_y === -1) { return }
        x_diff = pointer_start_x - e.clientX;
        y_diff = pointer_start_y - e.clientY;

        square_x_pos -= x_diff;
        square_y_pos -= y_diff;

        check_and_set_square_bounds();

        pointer_start_x = e.clientX;
        pointer_start_y = e.clientY;

        draw();
    });

    profile_canvas.addEventListener('mouseleave', (e) => {
        if (pointer_start_x === -1 || pointer_start_y === -1) { return }
        pointer_start_x = -1;
        pointer_start_y = -1;

        set_profile_square_pos();
    });

    profile_canvas.addEventListener('mouseup', (e) => {
        if (pointer_start_x === -1 || pointer_start_y === -1) { return }
        pointer_start_x = -1;
        pointer_start_y = -1;

        set_profile_square_pos();
    });

    profile_canvas.addEventListener('keydown', (e) => {
        console.log(e.code)
        let valid_key_pressed = false;
        switch (e.code) {
            case "ArrowUp":
                valid_key_pressed = true;
                square_y_pos -= 2;
                break;
            case "ArrowDown":
                valid_key_pressed = true;
                square_y_pos += 2;
                break;
            case "ArrowLeft":
                valid_key_pressed = true;
                square_x_pos -= 2;
                break;
            case "ArrowRight":
                valid_key_pressed = true;
                square_x_pos += 2;
                break;
            case "Minus":
                valid_key_pressed = true;
                profile_square_scale.value = parseFloat(profile_square_scale.value) - 2;
                square_size = (profile_square_scale.value / 100) * square_size_max;
                break;
            case "Equal":
                valid_key_pressed = true;
                profile_square_scale.value = parseFloat(profile_square_scale.value) + 2;
                square_size = (profile_square_scale.value / 100) * square_size_max;
                break;
            default:
        }

        if (valid_key_pressed) {
            e.preventDefault();
            check_and_set_square_bounds();
            set_profile_square_size();
            set_profile_square_pos();
            draw();
        } 
    })

    function set_profile_square_pos() {
        const aspect_ratio = profile_image.width / profile_image.height;
        profile_square_pos_y.value = ((square_y_pos / profile_canvas.height) * profile_image.height).toString();
        profile_square_pos_x.value = ((square_x_pos / profile_canvas.width) * profile_image.width).toString();

        if (parseFloat(profile_square_size.value) + parseFloat(profile_square_pos_y.value) > profile_image.height) {
            profile_square_pos_y.value = (profile_image.height - parseFloat(profile_square_size.value)).toString();
        }

        if (parseFloat(profile_square_size.value) + parseFloat(profile_square_pos_x.value) > profile_image.width) {
            profile_square_pos_x.value = (profile_image.width - parseFloat(profile_square_size.value)).toString();
        }
    }

    function set_profile_square_size() {
        const profile_square_size_max = Math.min(profile_image.width, profile_image.height);
        profile_square_size.value = ((profile_square_scale.value / 100) * profile_square_size_max).toString();
    }

    function check_and_set_square_bounds() {
        if (square_x_pos <= 0) {
            square_x_pos = 0;
        }
        if (square_y_pos <= 0) {
            square_y_pos = 0;
        }

        if (square_x_pos + square_size >= profile_canvas.width) {
            square_x_pos = profile_canvas.width - square_size;
        }
        if (square_y_pos + square_size >= profile_canvas.height) {
            square_y_pos = profile_canvas.height - square_size;
        }
    }


    function draw() {
        // Gray background
        profile_canvas_context.fillStyle = "grey";
        profile_canvas_context.fillRect(0, 0, profile_canvas.width, profile_canvas.height);

        // Draw Image
        profile_canvas_context.drawImage(profile_image, 0, 0, profile_canvas.width, profile_canvas.height);

        // Selection Square
        profile_canvas_context.strokeStyle = "black";
        profile_canvas_context.lineWidth = 4;
        profile_canvas_context.strokeRect(square_x_pos, square_y_pos, square_size, square_size);
        profile_canvas_context.strokeStyle = "white";
        profile_canvas_context.lineWidth = 2;
        profile_canvas_context.strokeRect(square_x_pos, square_y_pos, square_size, square_size);
    }
</script>
