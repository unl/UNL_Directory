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
<section class="summary wdn-grid-set">
	<div class="bp2-wdn-col-one-third department-summary">
		<?php echo $savvy->render($context, 'Officefinder/Department/Summary.tpl.php') ?>
		<?php if ($department): ?>
			<p><a href="#all_employees">Jump to employees</a></p>
		<?php endif; ?>
		<?php if ($userCanEdit): ?>
			<?php echo $savvy->render($context, 'Officefinder/Department/EditBox.tpl.php') ?>
		<?php endif ;?>
	</div>
	<div class="bp2-wdn-col-two-thirds">
		<div class="card" id="listings" data-department-id="<?php echo $context->id ?>">
			<div class="card-content<?php if ($userCanEdit): ?> editing<?php endif; ?>">
				<h2 class="wdn-brand icon-phone-book">Yellow Pages</h2>
				<?php if (count($listings)): ?>
					<?php echo $savvy->render($listings, 'Officefinder/Department/Listings.tpl.php') ?>
				<?php endif; ?>
				<?php if ($userCanEdit): ?>
					<div class="edit-tools">
						<a class="wdn-button wdn-button-triad listing-add" href="<?php echo $context->getNewChildURL() ?>">Add<span class="wdn-text-hidden"> a new child listing</span></a>
						<div class="forms">
							<div class="form"></div>
							<form method="post" action="<?php echo $context->getURL() ?>" class="sortform">
							    <input type="hidden" name="_type" value="sort_departments" />
							    <input type="hidden" name="department_id" value="<?php echo $context->id; ?>" />
							    <input type="hidden" name="sort_json" value="" />
							</form>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if ($department): ?>
	<section class="wdn-grid-set hr">
		<div class="bp2-wdn-col-two-thirds" id="all_employees">
			<h2 class="wdn-brand icon-employees">All Employees</h2>
			<?php echo $savvy->render($department) ?>
		</div>
		<div class="bp2-wdn-col-one-third wdn-pull-right" id="orgChart">
			<h2 class="wdn-brand icon-hierarchy">HR Organization Chart</h2>
			<?php if (!$context->isRoot()): ?>
				<ul>
					<li><a href="<?php echo $hrParent->getURL() ?>"><?php echo $hrParent->name ?></a>
			<?php endif; ?>

			<ul<?php if (!$context->isRoot()): ?> class="icon-down-arrow"<?php endif; ?>>
				<li>
					<strong><?php echo $context->name; ?></strong>
					<?php if ($hasOfficialChildDepartments): ?>
						<ul class="icon-down-arrow">
							<?php foreach ($officialChildren as $child): ?>
								<li><a href="<?php echo $child->getURL(); ?>"><?php echo $child->name; ?></a></li>
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
	</section>
<?php endif; ?>

<section class="record-single"></section>

<?php if ($userCanEdit): ?>
<section id="modal_edit_form" class="modal-overlay" role="dialog" aria-expanded="false">
	<div tabindex="-1" class="modal-content"></div>
</section>
<?php endif; ?>
