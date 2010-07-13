<form method="get" id="form1" action="<?php echo htmlentities(str_replace('index.php', '', $_SERVER['PHP_SELF']), ENT_QUOTES); ?>">
<fieldset>
    <legend>Search for students, faculty, staff and departments.</legend>
<ol>
    <li>
    <label for="q" id="queryString">Enter a name to begin your search.</label> 
    <?php if (isset($context->options['chooser'])) {
        echo '<input type="hidden" name="chooser" value="true" />';
    }
    if (isset($context->options['q'])) {
        $default = htmlentities($context->options['q'], ENT_QUOTES);
    } else {
        $default = '';
    }
    ?>
    <input type="text" value="<?php echo $default; ?>" id="q" name="q" />
    <input name="submitbutton" type="image" src="images/formSearch.png" value="Submit" id="submitbutton" />
    </li>
    <?php
    if (true !== $context->options['mobile']) {
    ?>
    <li id="filters">
        <fieldset>
        <span>Show:</span>
        <ol>
            <li><input type="checkbox" checked="checked" id="filterAll" name="all" value="1" /><label for="filterAll">All records</label></li>
            <li><input type="checkbox" id="filterStudents" name="student" value="1" /><label for="filterStudents">Students</label></li>
            <li><input type="checkbox" id="fitlerFaculty" name="faculty" value="1" /><label for="fitlerFaculty">Faculty</label></li>
            <li><input type="checkbox" id="fitlerStaff" name="staff" value="1" /><label for="fitlerStaff">Staff</label></li>
            <li><input type="checkbox" id="fitlerDepartments" name="departments" value="1" /><label for="fitlerDepartments">Departments</label></li>
        </ol>
        </fieldset>
    </li>
    <?php
    }
    ?>
</ol>
</fieldset>
</form>