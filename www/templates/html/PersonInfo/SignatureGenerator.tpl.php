<style>
    /* CONFIG FOR SIGNATURE GENERATOR */
    /* Color Config */
    :root {
        --signature-bg-light-gray: var(--bg-light-gray);
        --signature-bg-lighter-gray: var(--bg-lighter-gray);
        --signature-b-light-gray: var(--b-light-gray);

        --signature-preview-background: var(--badge);
        --signature-preview-border: var(--b-fieldset);

        --signature-sortable-border: var(--darkest-gray);
        --signature-sortable-current-border: var(--bg-brand-alpha);

        --signature-popup-message-error: var(--bg-brand-alpha);
        --signature-popup-message-success: var(--bg-brand-zeta);
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --signature-bg-light-gray: var(--bg-light-gray);
            --signature-bg-lighter-gray: var(--bg-lighter-gray);
            --signature-b-light-gray: var(--b-light-gray);

            --signature-preview-background: var(--bg-badge);
            --signature-preview-border: var(--b-fieldset);

            --signature-sortable-border: var(--darkest-gray);
            --signature-sortable-current-border: var(--bg-brand-alpha);

            --signature-popup-message-error: var(--bg-brand-alpha);
            --signature-popup-message-success: var(--bg-brand-zeta);
        }
    }

    /* Utilities */
    .signature-height-min-content {
        height: min-content;
    }
    .signature-bg-light-grey {
        background-color: var(--signature-bg-light-gray);
    }
    .signature-bg-lighter-grey {
        background-color: var(--signature-bg-lighter-gray);
    }
    .signature-b-light-grey {
        background-color: var(--signature-b-light-gray);
    }

    /* Signature Section */
    .signature-section {
        background-color: var(--signature-preview-background);
        height: min-content;
        width: 100%;
        border: solid 1px var(--signature-preview-border);
    }

    .signature-section a{
        color: revert;
    }
    .signature-section a:hover{
        color: revert;
    }
    .signature-section a:visited{
        color: revert;
    }

    /* Drag And Drop */
    .sortable-field {
        display: grid;
        grid-template-columns: 1fr auto;
        border: dashed 2px var(--signature-sortable-border);
        cursor: move;
    }

    .sortable-field input {
        width: calc(100% - 1em) !important;
    }

    .sortable-field.currently_dragging {
        border: dashed 2px var(--signature-sortable-current-border);
    }

    /* Popup Messages */
    .signature-copy-message,
    #username-error {
        color: #fff;
        width: fit-content;
        opacity: 1;
        position: absolute;
        bottom: 100%;
        left: -5%;
    }
    .signature-copy-message:before,
    #username-error:before {
        top: calc(100% - 0.5rem);
        left: 15%;
        rotate: 45deg;
        width: 1rem;
        content: "";
        position: absolute;
        height: 1rem;
        z-index: 3;
    }

    .signature-copy-message,
    .signature-copy-message:before {
        background-color: var(--signature-popup-message-success);
    }

    #username-error,
    #username-error:before {
        background-color: var(--signature-popup-message-error);
    }

    .signature-copy-message[aria-hidden="true"],
    #username-error[aria-hidden="true"] {
        opacity: 0;
        transition: opacity 500ms linear 1000ms;
    }
</style>

