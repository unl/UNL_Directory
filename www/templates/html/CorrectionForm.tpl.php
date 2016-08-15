<div class="correction-form commentProblem noprint forms">
    <h3>Have a correction?</h3>
    <p>If you'd like to correct your own entry, contact your UNL departmental HR liaison.<br />
    For corrections to another person's contact information, use the form below.<br /><br /></p>
    <form class="wdn_feedback_comments2" method="post" action="http://ucommchat.unl.edu/clientLogin">
        <ul>
            <li>
                  <label>
                    Name:
                    <input type="text" name="name" value="" />
                </label>
            </li>
            <li>
                <label>
                    Email:
                    <input type="text" name="email" value="" />
                </label>
            </li>
            <li>
                <label>
                    What is your correction?
                    <textarea name="message"></textarea>
                </label>
            </li>
            <li>
                 <input type="submit" value="Submit" />
            </li>
        </ul>
        
        <input type="hidden" name="method" value="EMAIL" />
        <input type="hidden" name="initial_pagetitle" value="UNL Directory" />
        <input type="hidden" name="initial_url" value="" />
    </form>
    <p class="success hidden">Thank you for your correction.</p>
</div>
<!--
<?php if (isset($context->options['uid'])) : ?>
<script type="text/javascript">
WDN.jQuery("document").ready(function(){
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
-->
