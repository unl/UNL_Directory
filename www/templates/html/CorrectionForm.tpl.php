<div class="correction-form noprint forms">
  <form class="dcf-form" style="display: none !important;" method="post" action="<?php echo UNL_Officefinder::getURL() ?>">
    <div class="dcf-form-group">
      <h2>Have a correction?</h2>
      <p>If you'd like to correct your own entry, contact your departmental HR liaison. For corrections to another person's contact or department/unit information, use the form below.</p>
    </div>
    <div class="dcf-form-group">
      <label class="dcf-label" for="your-name">Your Name: <small class="dcf-required">Required</small></label>
      <input class="dcf-input-text" id="your-name" type="text" name="name" value="" required />
    </div>
    <div class="dcf-form-group">
      <label class="dcf-label" for="your-email">Your Email: <small class="dcf-required">Required</small></label>
      <input class="dcf-input-text" id="your-email" type="text" name="email" value="" required />
    </div>
    <div class="dcf-form-group">
      <label class="dcf-label" for="your-correction">What is your correction? <small class="dcf-required">Required</small></label>
      <textarea class="dcf-input-text" id="your-correction" name="message" required></textarea>
    </div>
    <input class="dcf-btn dcf-btn-primary" type="submit" value="Submit" />
    <input type="hidden" name="_type" value="correction" />
    <input type="hidden" name="source" value="" />
    <input type="hidden" name="kind" value="" />
    <input type="hidden" name="id" value="" />
  </form>
  <p class="success dcf-p-10 dcf-d-none" tabindex="-1"></p>
  <h2>Have a correction?</h2>
  <p>
    If you'd like to correct your own entry, contact your departmental HR liaison.
  </p>
  <p>
    For corrections to another person's contact or department/unit information, please send us an email at
    <a href="mailto:dxg@lists.nebraska.edu">dxg@lists.nebraska.edu</a>. Be sure to include the specific
    change needed and the name of the person or department involved. Including a link to the page you’re viewing is
    also very helpful in ensuring the request is routed to the correct team.
  </p>
</div>
