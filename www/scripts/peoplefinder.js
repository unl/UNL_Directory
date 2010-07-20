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
				footerLoc = WDN.jQuery("#footer").offset();
				//alert(WDN.jQuery(this).height());
				if ((selectedLi.top - 40 + WDN.jQuery(this).height()) > footerLoc.top) {
					placementTop = (footerLoc.top - WDN.jQuery(this).height() - 40);
				} else {
					placementTop = selectedLi.top - 40;
				}
				return {top : placementTop, left : coords.left};
			});
		},
		presentPeopleFinderResults : function(query){
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
			WDN.toolbar_peoplefinder.queuePFRequest(query, 'results');
			document.title = 'UNL | Directory | People Search for ' + query;
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
					});
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
		
		of_getUID : function(uid) {
			WDN.jQuery('#pfShowRecord').html('<img alt="progress" id="pfprogress" src="/wdn/templates_3.0/css/header/images/colorbox/loading.gif" />');
			var url = 'departments/?view=department&format=partial&id=' + uid;
            WDN.get(url, null, service_officefinder.updateOfficeFinderRecord);
            return false;
		},
		
		updateOfficeFinderRecord : function(data, textStatus) {
			if (textStatus == 'success') {
            	document.getElementById('pfShowRecord').innerHTML = "<div class=\"vcard office\">"+data+"</div>";
            	service_peoplefinder.updatePeopleFinderRecord();
            } else {
                document.getElementById('pfShowRecord').innerHTML = 'Aw snap, something went wrong!';
            }
		}
	};
}();

directory = function() {
	return {
		initializeSearchBoxes : function() {
			WDN.jQuery('#peoplefinder, #officefinder').submit(function(eventObject) { //on submit of the search form (people)
				window.location.hash = '#q/' +this.id +'/' + WDN.jQuery('#'+this.id+' input.q').val() ; //triggering a hash change will run through the searching function
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
			WDN.jQuery('input.q').blur(function() {
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
		}
	}
}();

WDN.jQuery(function(){
	WDN.jQuery(window).bind('hashchange', function(eventObject){
		var hash = location.hash;
		if (hash.match(/^#q\/[peoplefinder|officefinder]/)) {
			hash = hash.split('/'); //hash[1] = term hash[2] = type
			
			if (hash[1] === 'peoplefinder'){
				WDN.jQuery('#q').val(hash[2]);
				service_peoplefinder.presentPeopleFinderResults(hash[2]);
			} else {
				WDN.jQuery('#q2').val(hash[2]);
				service_officefinder.getOfficeList(hash[2]);
			}
			eventObject.preventDefault();
			eventObject.stopPropagation();
			return false;
		}
		if (!hash) {
			// Load the default instructions
			WDN.jQuery('#maincontent').load('?format=partial', function(){
				directory.initializeSearchBoxes();
			});
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
	directory.initializeSearchBoxes();
});
function pf_handleResults(e)  {
	WDN.log(e);
}