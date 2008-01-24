<form method="get" id="form1" action="<?php echo str_replace('index.php', '', $_SERVER['PHP_SELF']); ?>">
	<label for="sn">Last Name: </label>
	<input type="text" name="sn" value="<? echo @$_GET['sn']; ?>" id="sn" />
	<br />
	<label for="cn">First Name: </label>
	<input type="text" name="cn" value="<? echo @$_GET['cn']; ?>" id="cn" />
	<?php if (isset($_GET['chooser'])) {
		echo '<input type="hidden" name="chooser" value="true" />';
	} ?>
	<input type="hidden" name="adv" value="y" />
	<br />
	<label for="eppa">Affiliation: </label>
	<select id="eppa" name="eppa">
		<option value="any" <? if(@$_GET['eppa'] == "any") echo "selected='selected'" ?>>Any</option>
		<option value="fs" <? if(@$_GET['eppa'] == "fs") echo "selected='selected'" ?>>Faculty/Staff</option>
		<option value="stu" <? if(@$_GET['eppa'] == "stu") echo "selected='selected'" ?>>Student</option>
	</select>
	<input style="margin-bottom:-7px;" name="submitbutton" type="image" src="/ucomm/templatedependents/templatecss/images/go.gif" value="Submit" id="submitbutton" />
</form>