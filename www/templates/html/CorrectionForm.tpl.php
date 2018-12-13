<div class="correction-form noprint forms">
    <h2 class="wdn-brand dcf-txt-center">Have a correction?</h2>
    <p>If you'd like to correct your own entry, contact your departmental HR liaison.
    For corrections to another person's contact information or yellow pages, use the form below.</p>
    <form method="post" action="<?php echo UNL_Officefinder::getURL() ?>">
        <ul class="dcf-list-bare">
            <li>
                  <label class="dcf-label">
                    Your Name: (required)
                    <input class="dcf-input-text" type="text" name="name" value="" required />
                </label>
            </li>
            <li>
                <label class="dcf-label">
                    Your Email: (required)
                    <input class="dcf-input-text" type="text" name="email" value="" required />
                </label>
            </li>
            <li>
                <label class="dcf-label">
                    What is your correction? (required)
                    <textarea class="dcf-input-text" name="message" required></textarea>
                </label>
            </li>
            <li>
                 <input class="dcf-btn" type="submit" value="Submit" />
            </li>
        </ul>

        <input type="hidden" name="_type" value="correction" />
        <input type="hidden" name="source" value="" />
        <input type="hidden" name="kind" value="" />
        <input type="hidden" name="id" value="" />
    </form>
    <p class="success dcf-d-none" tabindex="-1"></p>
</div>