<!-- Email Lookup Start -->
<div class="dcf-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw dcf-row-gap-5 dcf-mb-8">
    <ol class="dcf-mb-0">
        <li>
            Enter your My.UNL username (your login username) to start.
            <form class="dcf-form dcf-pt-4 dcf-pb-4 dcf-relative">
                <label for="username-to-look-up">Username</label>
                <div class="dcf-input-group" style="max-width: 40ch;">
                    <input id="username-to-look-up" name="person-data" placeholder="hhusker1" type="text">
                    <button id="start" class="dcf-btn dcf-btn-primary" type="button">Start</button>
                </div>
                <div id="username-error" class="dcf-rounded dcf-p-2 dcf-mt-2" aria-hidden="true" aria-live="polite">
                    Sorry this username does not exist
                </div>
            </form>
        </li>
        <li>
            Edit your information in the boxes on the right. Make sure
            <code>mailto:</code> is added before all email addresses and <code>https://</code> is added
            before all web addresses. Reorder the boxes drag and drop, or use the arrow buttons.
        </li>
        <li>
            Click the “copy your signature” button and your signature will be
            automatically copied.
            <div class="dcf-pt-4 dcf-pb-4 dcf-relative">
                <button class="signature-copy-btn dcf-btn dcf-btn-primary">Copy Your Signature</button>
                <div class="signature-copy-message dcf-rounded dcf-p-2 dcf-mt-2" aria-hidden="true" aria-live="polite">
                    Your signature has been copied!
                </div>
            </div>
        </li>
        <li class="dcf-mb-0">
            Copy and paste the highlighted signature into the signature
            preferences in your mail client (i.e. Outlook, Mac Mail, etc.).
            Be mindful that many mail clients will reformat text pasted into
            them. Make sure your client is set to “Keep Source Formatting.”
        </li>
    </ol>
    <figure class="signature-height-min-content">
        <img class="dcf-b-1 dcf-b-solid signature-b-light-grey" src="https://ucomm.unl.edu/images/email-signature/keepSource.jpg" alt="Keep Source Formatting Example Screenshot." />
        <figcaption>“Keep Source Formatting” example (Outlook for Mac).</figcaption>
    </figure>
</div>
<!-- Email Lookup End -->

<div class="dcf-d-none dcf-mb-8" id="signature-builder">
    <hr>

    <h2>Signature Details</h2>

    <div class="dcf-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw dcf-row-gap-4 dcf-mb-8">
        <div class="dcf-2nd dcf-1st@md">
            <form class="dcf-form">
                <div class="dcf-form-group">
                    <label for="logo-select"> Signature Logo </label>
                    <select id="logo-select">
                        <optgroup id="university-logo-options" label='University'></optgroup>
                        <optgroup id="affiliates-logo-options" label='Affiliates'></optgroup>
                    </select>
                </div>
                <fieldset aria-describedby="fields-fieldset-help">
                    <legend>Fields</legend>
                    <div class="dcf-d-flex dcf-flex-row dcf-ai-center dcf-gap-4 dcf-mb-5">
                        <p id="fields-fieldset-help" class="dcf-mb-0">Drag and drop, or use the arrow buttons to reorder.</p>
                        <button id="new-row" class="dcf-btn dcf-btn-primary dcf-flex-shrink-0" type="button">Append a New Field</button>
                    </div>
                    <ul id="sortable-field-collection" class="dcf-list-bare"></ul>
                </fieldset>
            </form>
        </div>

        <div class="dcf-1st dcf-2nd@md">
            <div class="dcf-sticky" style="top: 5rem;">
                <div class="signature-section dcf-d-flex dcf-jc-center dcf-ai-center dcf-pt-7 dcf-pb-5 dcf-pl-2 dcf-pr-2 dcf-rounded">
                    <table border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:none;width:auto;background-color:transparent;text-align:left;margin:0;">
                        <tbody>
                            <tr>
                                <td valign="top" style="border:none;border-top:solid #b52929 2px;padding:18px">
                                    <img src="" alt="" id="sig-logo" style="display: block;">
                                </td>
                                <td id="contactInfo" valign="top" style="border:none;padding:15px 18px 18px 0;border-top:solid #D00000 2px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="dcf-pt-4 dcf-relative">
                    <button class="signature-copy-btn dcf-btn dcf-btn-primary">Copy Your Signature</button>
                    <div class="signature-copy-message dcf-rounded dcf-p-2 dcf-mt-2" aria-hidden="true" aria-live="polite">
                        Your signature has been copied!
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="dcf-bleed dcf-wrapper dcf-d-flex dcf-jc-center dcf-pt-7 dcf-pb-7 signature-bg-light-grey">
    <div class="dcf-w-max-lg dcf-txt-center">
        <p class="dcf-mb-0">
            <small class="dcf-txt-sm">This application is developed and maintained
                by <a href="https://ucomm.unl.edu/dxg">Digital Experience Group</a>.
                Please submit issues to <a href="mailto:support@nebraska.edu">support@nebraska.edu</a>
            </small>
        </p>
    </div>
