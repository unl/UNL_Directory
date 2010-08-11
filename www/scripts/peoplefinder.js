service_peoplefinder = function() {
	return {
		updatePeopleFinderResults : function(){ //function called when the list has been rendered
			WDN.jQuery('ul.pfResult li').each(function(){
				//onClick = WDN.jQuery(this).find('.cInfo').attr('onclick');
				//WDN.jQuery(this).find('.cInfo, .fn a').removeAttr('onclick');
			});
			WDN.jQuery('ul.pfResult:not(.departments) li .overflow').click(function() {
				WDN.jQuery(this).css({'opacity' : '0.4'});
				WDN.jQuery('li.selected').removeClass('selected');
				WDN.jQuery(this).parent('li').addClass('selected');
				var href = WDN.jQuery(this).find('a.cInfo').attr('href');
				href = href.split('?uid=');
				var url = WDN.toolbar_peoplefinder.serviceURL + 'service.php?view=hcard&uid=' + href[1];
				WDN.get(url, null, service_peoplefinder.updatePeopleFinderRecord);
				return false;
			});
		},
		updatePeopleFinderRecord : function(data, textStatus){ //function called when a record has been rendered
			if (textStatus == 'success') {
            	//WDN.jQuery('li.selected .overflow').hide();
				WDN.jQuery('li.selected').append(data);
				WDN.jQuery('li.selected .vcard').slideDown();
            } else {
                
            }
		},
		presentPeopleFinderResults : function(query){
			WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('#maincontent div').not('.clear').remove();
			WDN.jQuery('#peoplefinder').after('<div id="filters" class="one_col left"></div><div id="results" class="three_col right"></div>');
			WDN.toolbar_peoplefinder.queuePFRequest(query, 'results');
			document.title = 'UNL | Directory | Search for ' + query;
		}
	};
}();
/*
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
*/
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

WDN.jQuery(function(){
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