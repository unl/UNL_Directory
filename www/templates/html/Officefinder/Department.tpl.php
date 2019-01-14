<?php


// Check if the user can edit and store this result for later
$userCanEdit = false;
if ($controller->options['view'] != 'alphalisting') {
	$userCanEdit = $context->userCanEdit(UNL_Officefinder::getUser());
}


// inject a prefix into the document title
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
		<div class="card dcf-mb-7" id="listings" data-department-id="<?php echo $context->id ?>">
      <h2 class="dcf-txt-h3 dcf-m-0"><span class="icon-phone-book" aria-hidden="true"></span>Yellow Pages</h2>
			<div class="card-content<?php if ($userCanEdit): ?> editing<?php endif; ?>">
				<?php if (count($listings)): ?>
					<?php echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php') ?>
				<?php endif; ?>
				<?php if ($userCanEdit): ?>
					<div class="edit-tools">
						<a class="dcf-btn wdn-button-triad listing-add" href="<?php echo $context->getNewChildURL() ?>">Add<span class="dcf-sr-only"> a new child listing</span></a>
						<div class="forms">
							<div class="form"></div>
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
				<h2 class="dcf-txt-h3"><span class="icon-employees" aria-hidden="true"></span>All Employees</h2>
				<?php echo $savvy->render($department) ?>
			</div>
			<div class="dcf-mt-7 dcf-txt-center" id="orgChart">
				<h2 class="dcf-txt-h3"><span class="icon-hierarchy" aria-hidden="true"></span>HR Organization Chart</h2>
				<?php if (!$context->isRoot()): ?>
					<ul class="dcf-list-bare unl-font-sans">
						<li><a class="dcf-txt-decor-hover" href="<?php echo $hrParent->getURL() ?>">
							<?php echo $hrParent->name ?>
							<span class="org-unit dcf-d-block dcf-txt-xs">#<?php echo $hrParent->org_unit ?></span>
						</a>
				<?php endif; ?>

				<ul<?php if (!$context->isRoot()): ?> class="dcf-list-bare icon-down-arrow"<?php endif; ?>>
					<li>
						<strong><?php echo $context->name; ?></strong>
						<?php if ($hasOfficialChildDepartments): ?>
							<ul class="dcf-list-bare icon-down-arrow">
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