</div>

<template id="sortable-field-template">
    <li class="sortable-field dcf-p-2 dcf-rounded" draggable="true">
        <div>
            <div class="dcf-form-group">
                <label class="label-text-value">Field Text</label>
                <input class="field-text-value" type="text">
            </div>
            <div class="dcf-form-group dcf-mb-0">
                <label class="label-text-url">Field URL</label>
                <input class="field-text-url" type="text">
            </div>
        </div>
        <div class="dcf-d-flex dcf-flex-col dcf-jc-between">
            <button class="delete-field-btn dcf-btn dcf-btn-secondary dcf-p-2" aria-label="Delete Field" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="dcf-fill-current dcf-w-4 dcf-h-4 dcf-d-block" viewBox="0 0 25 25" >
                    <path d="M24.6,22.4l-9.9-9.9l9.9-9.9C24.8,2.3,25,1.9,25,1.5s-0.2-0.8-0.4-1.1c-0.6-0.6-1.6-0.6-2.1,0l-9.9,9.9L2.6,0.4
                        C2-0.1,1-0.1,0.4,0.4C-0.1,1-0.1,2,0.4,2.6l9.9,9.9l-9.9,9.9C0.2,22.7,0,23.1,0,23.5s0.2,0.8,0.4,1.1C0.7,24.9,1.1,25,1.5,25
                        c0.4,0,0.8-0.1,1.1-0.4l9.9-9.9l9.9,9.9c0.3,0.3,0.7,0.4,1.1,0.4c0,0,0,0,0,0c0.4,0,0.8-0.2,1.1-0.4c0.3-0.3,0.4-0.7,0.4-1.1
                        C25,23.1,24.8,22.7,24.6,22.4z"/>
                </svg>
            </button>
            <div class="dcf-d-flex dcf-flex-col dcf-row-gap-3">
                <button class="up-field-btn dcf-btn dcf-btn-secondary dcf-p-2" aria-label="Move Field Up" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="dcf-fill-current dcf-w-4 dcf-h-4 dcf-d-block" viewBox="0 0 25 26">
                        <path d="M24.6,13L13.7,0.6l-0.1-0.2l-0.1,0c-0.5-0.4-1.4-0.4-1.9,0
                            L0.4,13C0.1,13.3,0,13.7,0,14.1c0,0.4,0.2,0.8,0.5,1
                            c0.6,0.5,1.6,0.5,2.1-0.1L11,5.5v19c0,0.8,0.7,1.5,1.5,1.5
                            s1.5-0.7,1.5-1.5v-19l8.4,9.5c0.5,0.6,1.5,0.7,2.1,0.1
                            c0.3-0.3,0.5-0.6,0.5-1C25,13.7,24.9,13.3,24.6,13z"/>
                    </svg>
                </button>
                <button class="down-field-btn dcf-btn dcf-btn-secondary dcf-p-2" aria-label="Move Field Down" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="dcf-fill-current dcf-w-4 dcf-h-4 dcf-d-block" viewBox="0 0 24 24">
                        <svg xmlns="http://www.w3.org/2000/svg" class="dcf-fill-current dcf-w-4 dcf-h-4 dcf-d-block" viewBox="0 0 25 26">
                            <path d="M24.5,10.9c-0.3-0.3-0.7-0.4-1.1-0.4c-0.4,0-0.8,0.2-1,0.5
                                L14,20.5v-19C14,0.7,13.3,0,12.5,0S11,0.7,11,1.5v19L2.6,11
                                c-0.3-0.3-0.6-0.5-1-0.5c-0.4,0-0.8,0.1-1.1,0.4c-0.3,0.3-0.5,0.6-0.5,1
                                c0,0.4,0.1,0.8,0.4,1.1l11,12.4l0.1,0.2l0.1,0.1
                                c0,0,0.1,0.1,0.2,0.1l0.2,0.1c0.2,0.1,0.4,0.1,0.6,0.1
                                c0.2,0,0.4,0,0.6-0.1l0.2-0.1c0.1,0,0.1-0.1,0.2-0.1L24.6,13
                                c0.3-0.3,0.4-0.7,0.4-1.1C25,11.5,24.8,11.1,24.5,10.9z"/>
                        </svg>
                </button>
            </div>
        </div>
    </li>
