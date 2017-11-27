<div class="correction-form noprint forms">
    <h2 class="wdn-brand wdn-center">Have a correction?</h2>
    <p>If you'd like to correct your own entry, contact your departmental HR liaison.
    For corrections to another person's contact information or yellow pages, use the form below.</p>
    <form method="post" action="<?php echo UNL_Officefinder::getURL() ?>">
        <ul>
            <li>
                  <label>
                    Your Name: (required)
                    <input type="text" name="name" value="" required />
                </label>
            </li>
            <li>
                <label>
                    Your Email: (required)
                    <input type="text" name="email" value="" required />
                </label>
            </li>
            <li>
                <label>
                    What is your correction? (required)
                    <textarea name="message" required></textarea>
                </label>
            </li>
            <li>
                 <input type="submit" value="Submit" />
            </li>
        </ul>

        <input type="hidden" name="_type" value="correction" />
        <input type="hidden" name="source" value="" />
        <input type="hidden" name="kind" value="" />
        <input type="hidden" name="id" value="" />
    </form>
    <p class="success hidden" tabindex="-1"></p>
</div>
