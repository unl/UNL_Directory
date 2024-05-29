<?php
    /**
     * Creates a human readable string for the bytes
     * This is here and not in the src because savvy will escape the abbr tag
     *
     * @param int $size Size of data in bytes
     * @param string $unit If we want to force a unit you can set this
     * @return string HTML code for the human readable bytes
     */
    function humanReadableBytes($size, $unit="") {
        if ((!$unit && $size >= 1<<30) || $unit == "GB") {
            return number_format($size/(1<<30)) . "<abbr title='Gigabytes'>GB</abbr>";
        }
        if ((!$unit && $size >= 1<<20) || $unit == "MB") {
            return number_format($size/(1<<20)) . "<abbr title='Megabytes'>MB</abbr>";
        }
        if ((!$unit && $size >= 1<<10) || $unit == "KB") {
            return number_format($size/(1<<10)) . "<abbr title='Kilobytes'>KB</abbr>";
        }
        return number_format($size) . " bytes";
    }
?>

<div class=" dcf-bleed dcf-hero dcf-hero-default">
    <!-- TemplateEndEditable -->
    <div class="dcf-hero-group-1">
        <div class="dcf-breadcrumbs-wrapper">
            <nav class="dcf-breadcrumbs" id="dcf-breadcrumbs" role="navigation" aria-label="breadcrumbs"></nav>
        </div>
        <header class="dcf-page-title" id="dcf-page-title">
            <h1> Welcome To UNL Directory </h1>
        </header>
    </div>
    <div class="dcf-hero-group-2"></div>
</div>


<div class="dcf-bleed view-unl_ucbcn_mananger_welcome events-manager">
    <div class="dcf-wrapper">
        <section class="dcf-grid dcf-col-gap-vw dcf-pb-8">
            <div class="dcf-col-100% dcf-col-75%@md">
                <h3
                style=
                    "background-color: #474746;
                    color: #ffff;
                    display: block; 
                    margin: 0; 
                    padding: 1em; 
                    font-size: .8em !important; 
                    font-style: normal;
                    font-weight: 400;
                    text-transform: uppercase;
                    width: 100%;
                    border-bottom: 1px solid var(--b)"
                >
                    Welcome to directory 
                </h3>
                <p>
                    Add content...
                </p>
                <p>Here are some tips to get going with UNL Directory:</p>
                <ul class="helpful">
                    <li>Add content.</li>
                    <li>Add content.</li>
                    <li>Add content.</li>
                </ul>
            </div>
            <div class="dcf-col-100% dcf-col-25%-end@md">
                <h3 
                    style=
                    "text-align: center; 
                    background-color: #474746;
                    color: #ffff;
                    display: block; 
                    margin: 0; 
                    padding: 1em; 
                    font-size: .8em !important; 
                    font-style: normal;
                    font-weight: 400;
                    text-transform: uppercase;
                    width: 100%;
                    border-bottom: 1px solid var(--b)"
                >
                    Tools
                </h3>
                <div class="tools">
                    <ul class="dcf-list-bare dcf-txt-sm" style="padding: 1px 20px;">
                        <li>
                            <a class="dcf-txt-decor-hover" href="<?php echo $context->getURL('')?>">Home</a>
                        </li>
                        <li>
                            <a class="dcf-txt-decor-hover" href="<?php echo $context->getURL('/myinfo/signature-generator')?>">Signature Generator</a>
                        </li>
                        <li>
                            <a class="dcf-txt-decor-hover" href="<?php echo $context->getURL('/myinfo/avatar') ?>">Avatar</a>
                        </li>
                        <li>
                            <a class="dcf-txt-decor-hover" href="https://www.github.com/unl/UNL_UCBCN_System/wiki">Get Help!</a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>