var service_peoplefinder = function() {
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
			WDN.jQuery('#breadcrumbs ul').append('<li>Search for '+query);
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

WDN.jQuery(document).ready(function() {
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = WDN.toAbs('../', window.location.protocol + '//' + window.location.host + window.location.pathname);
		WDN.toolbar_peoplefinder.configuedWebService = true;
	});
	WDN.jQuery('ul.pfResult:not(.departments) li .overflow').click(function(){
		service_peoplefinder.showIndividualPeopleFinderRecord(WDN.jQuery(this));
		return false;
		}
	);
	WDN.jQuery('a.img-qrcode').live('click', function() {
		WDN.jQuery(this).colorbox({open:true});
		return false;
	});
});