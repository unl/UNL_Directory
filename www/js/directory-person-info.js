//   ___      _
//  / __| ___| |_ _  _ _ __
//  \__ \/ -_)  _| || | '_ \
//  |___/\___|\__|\_,_| .__/
//                    |_|
// Sets up variables for HTML elements
const profile_input = document.getElementById('profile_input');
const profile_input_error = document.getElementById('profile_input_error');
const profile_editor = document.getElementById('profile_editor');
const profile_canvas = document.getElementById('profile_image');
const profile_image_container = document.getElementById('profile_image_container');
const profile_canvas_context = profile_canvas.getContext('2d');
const profile_square_scale = document.getElementById('profile_square_scale');
const profile_square_pos_x = document.getElementById('profile_square_pos_x');
const profile_square_pos_y = document.getElementById('profile_square_pos_y');
const profile_square_size = document.getElementById('profile_square_size');
const submit_button = document.getElementById('submit_button');

// Sets up variables for HTML elements in dcf-collapsible-fieldset
let profile_square_grid_guides = document.getElementById('profile_square_grid_guides');
let profile_square_center_guides = document.getElementById('profile_square_center_guides');
const guides_fieldset = document.getElementById('guides_fieldset');
guides_fieldset.addEventListener('ready', () => {
    // Contents of the fieldset are copied so we will need to reassign these element's variables
    profile_square_grid_guides = document.getElementById('profile_square_grid_guides');
    profile_square_center_guides = document.getElementById('profile_square_center_guides');

    // Set up event listeners for checking these checkboxes, we want to draw if they are checked
    profile_square_grid_guides.addEventListener('change', draw);
    profile_square_center_guides.addEventListener('change', draw);
});

// Set the default background color for transparency
const default_background_color = "rgb(227, 227, 226)";

// Sets up variables for the canvas
let profile_image = new Image();
let square_size_max = 300;
let square_size = 300;
let square_x_pos = 0;
let square_y_pos = 0;

// Set up variables for pointer events
let pointer_start_x = -1;
let pointer_start_y = -1;

// Set up event listeners for checking these checkboxes, we want to draw if they are checked
// These might be reset if the fieldset was not ready yet
profile_square_grid_guides.addEventListener('change', draw);
profile_square_center_guides.addEventListener('change', draw);

//   ___ _ _       _   _      _              _
//  | __(_) |___  | | | |_ __| |___  __ _ __| |
//  | _|| | / -_) | |_| | '_ \ / _ \/ _` / _` |
//  |_| |_|_\___|  \___/| .__/_\___/\__,_\__,_|
//                      |_|

// Set up event listener for when we upload an image
profile_input.addEventListener('change', () => {

    // Disable/Hide everything while we load the image
    submit_button.setAttribute('disabled', 'disabled');
    profile_editor.classList.add('dcf-d-none');
    profile_input_error.classList.add('dcf-d-none');
    profile_input_error.innerText = "";

    // If there is no file then there is no reason to continue
    if (profile_input.files[0] === undefined) { return; }

    // Validate image size
    if (MAX_FILE_UPLOAD_SIZE !== undefined && profile_input.files[0].size >= MAX_FILE_UPLOAD_SIZE) {
        profile_input_error.classList.remove('dcf-d-none');
        profile_input_error.innerText = "The image you have uploaded exceeds our maximum size, please upload a smaller image.";
        return;
    }

    // Creates a URL to the file uploaded
    let profile_file = profile_input.files[0];
    let profile_image_url = URL.createObjectURL(profile_file);

    // Creates a new image object to put the image in
    profile_image = new Image();

    // Assigns callback once the image is loaded
    profile_image.onload = function () {
        // Destroys the URL for that image file
        URL.revokeObjectURL(profile_image_url);

        // Calculates the aspect ratio of the image
        const aspect_ratio = profile_image.width / profile_image.height;

        // Sets up the canvas size
        profile_canvas.width = 300;
        profile_canvas.height = 300 / aspect_ratio;

        // Sets up the default values on the inputs
        square_size = Math.min(profile_canvas.width, profile_canvas.height);
        square_size_max = Math.min(profile_canvas.width, profile_canvas.height);
        profile_square_scale.value = 100; // This is needed for changing images, not sure why it doesn't work without it
        square_x_pos = 0;
        square_y_pos = 0;

        // Saves those values and draw the image to canvas
        set_profile_square_size();
        set_profile_square_pos();
        draw();

        // Enables/Un-hides inputs once everything has loaded
        profile_editor.classList.remove('dcf-d-none');
        submit_button.removeAttribute('disabled');
        profile_image_container.focus();
    };

    // Assigns the file uploaded's url to the image object
    profile_image.src = profile_image_url;
});

