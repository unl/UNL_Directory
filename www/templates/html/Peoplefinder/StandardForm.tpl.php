<form method="get" id="peoplefinder" action="<?php echo UNL_Peoplefinder::getURL(); ?>" class="directory-search">
    <?php if (isset($context->options['chooser'])): ?>
        <input type="hidden" name="chooser" value="true" />
    <?php endif; ?>

    <?php
    $default = '';
    if (isset($context->options['q']) && !($context->options['q'] instanceof ArrayAccess)) {
        $default = htmlentities((string)$context->options['q'], ENT_QUOTES);
    }
    ?>
    <div class="wdn-input-group">
        <input tabindex="0" type="text" autofocus placeholder="Herbie Husker" value="<?php echo $default; ?>" id="q" name="q" title="Enter a name to begin your search" />
        <span class="wdn-input-group-btn">
            <button name="submitbutton" type="submit" value="Search" title="Search" class="button wdn-icon-search"></button>
        </span>
    </div>
</form>
<p><a href="<?php echo UNL_Peoplefinder::getURL() ?>help/" class="wdn-button" target="_blank">Help</a></p>

<script id="annotateTemplate" type="text/x-jsrender">
<div title="Leave notes on the listing for {{:preferredName}}" class="wdn_annotate" id="directory_{{:uid}}"></div>
</script>

<script id="noticeTemplate" type="text/x-jsrender">
<div class="wdn_notice">
    <div class="close">
        <a href="#" title="Close this notice">Close this notice</a>
    </div>
    <div class="message">
        <p class="title">We automatically tried some other searches for you.</p>
        <p>
            Your original search for <span>{{:originalSearch}}</span> did not return any results.
            So we tried a few more advanced searches and below is what we found for <span>First Name: {{:firstName}} AND Last Name: {{:lastName}}</span>.</p>
    </div>
</div>
</script>

<script id="genericErrorTemplate" type="text/x-jsrender">
<p class="error">Something went wrong. Please try again later.</p>
</script>

<script id="queryLengthTemplate" type="text/x-jsrender">
<p>Please enter more information, your query must be at least 3 characters long.</p>
</script>
