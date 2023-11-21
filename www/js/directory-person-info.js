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

let profile_square_grid_guides = document.getElementById('profile_square_grid_guides');
let profile_square_center_guides = document.getElementById('profile_square_center_guides');
const guides_fieldset = document.getElementById('guides_fieldset');
guides_fieldset.addEventListener('ready', () => {
    profile_square_grid_guides = document.getElementById('profile_square_grid_guides');
    profile_square_center_guides = document.getElementById('profile_square_center_guides');
    profile_square_grid_guides.addEventListener('change', draw);
    profile_square_center_guides.addEventListener('change', draw);
});

const default_background_color = "rgb(227, 227, 226)";

let profile_image = new Image();
let square_size_max = 300;
let square_size = 300;
let square_x_pos = 0;
let square_y_pos = 0;

let pointer_start_x = -1;
let pointer_start_y = -1;

profile_square_grid_guides.addEventListener('change', draw);
profile_square_center_guides.addEventListener('change', draw);

profile_input.addEventListener('change', () => {
    submit_button.setAttribute('disabled', 'disabled');
    profile_editor.classList.add('dcf-d-none');
    profile_input_error.classList.add('dcf-d-none');
    profile_input_error.innerText = "";

    if (profile_input.files[0] === undefined) { return; }

    if (MAX_FILE_UPLOAD_SIZE !== undefined && profile_input.files[0].size >= MAX_FILE_UPLOAD_SIZE) {
        profile_input_error.classList.remove('dcf-d-none');
        profile_input_error.innerText = "The image you have uploaded exceeds our maximum size, please upload a smaller image.";
        return;
    }

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
        profile_square_scale.value = 100; // This is needed for changing images, not sure why it doesn't work without it
        square_x_pos = 0;
        square_y_pos = 0;

        set_profile_square_size();
        set_profile_square_pos();
        draw();

        profile_editor.classList.remove('dcf-d-none');
        submit_button.removeAttribute('disabled');
        profile_image_container.focus();
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

profile_canvas.addEventListener('touchstart', (e) => {
    if (e.targetTouches.length === 0 && e.targetTouches.length > 1) { return; }

    pointer_start_x = e.targetTouches[0].clientX;
    pointer_start_y = e.targetTouches[0].clientY;

    e.preventDefault();
});

profile_canvas.addEventListener('mousemove', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    let x_diff = pointer_start_x - e.clientX;
    let y_diff = pointer_start_y - e.clientY;

    square_x_pos -= x_diff;
    square_y_pos -= y_diff;

    check_and_set_square_bounds();

    pointer_start_x = e.clientX;
    pointer_start_y = e.clientY;

    draw();
});
profile_canvas.addEventListener('touchmove', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    if (e.targetTouches.length === 0 && e.targetTouches.length > 1) { return; }

    let x_diff = pointer_start_x - e.targetTouches[0].clientX;
    let y_diff = pointer_start_y - e.targetTouches[0].clientY;

    square_x_pos -= x_diff;
    square_y_pos -= y_diff;

    check_and_set_square_bounds();

    pointer_start_x = e.targetTouches[0].clientX;
    pointer_start_y = e.targetTouches[0].clientY;

    draw();
    e.preventDefault();
});

profile_canvas.addEventListener('mouseleave', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    pointer_start_x = -1;
    pointer_start_y = -1;

    set_profile_square_pos();
});
profile_canvas.addEventListener('touchcancel', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    pointer_start_x = -1;
    pointer_start_y = -1;

    set_profile_square_pos();
    e.preventDefault();
});

profile_canvas.addEventListener('mouseup', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    pointer_start_x = -1;
    pointer_start_y = -1;

    set_profile_square_pos();
});
profile_canvas.addEventListener('touchend', (e) => {
    if (pointer_start_x === -1 || pointer_start_y === -1) { return; }
    pointer_start_x = -1;
    pointer_start_y = -1;

    set_profile_square_pos();
    e.preventDefault();
});

profile_image_container.addEventListener('keydown', (e) => {
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
    profile_square_size.value = ((profile_square_scale.value / 100) * profile_square_size_max);
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
    // We want to draw around the square
    const outerX = square_x_pos - 4;
    const outerY = square_y_pos - 4;
    const outerSize = square_size + 8;

    // Gray background
    profile_canvas_context.fillStyle = default_background_color;
    profile_canvas_context.fillRect(0, 0, profile_canvas.width, profile_canvas.height);

    // Draw Image
    profile_canvas_context.drawImage(profile_image, 0, 0, profile_canvas.width, profile_canvas.height);

    // Selection Square
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
