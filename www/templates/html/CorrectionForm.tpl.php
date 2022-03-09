<div class="correction-form noprint forms">
  <form class="dcf-form" method="post" action="<?php echo UNL_Officefinder::getURL() ?>">
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
  <p class="success dcf-d-none" tabindex="-1"></p>
</div>