//   ___ _ _    _
//  / __| (_)__| |___ _ _
//  \__ \ | / _` / -_) '_|
//  |___/_|_\__,_\___|_|

// Add event listener for when the slider is moved
profile_square_scale.addEventListener('input', () => {

    // Recalculates the square size
    square_size = (profile_square_scale.value / 100) * square_size_max;

    // If the square is off the side of the image move it to be back on
    check_and_set_square_bounds();

    // Saves the values and re-draws the image
    set_profile_square_size();
    set_profile_square_pos();
    draw();
});

// Add event listener for when the slider is moved
profile_square_scale.addEventListener('change', () => {

    // Recalculates the square size
    square_size = (profile_square_scale.value / 100) * square_size_max;

    // If the square is off the side of the image move it to be back on
    check_and_set_square_bounds();

    // Saves the values and re-draws the image
    set_profile_square_size();
    set_profile_square_pos();
    draw();
});

//   ___     _     _             ___             _
//  | _ \___(_)_ _| |_ ___ _ _  | __|_ _____ _ _| |_ ___
//  |  _/ _ \ | ' \  _/ -_) '_| | _|\ V / -_) ' \  _(_-<
//  |_| \___/_|_||_\__\___|_|   |___|\_/\___|_||_\__/__/

// Add event listener for mouse down
profile_canvas.addEventListener('mousedown', (e) => {
    // Sets the starting x,y point
    pointer_start_x = e.clientX;
    pointer_start_y = e.clientY;
});

// Add event listener for touch start
profile_canvas.addEventListener('touchstart', (e) => {
    // Gets the first touch target
    if (e.targetTouches.length === 0 && e.targetTouches.length > 1) { return; }

    // Sets the starting x,y point
    pointer_start_x = e.targetTouches[0].clientX;
    pointer_start_y = e.targetTouches[0].clientY;

    // We do not want this to scroll the page
    e.preventDefault();
});

// Add event listener for mouse move
profile_canvas.addEventListener('mousemove', (e) => {
    // If the pointer start is not set we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }

    // Find how much the mouse has moved
    let x_diff = pointer_start_x - e.clientX;
    let y_diff = pointer_start_y - e.clientY;

    // Move the square that much
    square_x_pos -= x_diff;
    square_y_pos -= y_diff;

    // If the square is off the side of the canvas move it back on
    check_and_set_square_bounds();

    // Resets the starting point
    pointer_start_x = e.clientX;
    pointer_start_y = e.clientY;

    // Re-draws the canvas with the new square position
    draw();
});

// Add event listener for touch move
profile_canvas.addEventListener('touchmove', (e) => {
    // If the pointer start is not set we do not need to continue
    // If there is no touch target or more than one then we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    if (e.targetTouches.length === 0 && e.targetTouches.length > 1) { return; }

    // Find how much the touch target has moved
    let x_diff = pointer_start_x - e.targetTouches[0].clientX;
    let y_diff = pointer_start_y - e.targetTouches[0].clientY;

    // Move the square that much
    square_x_pos -= x_diff;
    square_y_pos -= y_diff;

    // If the square is off the side of the canvas move it back on
    check_and_set_square_bounds();

    // Resets the starting point
    pointer_start_x = e.targetTouches[0].clientX;
    pointer_start_y = e.targetTouches[0].clientY;

    // Re-draws the canvas with the new square position
    draw();

    // We do not want this to scroll the page
    e.preventDefault();
});

// Add event listener for when the mouse leaves the canvas
profile_canvas.addEventListener('mouseleave', (e) => {
    // If the pointer start is not set we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }

    // Resets the pointer start location
    pointer_start_x = -1;
    pointer_start_y = -1;

    // Saves the new square position
    set_profile_square_pos();
});

