

WDN.jQuery(document).ready(function() {
	self.focus();
	document.getElementById("form1").elements[0].focus();
	WDN.loadJS('wdn/templates_3.0/scripts/toobar_peoplefinder.js');
	WDN.jQuery('#form1').submit(function(eventObject) {
		WDN.toolbar_peoplefinder.queuePFRequest('bieber', 'results');
		eventObject.preventDefault();
		eventObject.stopPropagation();
		return false;
	});
});
