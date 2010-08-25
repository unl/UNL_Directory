service_peoplefinder = function() {
	return {
		updatePeopleFinderResults : function(){ //function called when the list has been rendered
			WDN.loadJS('scripts/filters.js', function(){
				filters.initialize();
				
			});
			WDN.jQuery('ul.pfResult li').each(function(){
				//onClick = WDN.jQuery(this).find('.cInfo').attr('onclick');
				WDN.jQuery(this).find('.cInfo, .fn a').removeAttr('onclick');
			});
			WDN.jQuery('ul.pfResult:not(.departments) li .overflow').click(function(){
				service_peoplefinder.showIndividualPeopleFinderRecord(WDN.jQuery(this));
				return false;
				}
			);
		},
		
		updatePeopleFinderRecord : function(data, textStatus){ //function called when a record has been rendered
			if (textStatus == 'success') {
				WDN.jQuery('li.current').append(data);
				WDN.jQuery('li.current .vcard a.planetred_profile').fadeIn(400);
				WDN.jQuery('li.current .vcard').slideDown();
            	WDN.jQuery('li.selected .loading').hide();
            } else {
                
            }
		},
		
		presentPeopleFinderResults : function(query){
			WDN.jQuery('#filters').css({'opacity' : '0.4'});
			WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('#maincontent div.two_col').remove();
			WDN.toolbar_peoplefinder.queuePFRequest(query, 'results');
			document.title = 'UNL | Directory | Search for ' + query;
			WDN.jQuery("#breadcrumbs ul li:contains('Search for')").remove();
			WDN.jQuery('#breadcrumbs ul').append('<li>Search for '+query+'</li>');
		},
		
		showIndividualPeopleFinderRecord : function(liRecord) {
			if (liRecord.parent().hasClass('selected')) {
				liRecord.siblings('.vcard').children('a.planetred_profile').fadeOut(400);
				liRecord.siblings('.vcard').slideUp(function(){
					WDN.jQuery(this).remove();
					
				});
				liRecord.parent().removeClass('selected');
			} else {
				liRecord.children('.loading').show();
				WDN.jQuery('li.current').removeClass('current');
				liRecord.parent('li').addClass('selected current');
				var href = liRecord.find('a.cInfo').attr('href');
				href = href.split('?uid=');
				var url = WDN.toolbar_peoplefinder.serviceURL + 'service.php?view=hcard&uid=' + href[1];
				WDN.get(url, null, service_peoplefinder.updatePeopleFinderRecord);
			}
			return false;
		}
	};
}();

directory = function() {
	return {
		initializeSearchBoxes : function() {
			WDN.jQuery('#peoplefinder').submit(function(eventObject) { //on submit of the search form
				window.location.hash = '#q/' + WDN.jQuery('#'+this.id+' input.q').val() ; //triggering a hash change will run through the searching function
				eventObject.preventDefault();
				eventObject.stopPropagation();
				WDN.jQuery('#q').focus().select();
				return false;
			});
			WDN.jQuery('#q').focus(function(){
				WDN.jQuery(this).siblings('label').hide();
			});
			WDN.jQuery('form.directorySearch ol > li > label').focus(function(){
					WDN.jQuery(this).hide().siblings('input[name=q]').focus();
			});
			if (WDN.jQuery('#q').val() !== "") {
				WDN.jQuery('#q').siblings('label').hide();
			};
			WDN.jQuery('input.q').blur(function() {
				if (WDN.jQuery(this).val() === "") {
					WDN.jQuery(this).siblings('label').show();
				}
			});
			WDN.jQuery('#q').focus().select();
		}
	};
}();

WDN.jQuery(document).ready(function() {
	WDN.loadJS('wdn/templates_3.0/scripts/plugins/hashchange/jQuery.hashchange.1-3.min.js', function() {
		WDN.jQuery(window).bind('hashchange', function(eventObject){
			var hash = location.hash;
			if (hash.match(/[^#q\/]/)) {
				WDN.log('We have a hash match: '+ hash);
				hash = hash.split('/'); //hash[1]
				WDN.jQuery('#q').val(hash[1]);
				service_peoplefinder.presentPeopleFinderResults(hash[1]);
				
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
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = '';
		WDN.toolbar_peoplefinder.configuedWebService = true;
		if (window.location.hash) {
			WDN.jQuery(window).trigger('hashchange');
		}
	});
	directory.initializeSearchBoxes();
	WDN.jQuery('a.img-qrcode').live('click', function() {
		WDN.jQuery(this).colorbox({open:true});
		return false;
	});
});
WDN.jQuery(window).keydown(function(event) {
	if (event.which == '191') {
		WDN.jQuery('#q').focus().select();
		event.preventDefault();
		event.stopPropagation();
	}
});