// Add event listener for when the touch target leaves the canvas
profile_canvas.addEventListener('touchcancel', (e) => {
    // If the pointer start is not set we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }

    // Resets the pointer start location
    pointer_start_x = -1;
    pointer_start_y = -1;

    // Saves the new square position
    set_profile_square_pos();

    // We do not want to scroll the page
    e.preventDefault();
});

// Add event listener for mouse up
profile_canvas.addEventListener('mouseup', (e) => {
    // If the pointer start is not set we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }

    // Resets the pointer start location
    pointer_start_x = -1;
    pointer_start_y = -1;

    // Saves the new square position
    set_profile_square_pos();
});

// Add event listener for touch end
profile_canvas.addEventListener('touchend', (e) => {
    // If the pointer start is not set we do not need to continue
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }

    // Resets the pointer start location
    pointer_start_x = -1;
    pointer_start_y = -1;

    // Saves the new square position
    set_profile_square_pos();

    // We do not want to scroll the page
    e.preventDefault();
});

//   _  __         _                      _   ___             _
//  | |/ /___ _  _| |__  ___  __ _ _ _ __| | | __|_ _____ _ _| |_ ___
//  | ' </ -_) || | '_ \/ _ \/ _` | '_/ _` | | _|\ V / -_) ' \  _(_-<
//  |_|\_\___|\_, |_.__/\___/\__,_|_| \__,_| |___|\_/\___|_||_\__/__/
//            |__/

// Add event listener for keydown
profile_image_container.addEventListener('keydown', (e) => {
    // Add check variable for is the keypress was a valid key
    let valid_key_pressed = false;

    // Figures out which key was pressed
    switch (e.code) {
        // Move square up
        case "ArrowUp":
            valid_key_pressed = true;
            square_y_pos -= 2;
            break;
        // Move square down
        case "ArrowDown":
            valid_key_pressed = true;
            square_y_pos += 2;
            break;
        // Move square left
        case "ArrowLeft":
            valid_key_pressed = true;
            square_x_pos -= 2;
            break;
        // Move square right
        case "ArrowRight":
            valid_key_pressed = true;
            square_x_pos += 2;
            break;
        // Shrink square
        case "Minus":
            valid_key_pressed = true;
            profile_square_scale.value = parseFloat(profile_square_scale.value) - 2;
            square_size = (profile_square_scale.value / 100) * square_size_max;
            break;
        // Grow square
        case "Equal":
            valid_key_pressed = true;
            profile_square_scale.value = parseFloat(profile_square_scale.value) + 2;
            square_size = (profile_square_scale.value / 100) * square_size_max;
            break;
        default:
    }

    // If it was a valid key press then save the values and redraw
    if (valid_key_pressed) {
        // We do not want the keypress to scroll the page
        e.preventDefault();
        check_and_set_square_bounds();
        set_profile_square_size();
        set_profile_square_pos();
        draw();
    }
})

//   ___           _            __   __    _
//  / __| __ ___ _(_)_ _  __ _  \ \ / /_ _| |_  _ ___ ___
//  \__ \/ _` \ V / | ' \/ _` |  \ V / _` | | || / -_|_-<
//  |___/\__,_|\_/|_|_||_\__, |   \_/\__,_|_|\_,_\___/__/
//                       |___/

/**
 * Sets the square position values
 * @return void
 */
function set_profile_square_pos() {
    // Sets the x,y position of the square in the HTML inputs
    profile_square_pos_y.value = ((square_y_pos / profile_canvas.height) * profile_image.height).toString();
    profile_square_pos_x.value = ((square_x_pos / profile_canvas.width) * profile_image.width).toString();

    // Because floats are stupid we need to check that we are not bigger than the size of the image
    // This takes into account of the size of the square
    if (parseFloat(profile_square_size.value) + parseFloat(profile_square_pos_y.value) > profile_image.height) {
        profile_square_pos_y.value = (profile_image.height - parseFloat(profile_square_size.value)).toString();
    }
    if (parseFloat(profile_square_size.value) + parseFloat(profile_square_pos_x.value) > profile_image.width) {
        profile_square_pos_x.value = (profile_image.width - parseFloat(profile_square_size.value)).toString();
    }
}

/**
 * Sets the square size value
 * @return void
 */
function set_profile_square_size() {
    // Calculates the max size and sets the value based off that
    const profile_square_size_max = Math.min(profile_image.width, profile_image.height);
    profile_square_size.value = ((profile_square_scale.value / 100) * profile_square_size_max);
}

