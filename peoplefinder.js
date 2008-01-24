function goBack() {
	if (document.referrer.indexOf('/index.php')!=-1) {
		history.go(-1);
		return false;
	}
	else
		return true;
}

function showHide(eleId) {
	if (document.getElementById(eleId).style.display=='none') {
		document.getElementById(eleId).style.display='block';
	} else {
		document.getElementById(eleId).style.display='none';
	}
	return false;
}
