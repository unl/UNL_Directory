<?php

// Check if the user can edit and store this result for later
$userCanEdit = false;
if ($controller->options['view'] != 'alphalisting') {
    $userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}

// Inject a prefix into the document title
$titleName = empty($context->name) ? '[New Department]' : $context->name;
if (isset($page)) {
    $page = $page->getRawObject();
    $page->doctitle = substr_replace($page->doctitle, $titleName . ' | ', strlen('<title>'), 0);
}

if (isset($context->options['render'])) {
    if ($context->options['render'] === 'editing') {
        echo $savvy->render($context, 'Officefinder/Department/EditForm.tpl.php');
        return;
    } elseif ($context->options['render'] === 'listing') {
        echo $savvy->render($context, 'Officefinder/Department/ListingItem.tpl.php');
        return;
    } elseif ($context->options['render'] === 'summary') {
        echo $savvy->render($context, 'Officefinder/Department/Summary.tpl.php');
        return;
    }
}

// Get the official org unit if possible
$department = $context->getHRDepartment();

$listings = $context->getUnofficialChildDepartments();
$hrParent = $context->getOfficialParent();

$officialChildren = new UNL_Officefinder_DepartmentList_Filter_Suppressed($context->getOfficialChildDepartments('name ASC'));
$hasOfficialChildDepartments = count($officialChildren);
?>
<section class="summary dcf-grid dcf-col-gap-vw" itemscope itemtype="http://schema.org/Organization">
    <div class="dcf-col-100% dcf-col-33%-start@md department-summary" aria-live="polite">
        <?php echo $savvy->render($context, 'Officefinder/Department/Summary.tpl.php') ?>
        <?php if ($department): ?>
            <p><a href="#all_employees">Jump to employees</a></p>
        <?php endif; ?>
        <?php if ($userCanEdit): ?>
            <?php echo $savvy->render($context, 'Officefinder/Department/EditBox.tpl.php') ?>
        <?php endif ;?>
    </div>
    <div class="dcf-col-100% dcf-col-67%-end@md">
        <div class="dcf-mb-7 dcf-bg-white dcf-b-1 dcf-b-solid unl-b-light-gray" id="listings" data-department-id="<?php echo $context->id ?>">
            <h2 class="dcf-txt-h4 dcf-mb-0 dcf-ml-0">
                <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M14.74 19.77c.402.266.861.396 1.318.396.619 0 1.232-.237 1.692-.697l.733-.733a1.758 1.758 0 00.001-2.493l-1.338-1.338c-.666-.666-1.827-.666-2.491 0l-.123.122a29.517 29.517 0 01-3.392-3.391l.123-.123c.334-.333.518-.775.518-1.247 0-.471-.184-.912-.516-1.243l-1.34-1.338a1.762 1.762 0 00-2.492 0l-.734.734a2.4 2.4 0 00-.3 3.01 29.723 29.723 0 008.341 8.341zM7.406 9.126l.734-.734a.76.76 0 011.079 0l1.338 1.34a.76.76 0 01-.001 1.075l-.446.446a.5.5 0 00-.033.671 30.346 30.346 0 004.169 4.167.502.502 0 00.67-.033l.446-.445a.781.781 0 011.077 0l1.338 1.338a.758.758 0 010 1.076l-.733.733a1.396 1.396 0 01-1.751.175 28.736 28.736 0 01-8.059-8.058 1.394 1.394 0 01.172-1.751z"></path>
                    <path d="M21.5 4h-17C3.673 4 3 3.327 3 2.5S3.673 1 4.5 1h17a.5.5 0 000-1h-17a2.503 2.503 0 00-2.476 2.847A.48.48 0 002 3v4h-.5a.5.5 0 000 1H2v2h-.5a.5.5 0 000 1H2v2h-.5a.5.5 0 000 1H2v2h-.5a.5.5 0 000 1H2v2h-.5a.5.5 0 000 1H2v1.5C2 22.879 3.122 24 4.5 24h17a.5.5 0 00.5-.5v-19a.5.5 0 00-.5-.5zM21 23H4.5c-.827 0-1.5-.673-1.5-1.5V20h.5a.5.5 0 000-1H3v-2h.5a.5.5 0 000-1H3v-2h.5a.5.5 0 000-1H3v-2h.5a.5.5 0 000-1H3V8h.5a.5.5 0 000-1H3V4.499c.418.314.938.501 1.5.501H21v18z"></path>
                    <path d="M4.5 2a.5.5 0 000 1h16a.5.5 0 000-1h-16z"></path>
                </svg>
                Yellow Pages
            </h2>

            <div class="<?php if ($userCanEdit): ?> editing<?php endif; ?>">
                <?php if (count($listings)): ?>
                    <?php echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php') ?>
                <?php endif; ?>
                <?php if ($userCanEdit): ?>
                <div class="edit-tools">
                    <a class="dcf-btn wdn-button-triad listing-add" href="<?php echo $context->getNewChildURL() ?>">Add<span class="dcf-sr-only"> a new child listing</span></a>
                    <div class="forms">
                        <div class="form">
                        </div>
                        <form method="post" action="<?php echo $context->getURL() ?>" class="sortform">
                            <input type="hidden" name="_type" value="sort_departments" />
                            <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
                            <input type="hidden" name="sort_json" value="" />
                            <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                            <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                            <input type="submit" value="Save order" />
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($department): ?>
        <div class="dcf-mb-7" id="all_employees">
            <h2 class="dcf-txt-h4">
                <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M21.5 4h-16C4.673 4 4 3.327 4 2.5S4.673 1 5.5 1h16a.5.5 0 000-1h-16A2.503 2.503 0 003 2.5V7h-.5a.5.5 0 000 1H3v2h-.5a.5.5 0 000 1H3v2h-.5a.5.5 0 000 1H3v2h-.5a.5.5 0 000 1H3v2h-.5a.5.5 0 000 1H3v1.5C3 22.878 4.122 24 5.5 24h16a.5.5 0 00.5-.5v-19a.5.5 0 00-.5-.5zM21 23H5.5c-.827 0-1.5-.673-1.5-1.5V20h.5a.5.5 0 000-1H4v-2h.5a.5.5 0 000-1H4v-2h.5a.5.5 0 000-1H4v-2h.5a.5.5 0 000-1H4V8h.5a.5.5 0 000-1H4V4.499c.418.314.938.501 1.5.501H21v18z"></path>
                    <path d="M5.5 2a.5.5 0 000 1h15a.5.5 0 000-1h-15zM8 20h10a.5.5 0 00.5-.5V18c0-.772-.582-1.543-1.325-1.755L15 15.623v-.765c.904-.683 1.5-1.81 1.5-3.088C16.5 9.691 14.93 8 13 8s-3.5 1.691-3.5 3.771c0 1.278.596 2.405 1.5 3.088v.765l-2.177.622C8.081 16.458 7.5 17.229 7.5 18v1.5a.5.5 0 00.5.5zm5-11c1.126 0 2.07.835 2.381 1.972a1.28 1.28 0 01-.56.04c-.232-.034-.654-.154-.881-.578a.498.498 0 00-.794-.116c-.729.73-1.821.788-2.492.527C11 9.773 11.916 9 13 9zm-2.493 2.852a3.468 3.468 0 002.911-.463c.326.324.763.54 1.257.613a2.18 2.18 0 00.805-.024c-.097 1.431-1.167 2.565-2.481 2.565-1.353-.001-2.452-1.201-2.492-2.691zM8.5 18c0-.325.285-.704.598-.793l2.539-.726A.5.5 0 0012 16v-.633c.319.103.65.175 1 .175s.681-.072 1-.175V16a.5.5 0 00.363.481l2.538.726c.314.089.6.468.6.793v1h-9v-1z"></path>
                </svg>
                All Employees
            </h2>
            <?php echo $savvy->render($department) ?>
        </div>
        <div class="dcf-mt-7 dcf-txt-center" id="orgChart">
            <h2 class="dcf-txt-h4">
                <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current dcf-txt-top" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path d="M23.5 18.001H21v-5.5a.5.5 0 00-.5-.5h-8v-6h7a.5.5 0 00.5-.5v-4a.5.5 0 00-.5-.5h-15a.5.5 0 00-.5.5v4a.5.5 0 00.5.5h7v6h-8a.5.5 0 00-.5.5v5.5H.5a.5.5 0 00-.5.5v5a.5.5 0 00.5.5h5a.5.5 0 00.5-.5v-5a.5.5 0 00-.5-.5H4v-5h7.5v5h-2a.5.5 0 00-.5.5v5a.5.5 0 00.5.5h5a.5.5 0 00.5-.5v-5a.5.5 0 00-.5-.5h-2v-5H20v5h-1.5a.5.5 0 00-.5.5v5a.5.5 0 00.5.5h5a.5.5 0 00.5-.5v-5a.5.5 0 00-.5-.5zM5 5.001v-3h14v3h-6.5V5h-1v.001H5zm0 14v4H1v-4h4zm9 4h-4v-4h4v4zm9 0h-4v-4h4v4z"></path>
                </svg>
                HR Organization Chart
            </h2>
            <?php if (!$context->isRoot()): ?>
            <ul class="dcf-list-bare unl-font-sans">
                <li><a class="dcf-txt-decor-hover" href="<?php echo $hrParent->getURL() ?>">
                    <?php echo $hrParent->name ?>
                    <span class="org-unit dcf-d-block dcf-txt-xs">#<?php echo $hrParent->org_unit ?></span>
                </a>
                <?php endif; ?>

                    <ul<?php if (!$context->isRoot()): ?> class="dcf-list-bare"<?php endif; ?>>
                        <li>
                            <strong><?php echo $context->name; ?></strong>
                            <?php if ($hasOfficialChildDepartments): ?>
                            <svg class="dcf-mt-2 dcf-h-5 dcf-w-5 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                                <path d="M23.277 12.227l-3.449-3.451a.502.502 0 00-.692-.015L15 12.57V.5a.5.5 0 00-.5-.5h-5a.5.5 0 00-.5.5v12.086L4.865 8.762a.5.5 0 00-.693.013L.721 12.226a.5.5 0 000 .708l10.926 10.924a.498.498 0 00.708 0l10.924-10.924a.5.5 0 00-.002-.707zM12 22.797L1.781 12.58l2.758-2.758 4.622 4.274a.501.501 0 00.84-.367V1h4v12.711a.5.5 0 00.839.368l4.622-4.257 2.756 2.758L12 22.797z"></path>
                            </svg>
                            <ul class="dcf-list-bare">
                                <?php foreach ($officialChildren as $child): ?>
                                <li><a class="dcf-txt-decor-hover" href="<?php echo $child->getURL(); ?>">
                                    <?php echo $child->name; ?>
                                    <?php if ($child->isOfficialDepartment()): ?>
                                    <span class="org-unit dcf-d-block dcf-txt-xs">#<?php echo $child->org_unit ?></span>
                                    <?php endif; ?>
                                </a></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        </li>
                    </ul>

                <?php if (!$context->isRoot()): ?>
                </li>
            </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="record-single"></section>