//   ___                   _
//  | _ ) ___ _  _ _ _  __| |___
//  | _ \/ _ \ || | ' \/ _` (_-<
//  |___/\___/\_,_|_||_\__,_/__/

// If the square is off the edge of the canvas put it back on there
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

//   ___
//  |   \ _ _ __ ___ __ __
//  | |) | '_/ _` \ V  V /
//  |___/|_| \__,_|\_/\_/

// Re-draws the canvas
function draw() {
    // We want to draw around the square
    const outerX = square_x_pos - 4;
    const outerY = square_y_pos - 4;
    const outerSize = square_size + 8;

    // Gray background
    profile_canvas_context.fillStyle = default_background_color;
    profile_canvas_context.fillRect(0, 0, profile_canvas.width, profile_canvas.height);

    // Draw Image
    profile_canvas_context.drawImage(profile_image, 0, 0, profile_canvas.width, profile_canvas.height);

    // Selection Square, we do both black and white to maximize visibility no matter the image below
    profile_canvas_context.strokeStyle = "black";
    profile_canvas_context.lineWidth = 4;
    profile_canvas_context.strokeRect(outerX, outerY, outerSize, outerSize);

    // Diagonal lines corner to corner
    if (profile_square_center_guides.checked) {
        profile_canvas_context.beginPath();
        profile_canvas_context.moveTo(outerX, outerY);
        profile_canvas_context.lineTo(outerX + outerSize, outerY + outerSize);
        profile_canvas_context.moveTo(outerX, outerY + outerSize);
        profile_canvas_context.lineTo(outerX + outerSize, outerY);
        profile_canvas_context.stroke();
    }

    // Grid lines vertical and horizontal
    if (profile_square_grid_guides.checked) {
        profile_canvas_context.beginPath();
        profile_canvas_context.moveTo(outerX + (outerSize / 3), outerY);
        profile_canvas_context.lineTo(outerX + (outerSize / 3), outerY + outerSize);
        profile_canvas_context.moveTo(outerX + (2 * (outerSize / 3)), outerY);
        profile_canvas_context.lineTo(outerX + (2 * (outerSize / 3)), outerY + outerSize);
        profile_canvas_context.moveTo(outerX, outerY + (outerSize / 3));
        profile_canvas_context.lineTo(outerX + outerSize, outerY + (outerSize / 3));
        profile_canvas_context.moveTo(outerX, outerY + (2 * (outerSize / 3)));
        profile_canvas_context.lineTo(outerX + outerSize, outerY + (2 * (outerSize / 3)));
        profile_canvas_context.stroke();
    }

    // Selection Square, we do both black and white to maximize visibility no matter the image below
    profile_canvas_context.strokeStyle = "white";
    profile_canvas_context.lineWidth = 2;
    profile_canvas_context.strokeRect(outerX, outerY, outerSize, outerSize);

    // Diagonal lines corner to corner
    if (profile_square_center_guides.checked) {
        profile_canvas_context.beginPath();
        profile_canvas_context.moveTo(outerX, outerY);
        profile_canvas_context.lineTo(outerX + outerSize, outerY + outerSize);
        profile_canvas_context.moveTo(outerX, outerY + outerSize);
        profile_canvas_context.lineTo(outerX + outerSize, outerY);
        profile_canvas_context.stroke();
    }

    // Grid lines vertical and horizontal
    if (profile_square_grid_guides.checked) {
        profile_canvas_context.beginPath();
        profile_canvas_context.moveTo(outerX + (outerSize / 3), outerY);
        profile_canvas_context.lineTo(outerX + (outerSize / 3), outerY + outerSize);
        profile_canvas_context.moveTo(outerX + (2 * (outerSize / 3)), outerY);
        profile_canvas_context.lineTo(outerX + (2 * (outerSize / 3)), outerY + outerSize);
        profile_canvas_context.moveTo(outerX, outerY + (outerSize / 3));
        profile_canvas_context.lineTo(outerX + outerSize, outerY + (outerSize / 3));
        profile_canvas_context.moveTo(outerX, outerY + (2 * (outerSize / 3)));
        profile_canvas_context.lineTo(outerX + outerSize, outerY + (2 * (outerSize / 3)));
        profile_canvas_context.stroke();
    }
}
