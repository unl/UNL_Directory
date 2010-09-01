<?php
// Now rename a couple departments that are really mis-named:
foreach (array(
    'Aaup (American Association of University Professors)'=>'AAUP (American Association of University Professors)',
    'Operated By Follett Higher Education Group University Bookstore'=>'University Bookstore',
    'Un Computing Services Network (University of Nebraska Central Administration)'=>'UN Computing Services Network (University of Nebraska Central Administration)',
    'Uaad Officers & Non-Committee Chair Executive Board'=>'UAAD Officers & Non-Committee Chair Executive Board',
    'Tdd (Telecommunications Device for The Deaf)'=>'TDD (Telecommunications Device for The Deaf)',
    'Srl/scientific Resources for The Law'=>'SRL/Scientific Resources for The Law',
    'College of (Uno) Public Affairs & Community Service'=>'College of Public Affairs & Community Service (UNO)',
    'College of (Unmc)-Lincoln Division Nursing'=>'College of Nursing (UNMC) - Lincoln Division',
    ) as $old=>$new) {
    $dept = UNL_Officefinder_Department::getByOrg_unit($old);
    if ($dept === false) {
        $dept = UNL_Officefinder_Department::getByName($old);
    }
    if ($dept) {
        $dept->name = $new;
        $dept->save();
    }
}
foreach (array('University Communications Scarlet The\'') as $delete) {
    $dept = UNL_Officefinder_Department::getByOrg_unit($delete);
    if ($dept === false) {
        $dept = UNL_Officefinder_Department::getByName($delete);
    }
    if ($dept) {
        $dept->delete();
    }
}