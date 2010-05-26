service_peoplefinder = function() {
	return {
		updatePeopleFinderResults : function(){
			alert('update results');
		},
		updatePeopleFinderRecord : function(){
			alert('update record');
		}
	}
}();

WDN.jQuery(document).ready(function() {
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = '';
	});
	WDN.jQuery('#form1').submit(function(eventObject) {
		WDN.jQuery('#pfShowRecord').empty();
		WDN.toolbar_peoplefinder.queuePFRequest(WDN.jQuery('#q').val(), 'results');
		
		eventObject.preventDefault();
		eventObject.stopPropagation();
		WDN.jQuery('li#filters').slideDown();
		return false;
	});
	WDN.jQuery('#q').focus(hideLabel);
	WDN.jQuery('#queryString').focus(hideLabel);
	if (WDN.jQuery('#q').val() !== "") {
		hideLabel();
	};
	WDN.jQuery('#q').blur(function() {
		if (WDN.jQuery('#q').val() === "") {
			showLabel();
		}
	});
	WDN.jQuery('#filters input').click(function(){
		if(WDN.jQuery(this).attr('id') === "filterAll") {
			if (this.checked){
				WDN.jQuery('#filters input').not('#filterAll').removeAttr('checked');
				WDN.jQuery('div.affiliation').show();
			} 
		} else {
				WDN.jQuery('#filterAll').removeAttr('checked');
				WDN.jQuery('#filters input').not('#filterAll').each(function(){
					if(this.checked){
						WDN.jQuery('div.'+WDN.jQuery(this).attr('name')).show();
					} else {
						WDN.jQuery('div.'+WDN.jQuery(this).attr('name')).hide();
					}
				});
		}
	});
});
function hideLabel() {
	WDN.jQuery('#queryString').hide();
	WDN.jQuery('#q').focus();
}

function showLabel() {
	WDN.jQuery('#queryString').show();
}
function pf_handleResults(e)  {
	WDN.log(e);
}
function updateDisplay() {
	alert('hell yeah');
}