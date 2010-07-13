service_peoplefinder = function() {
	return {
		updatePeopleFinderResults : function(){ //function called when the list has been rendered
			WDN.jQuery('ul.pfResult li').each(function(){
				onClick = WDN.jQuery(this).find('.cInfo').attr('onclick');
				WDN.jQuery(this).find('.cInfo').removeAttr('onclick');
				WDN.jQuery(this).click(onClick);
			});
			WDN.jQuery('ul.pfResult li').click(function() {
				WDN.jQuery('li.selected').removeClass('selected');
				WDN.jQuery(this).addClass('selected');
				return false;
			});
		},
		updatePeopleFinderRecord : function(){ //function called when a record has been rendered
			WDN.jQuery('#pfShowRecord').offset(function(index, coords) {
				selectedLi = WDN.jQuery('li.selected').offset();
				return {top : selectedLi.top - 40, left : coords.left};
			});
		}
	};
}();
WDN.jQuery(function(){
	WDN.jQuery(window).bind('hashchange', function(){
		var hash = location.hash;
		if (hash.match(/^#q=/)) {
			hash = hash.split('=');
			//WDN.toolbar_peoplefinder.queuePFRequest(hash[1], 'results');
			WDN.jQuery('#q').val(hash[1]);
			hideLabel();
			WDN.jQuery('#pfShowRecord').empty();
			WDN.jQuery('li#filters').slideDown();
			document.title = 'UNL | Peoplefinder | Search for ' + hash[1];
		}
		if(!hash){
			WDN.jQuery('#maincontent').load('templates/html/Peoplefinder/Instructions.tpl.php');
		}
	});
	if (window.location.hash) {
		WDN.jQuery(window).trigger('hashchange');
	}
});

WDN.jQuery(document).ready(function() {
	WDN.loadJS('/wdn/templates_3.0/scripts/plugins/hashchange/jQuery.hashchange.1-2.min.js');
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = '';
		WDN.toolbar_peoplefinder.configuedWebService = true;
	});
	WDN.jQuery('#form1').submit(function(eventObject) { //on submit of the search form (people)
		//animate the form (move it up)
		//1. start animation
		
		WDN.jQuery(this).animate(
			{
				'top' : '0',
				'width' : '960px',
				'left' : '0'
			},
			500,
			function() {
				WDN.jQuery('li#filters').slideDown();
			}
		);
		
		//2. remove form from #results
		WDN.jQuery(this).insertBefore('#results');
		WDN.jQuery('#results').css({'margin-top' : '80px'});
		
		//3.
		WDN.jQuery('#pfShowRecord').empty();
		WDN.toolbar_peoplefinder.queuePFRequest(WDN.jQuery('#q').val(), 'results');
		//window.location.hash = '#q=' + WDN.jQuery('#q').val();
		document.title = 'UNL | Peoplefinder | Search for ' + WDN.jQuery('#q').val();
		
		eventObject.preventDefault();
		eventObject.stopPropagation();
		return false;
	});
	WDN.jQuery('#q').focus(function(){
		WDN.jQuery('#queryString').hide();
	});
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
function presentResults(hash){
	
}