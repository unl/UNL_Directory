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
		},
		presentPeopleFinderResults : function(hash){
			WDN.jQuery('#peoplefinder').animate(
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
			WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('#peoplefinder').insertBefore('#results');
			WDN.jQuery('#results').css({'margin-top' : '80px'});
			
			WDN.jQuery('#pfShowRecord').empty();
			WDN.toolbar_peoplefinder.queuePFRequest(hash, 'results');
			document.title = 'UNL | Directory | People Search for ' + hash;
		}
	};
}();

service_officefinder = function() {
	return {
		getOfficeList : function(q) {
			WDN.jQuery('#officefinder').animate(
				{
					'top' : '0',
					'width' : '960px',
					'left' : '0'
				},
				500,
				function(){
					WDN.jQuery('#results').html('<img alt="progress" id="pfprogress" src="/wdn/templates_3.0/css/header/images/colorbox/loading.gif" />');
					WDN.get('departments/?q='+q+'&format=partial', '', function(data, textStatus){
						if (textStatus == 'success') {
							document.title = 'UNL | Directory | Department Search for ' + q;
							service_officefinder.presentOfficeFinderResults(data);
						}
					})
				}
			);
			WDN.jQuery('#q2').siblings('label').hide();
			WDN.jQuery('#officefinder').insertBefore('#results');
			WDN.jQuery('#results').css({'margin-top' : '80px'});
			WDN.jQuery('#pfShowRecord, #results').empty();
		},
		
		presentOfficeFinderResults : function(html) {
			WDN.jQuery('#results').empty();
			WDN.jQuery('#results').html(html);
		}
	};
}();
WDN.jQuery(function(){
	WDN.jQuery(window).bind('hashchange', function(eventObject){
		var hash = location.hash;
		if (hash.match(/^#q=/)) {
			hash = hash.split('='); //hash[1] = term hash[2] = type
			
			if (hash[2] === 'peoplefinder'){
				WDN.jQuery('#q').val(hash[1]);
				service_peoplefinder.presentPeopleFinderResults(hash[1]);
			} else {
				WDN.jQuery('#q2').val(hash[1]);
				service_officefinder.getOfficeList(hash[1]);
			}
			eventObject.preventDefault();
			eventObject.stopPropagation();
			return false;
		}
		if(!hash){
			WDN.jQuery('#maincontent').load('?format=partial');
		}
	});
});

WDN.jQuery(document).ready(function() {
	WDN.loadJS('wdn/templates_3.0/scripts/plugins/hashchange/jQuery.hashchange.1-2.min.js');
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = '';
		WDN.toolbar_peoplefinder.configuedWebService = true;
		if (window.location.hash) {
			WDN.jQuery(window).trigger('hashchange');
		}
	});
	WDN.jQuery('#peoplefinder, #officefinder').submit(function(eventObject) { //on submit of the search form (people)
		window.location.hash = '#q=' + WDN.jQuery('#q').val() +'='+this.id; //triggering a hash change will run through the searching function
		eventObject.preventDefault();
		eventObject.stopPropagation();
		return false;
	});
	WDN.jQuery('#q, #q2').focus(function(){
		WDN.jQuery(this).siblings('label').hide();
	});
	WDN.jQuery('form.directorySearch ol > li > label').focus(function(){
			WDN.jQuery(this).hide().siblings('input[name=q]').focus();
	});
	if (WDN.jQuery('#q').val() !== "") {
		WDN.jQuery('#q').siblings('label').hide();
	};
	if (WDN.jQuery('#q2').val() !== "") {
		WDN.jQuery('#q2').prev('label').hide();
	};
	WDN.jQuery('#q, #q2').blur(function() {
		if (WDN.jQuery(this).val() === "") {
			WDN.jQuery(this).siblings('label').show();
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
function pf_handleResults(e)  {
	WDN.log(e);
}
function updateDisplay() {
	alert('hell yeah');
}
function presentResults(hash){
	
}