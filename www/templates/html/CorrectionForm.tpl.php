<div class="commentProblem noprint">
    <h3>Have a correction?</h3>
    <p>If you'd like to correct your own entry, contact your UNL departmental HR liaison.<br />
    For corrections to another person's contact information, use the form below.<br /><br /></p>
    <form class="wdn_feedback_comments2" method="post" action="http://ucommchat.unl.edu/clientLogin">
        <input type="hidden" name="initial_url" value="" />
        <label for="name">Name:</label><input type="text" name="name" id="name" value="" />
        <label for="email">Email:</label><input type="text" name="email" id="email" value="" /><br />
        <label for="comment">What is your correction?</label>
        <textarea name="message" id="comment"></textarea>
        <input type="hidden" name="method" value="EMAIL" />
        <input type="hidden" name="initial_pagetitle" value="UNL Directory" />
        
        <input type="submit" value="Submit" />
    </form>
</div>
<?php if (isset($context->options['uid'])) : ?>
<script type="text/javascript">
WDN.jQuery("document").ready(function(){

	var location = window.location.href;

	if (WDN.jQuery(".permalink").size()) {
		location = WDN.jQuery(".permalink").attr("href");
	}

	WDN.jQuery('input[name="initial_url"]').val(location);

    require(['idm'], function(idm) {
        if (idm.getEmailAddress()) {
            WDN.jQuery('.commentProblem input[name="email"]').val(idm.getEmailAddress());
        }
        if (idm.getUserId()) {
            WDN.jQuery('.commentProblem input[name="name"]').val(idm.getUserId());
        }
    })
	
	correctionHTML = 
		'<a href="http://www1.unl.edu/comments/" class="dir_correctionRequest pf_record noprint">Have a correction?</a>';
	WDN.jQuery(".vcardInfo").append(correctionHTML);
    
    //Initialize the new correction form link
    directory.initializeCorrectionForms();
});
</script>
<?php endif; ?>