</template>

<template id="signature-primary-field-template">
    <p style="margin:0px 2px 2px 2px;font-family: Helvetica, Arial;color: #454545;font-size:16px;line-height: 18px;"></p>
</template>

<template id="signature-secondary-field-template">
    <p style="font-size:12px;margin:2px;font-style:italic;font-family: Georgia;color: #454545;line-height: 14px;"></p>
</template>

<template id="signature-field-template">
    <p style="font-size:10px;margin:2px;font-family: Helvetica, Arial;color: #454545;line-height: 14px;"></p>
</template>

<script>
    const default_person_to_load = '<?php echo $context->getUser(); ?>';
    const logo_url = '<?php echo UNl_Peoplefinder::$url . 'images/logos/'; ?>';
</script>

<script type="text/javascript" defer>
    // CONFIG FOR SIGNATURE GENERATOR
    // This will control the options and flow for signature generator
    const config = {
        // (optional) this is the URI for UNL's directory. All fields are returned as arrays. if this is not provided default data will be used. {{user}} is replaced with what is entered in input box.
        api: 'https://directory.unl.edu/people/{{user}}.json',

        // (optional) this function is called just before the data is used to generate the signature. You can use it to manipulate data. Here is an example removing the 's' from 'Communications'. You could potentially use it to shim an API that doesn't return data in Arrays.
        dataTransforms: (data) => {
            if(data.unlHRPrimaryDepartment){
                data.unlHRPrimaryDepartment[0] = data.unlHRPrimaryDepartment[0].replace("Office of University Communications", "Office of University Communication");
                data.unlHRPrimaryDepartment[0] = data.unlHRPrimaryDepartment[0].replace("Hixson-Lied Fine & Performing Arts", "College of Fine and Performing Arts");
            }
            return data;
        },
      
        // (Optional) This function will parse the username input value and get the user's id. This will then be used in the API
        userTransforms: (data) => {
            return data.split('@')[0]
        },
        logos: [
            {
                name: 'N-(UNL Icon)',
                alt: 'University of Nebraska &ndash; Lincoln',
                color: '#D00000',
                logo: logo_url + 'Nebraska_N_RGB_small_on_white.gif',
                optgroup: 'university'
            },
            {
                name: 'N-Grit/Glory',
                alt: 'In Our GRIT Our GLORY',
                color: '#D00000',
                logo: logo_url + 'Nebrasla_N_GritGlory_Stacked_RGB.gif',
                optgroup: 'university'
            },
            {
                name: 'N-Museum',
                alt: 'University of Nebraska State Museum',
                color: '#2b5135',
                logo: logo_url + 'unsm.gif',
                optgroup: 'affiliates'
            },
            {
                name: 'NFS',
                alt: 'Nebraska Forest Service',
                color: '#D00000',
                logo: logo_url + 'nfs.gif',
                optgroup: 'affiliates'
            },
            {
                name: '4H',
                alt: '4H',
                color: '#D00000',
                logo: logo_url + '4h.gif',
                optgroup: 'affiliates'
            },
            {
                name: 'Alumni Association',
                alt: 'Alumni Association',
                color: '#D00000',
                logo: logo_url + 'Alumni_Association.png',
                optgroup: 'affiliates'
            },
            {
                name: 'Alumni Association 150 Years',
                alt: 'Alumni Association 150 Years',
                color: '#D00000',
                logo: logo_url + 'NAA_150Years_RGB_4.png',  
                optgroup: 'affiliates'
            },
        ],
        defaultData: { // (required) Data to be used if API does not return a field or if no API is provided.
            // use "fieldName: null" if a field should be omitted if not provided by the API. Otherwise the field is treated as fallback content.
            // Object keys are used to determine which fields should be pulled from the API.
            displayName:["Herbie W. Husker"],
            title:["Employee Title"],
            organizationName: ["University of Nebraska&ndash;Lincoln"],
            unlHRPrimaryDepartment:["Department Name"],
            unlHRAddress:["Lincoln, NE"],
            telephoneNumber:["(555) 555-5555"]
        }
    };

    // Regex for detecting phone numbers
    const telephoneReg = /^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/;

    // Variables for the signature output
    const sig_logo_element = document.getElementById('sig-logo');
    const contact_info_element = document.getElementById('contactInfo');
    const signature_primary_field_template = document.getElementById('signature-primary-field-template');
    const signature_secondary_field_template = document.getElementById('signature-secondary-field-template');
    const signature_field_template = document.getElementById('signature-field-template');

    // Variables for the fields
    const signature_builder = document.getElementById('signature-builder');
    const field_collection = document.getElementById('sortable-field-collection');
    const field_template_element = document.getElementById('sortable-field-template');
    const add_field_btn = document.getElementById('new-row');

    // Sets up event listeners and variables for drag and drop
    let selected_field = null;
    field_collection.addEventListener('drop', (e) => {
        e.preventDefault();
    });
    field_collection.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = "move";
    });

    // Add event listener for new row
    add_field_btn.addEventListener('click', () => {
        add_new_field("");
        render_signature();
        Array.from(document.querySelectorAll('.sortable-field'))?.at(-1)?.scrollIntoView?.();
    });

    // Set up variables for logo select and adds the options
    const logo_select = document.getElementById('logo-select');
    const logo_select_group_university = document.getElementById('university-logo-options');
    const logo_select_group_affiliates = document.getElementById('affiliates-logo-options');
    config.logos.forEach((logo) => {

        // Create new option for the
        const new_option = document.createElement('option');
        new_option.innerText = logo.name;
        new_option.value = logo.logo;
        new_option.dataset.color = logo.color;
        new_option.dataset.altText = logo.alt;

        // Append it to the right section
        if (logo.optgroup === "university") {
            logo_select_group_university.append(new_option);
        } else {
            logo_select_group_affiliates.append(new_option);
        }
    });

    // Re-render signature when logo is changed
    logo_select.addEventListener('input', () => {
        render_signature();
    });

    // Add event listeners
    const copy_buttons = document.querySelectorAll('.signature-copy-btn');
    copy_buttons.forEach((single_copy_button) => {
        single_copy_button.addEventListener('click', () => {

            // Ignore if signature is not set
            if (signature_builder.classList.contains('dcf-d-none')) {
                return;
            }

            // Select the text and execute a copy
            select_text(".signature-section");
            const copied = document.execCommand("copy");
            if(!copied){
                // if we get an error then tell them to do the copy themselves
                confirm("The copy button is not supported in your browser. You Signature has been selected. Please manually copy with cmd/ctrl+C. ");
            } else {
                // if no error then we clear selection
                clear_select();
            }
        });
    });

    // Listen for copy commands and if we are trying to copy the signature then call copy signature
    window.addEventListener('copy', (e) => {
        if (e.target.closest('.signature-section') === null) {
            return;
        }
        copy_signature(e);
        e.preventDefault();
    })

    // INIT the control flow
    if (config.api !== undefined) {
        // Get the html inputs and set up their event listeners
        const username_input = document.getElementById('username-to-look-up');
        const start_button = document.getElementById('start');

        // If the username param is set auto load the user
        const params = new URLSearchParams(document.location.search);
        const username_param = params.get("username");
        if (username_param !== null) {
            load_user(username_param);
        }

        //Load the user if they are already logged in
        if (default_person_to_load !== null) {
            load_user(default_person_to_load);
        }

        // Load the user when they submit the UID
        username_input.addEventListener('keydown', (e) => {
            if (e.code === "Enter") {
                let username = username_input.value;

                // Call config transform user
                if(config.userTransforms !== undefined){0
                    username = config.userTransforms(username);
                }

                load_user(username);
                e.preventDefault();
            }
        });
        start_button.addEventListener('click', (e) => {
            let username = username_input.value;

            // Call config transform user
            if(config.userTransforms !== undefined){
                username = config.userTransforms(username);
            }

            load_user(username);
        });
    } else {

        // Use default data if no API to call
        parse_user_data(config.defaultData);
    }

    /**
     * Calls the config API and sends user data to be parsed
     * @param { string } user_to_look_up
     */
    async function load_user(user_to_look_up) {
        // Formats the URL and calls it
        const api_url = config.api.replace('{{user}}', user_to_look_up);
        const response = await fetch(api_url);

        // If we get the data back we can parse it
        if (response.ok) {
            const user_json = await response.json();
            parse_user_data(user_json);
        } else {

            // If not we show the error message
            const username_error = document.getElementById('username-error');
            username_error.classList.remove('dcf-d-none');
            setTimeout( () => {
                username_error.classList.add('dcf-d-none');
            }, 2000);
        }
    }

    /**
     * Parse the user data and sends calls to create fields
     * @param { object } user_data
     */
    function parse_user_data (user_data) {

        // Call config transform data
        if(config.dataTransforms !== undefined){
            user_data = config.dataTransforms(user_data);
        }

        // Clears previous fields
        field_collection.innerHTML = "";

        // Loop through all the default data and see if the incoming data has that value
        for (const key in config.defaultData) {
            let field_text = user_data[key]?.[0] || user_data[key] || config.defaultData[key];
            let field_url = "";

            // If it is a phone number then set the URL to a telephone link
            if (telephoneReg.test(field_text)) {
                field_url = `tel:${field_text}`;
            }

            // Creates the new field
            add_new_field(field_text, field_url);
        }

        // Re-render the signature and show the builder
        render_signature();
        signature_builder.classList.remove('dcf-d-none');
        signature_builder.scrollIntoView();
    }

    /**
     * Creates a new field and adds it to the builder
     * @param {string} field_text
     * @param {string} field_url
     */
    function add_new_field(field_text, field_url="") {

        // Creates a copy of the field template
        const field_template = field_template_element.content.children[0].cloneNode(true);

        // Sets up the variables
        const field_template_id = generate_UUID();
        const field_text_value = field_template.querySelector(".field-text-value");
        const field_text_url = field_template.querySelector(".field-text-url");
        const label_text_value = field_template.querySelector(".label-text-value");
        const label_text_url = field_template.querySelector(".label-text-url");
        const delete_field_btn = field_template.querySelector(".delete-field-btn");

        // Sets id of the field
        field_template.id = field_template_id;

        // Sets values of the field text
        field_text_value.id = field_template_id + '_text';
        field_text_value.value = field_text;
        label_text_value.setAttribute('for', field_text_value.id);

        // Sets values of the field url
        field_text_url.id = field_template_id + '_url';
        field_text_url.value = field_url;
        label_text_url.setAttribute('for', field_text_url.id);

        // Adds it to the list of fields
        field_collection.append(field_template);

        // If the inputs are changed we will re-render
        field_template.addEventListener('input', () => {
            render_signature();
        }, true);

        // If we click the delete button then remove the element and re-render
        delete_field_btn.addEventListener('click', () => {
            field_template.remove();
            render_signature();
        });

        // Set up drag and drop event listeners
        field_template.addEventListener('dragstart', dragStart);
        field_template.addEventListener('dragover', dragOver);
        field_template.addEventListener('dragend', dragEnd);
        field_template.querySelector('.up-field-btn')?.addEventListener('click', moveFieldUp);
        field_template.querySelector('.down-field-btn')?.addEventListener('click', moveFieldDown);

        disableFirstAndLastButtons();
    }

    /**
     * Moves the clicked field up in the list
     * @param { Mouse Event } e
     * @returns void
     */
    function moveFieldUp(e) {
        // Get the field clicked
        const field_clicked = e.target.closest('.sortable-field');
        if (field_clicked === null) { return; }

        // Get the field to swap
        const field_to_swap = field_clicked.previousElementSibling;
        if (field_to_swap === null) { return; }

        // Swap Elements
        field_collection.insertBefore(field_clicked, field_to_swap);

        // Set the buttons and re-render the signature
        disableFirstAndLastButtons();
        render_signature();
    }

    /**
     * Moves the clicked field down in the list
     * @param { Mouse Event } e
     * @returns void
     */
    function moveFieldDown(e) {
        // Get the field clicked
        const field_clicked = e.target.closest('.sortable-field');
        if (field_clicked === null) { return; }

        // Get the field to swap
        const field_to_swap = field_clicked.nextElementSibling;
        if (field_to_swap === null) { return; }

        // Swap Elements
        field_collection.insertBefore(field_to_swap, field_clicked);

        // Set the buttons and re-render the signature
        disableFirstAndLastButtons();
        render_signature();
    }

    /**
     * This function will enable all field move buttons
     * but disable the first and last
     * @returns void
     */
    function disableFirstAndLastButtons() {
        // Get all the fields
        const all_fields = field_collection.querySelectorAll('.sortable-field');
        all_fields.forEach((single_field, index) => {
            // Get their up and down arrow buttons
            const up_arrow = single_field.querySelector('.up-field-btn');
            const down_arrow = single_field.querySelector('.down-field-btn');

            // Enable them
            up_arrow.removeAttribute('disabled');
            down_arrow.removeAttribute('disabled');

            // Disable the first and last button
            if (index === 0) {
                up_arrow.setAttribute('disabled', 'disabled');
            }
            if (index == all_fields.length - 1) {
                down_arrow.setAttribute('disabled', 'disabled');
            }
        });
    }


    /**
     * Handles the drag start events
     * @param {DragEvent} e
     * @returns void
     */
    function dragStart(e) {

        // If we started a drag with the keyboard cancel it
        field_collection.querySelectorAll('.currently_dragging').forEach((dragged_element) => {
            dragged_element.classList.remove('currently_dragging');
        });

        // Let the drag know we are moving the element
        e.dataTransfer.effectAllowed = 'move'
        e.dataTransfer.setData('text/plain', null);

        // Sets us selected field variables
        selected_field = e.target;
        e.target.classList.add('currently_dragging');
    }

    /**
     * Handles the dragover events
     * @param {DragEvent} e
     * @returns void
     */
    function dragOver(e) {

        // If we drag over itself ignore
        const target = e.target.closest('.sortable-field');
        if (selected_field.isEqualNode(target)) { return; }

        // If the dragged element is before the event target then insert before (Move Up)
        if (isBefore(selected_field, target)) {
            field_collection.insertBefore(selected_field, target);
        } else {
            // Else the dragged element is after the event target then insert before the next element (Move Down)
            field_collection.insertBefore(selected_field, target.nextElementSibling);
        }

        // Re-render the signature
        render_signature();
    }

    /**
     * Handles when the drag is done
     * @param {DragEvent} e
     */
    function dragEnd(e) {

        // Clear the dragged element
        selected_field = null;
        e.target.classList.remove('currently_dragging');

        // Re-render the signature
        render_signature();
    }

    /**
     * Renders the signature
     */
    function render_signature() {

        // Clears the current data in the signature
        contact_info_element.innerHTML = "";

        // Gets the selected logo and its values
        const selected_logo_option = logo_select.options[logo_select.selectedIndex];
        const selected_logo_url = selected_logo_option.value;
        const selected_logo_alt = selected_logo_option.dataset.altText;
        const selected_logo_color = selected_logo_option.dataset.color;

        // Sets the logo and the border top color
        sig_logo_element.src = selected_logo_url;
        sig_logo_element.setAttribute('alt', selected_logo_alt);
        contact_info_element.style.borderTopColor = selected_logo_color;
        sig_logo_element.parentElement.style.borderTopColor = selected_logo_color;

        // Loops through the fields and add them to the signature
        let field_count = 0;
        field_collection.querySelectorAll('.sortable-field').forEach((field) => {
            const field_text = field.querySelector('.field-text-value')?.value || "";
            let field_url = field.querySelector('.field-text-url')?.value || "";

            // The first line is big and bold, seconds line is italic, and the rest of the lines are small
            // We always want there to be a space
            let field_paragraph_element = "";
            let field_paragraph_value = field_text === "" ? "&nbsp;" : field_text;
            if (field_count === 0) {
                field_paragraph_element = signature_primary_field_template.content.children[0].cloneNode(true);
                field_paragraph_value = `<strong>${field_paragraph_value}</strong>`;
            } else if (field_count === 1) {
                field_paragraph_element = signature_secondary_field_template.content.children[0].cloneNode(true);
            } else {
                field_paragraph_element = signature_field_template.content.children[0].cloneNode(true);
            }

            // If we have a URL then make it a link
            if (field_text !== "" && field_url !== "") {
                field_paragraph_value = `<a href="${field_url}">${field_paragraph_value}</a>`;
            }

            // Set the innerHTML and append it to the signature
            field_paragraph_element.innerHTML = field_paragraph_value;
            contact_info_element.append(field_paragraph_element);

            // Only count up if there was actual content there
            if (field_text !== "") {
                field_count++;
            }
        });
    }

    /**
     * Copies the signature and saves it to the clipboard
     * @param {ClipboardEvent} copy_event
     */
    function copy_signature(copy_event) {
        // Gets the signature and saves it to the clip board
        const signature_element = document.querySelector('.signature-section table').outerHTML;
        copy_event.clipboardData.setData('text/plain', "Please paste into a format that supports HTML");
        copy_event.clipboardData.setData('text/html', signature_element);

        // Sets the message and clears it after 2 seconds
        document.querySelectorAll('.signature-copy-message').forEach((single_message) => {
            single_message.classList.remove('dcf-d-none');
        });
        setTimeout( () => {
            document.querySelectorAll('.signature-copy-message').forEach((single_message) => {
                single_message.classList.add('dcf-d-none');
            });
        }, 2000);
    }

    /**
     * Checks if element A is before element B
     * @param { HTMLElement } element_a
     * @param { HTMLElement } element_b
     * @returns { Bool } If element A is before B
     */
    function isBefore(element_a, element_b) {
        return element_a.compareDocumentPosition(element_b) & Node.DOCUMENT_POSITION_PRECEDING;
    }

    /**
     * Selects the text of the element in the query selector
     * @param {string} element_query_selection
     */
    function select_text(element_query_selection) {
        const text = document.querySelector(element_query_selection);
        if (document.body.createTextRange) {
            let range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            let selection = window.getSelection();
            let range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    /**
     * Clears the selected text
     */
    function clear_select() {
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        } else if (document.selection) {
            document.selection.empty();
        }
    }

    /**
     *  Generates a random UUID
     * @returns { string }
     */
    function generate_UUID() {
        return 'xxxx-xxxx-xxx-xxxx'.replace(/[x]/g, (c) => {
            let r = Math.random() * 16 | 0;
            let v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
</script>