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
		
		presentPeopleFinderResults : function(){
			WDN.jQuery('#filters').css({'opacity' : '0.4'});
			WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('#maincontent div.two_col').remove();
			if (!splitName) {
				WDN.toolbar_peoplefinder.queuePFRequest(query, 'results');
			} else {
				WDN.toolbar_peoplefinder.queuePFRequest('', 'results', '', cn, sn);
				query = cn +" "+ sn;
			}
			document.title = 'UNL | Directory | Search for ' + query;
			WDN.jQuery("#breadcrumbs ul li:last").remove();
			WDN.jQuery('#breadcrumbs ul').append('<li>Search for '+WDN.jQuery('<div/>').text(query).html()+'</li>');
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

var directory = function() {
	return {
		initializeSearchBoxes : function() {
			WDN.jQuery('#peoplefinder').submit(function(eventObject) { //on submit of the search form
				if (WDN.jQuery('#'+this.id+' input.q').val().length) {
					if(WDN.jQuery('#cn').length > 0){
						window.location.hash = '#q/' + WDN.jQuery('#cn').val() + '/' + WDN.jQuery('#sn').val();
						WDN.jQuery('#cn').focus().select();
					} else {
						window.location.hash = '#q/' + WDN.jQuery('#'+this.id+' input.q').val(); //triggering a hash change will run through the searching function
						WDN.jQuery('#q').focus().select();
					}
				}
				eventObject.preventDefault();
				eventObject.stopPropagation();
				return false;
			});
			directory.fixLabel();
		},
		
		fixLabel : function() { //called to reposition the label over the input and hide
			WDN.jQuery('.directorySearch > fieldset > ol > li > label').css({'top' : '16px'}).focus(function(){
					WDN.jQuery(this).hide().siblings('input[type=text]').next().focus();
			});
			WDN.jQuery('.directorySearch input').bind({
				focus : function(){
					WDN.jQuery(this).siblings('label[for='+this.id+']').hide();
				},
				blur : function(){
					if (WDN.jQuery(this).val() === "") {
						WDN.jQuery(this).siblings('label[for='+this.id+']').show();
					}
				}
			});
		},
		
		splitSearchBoxes : function(cn, sn) { //function called to prepare the advanced search boxes
			if (WDN.jQuery('#q').length){
				WDN.jQuery("#queryString, #q").remove();
				WDN.jQuery('#peoplefinder li').prepend('<label for="cn" class="cn">First Name</label><input type="text" value="" id="cn" name="cn" class="n q" /><label for="sn" class="sn">Last Name</label><input type="text" value="" id="sn" name="sn" class="s n q" />');
			}
			WDN.jQuery('#cn, #sn').focus(function(){
				WDN.jQuery(this).prev('label').hide();
			});
			WDN.jQuery('#advancedSearch').unbind('click').removeClass('advanced').addClass('simple').text('Simple Search').bind({
				focus : function(){
					WDN.jQuery("#queryString").remove();
				},
				click : function(eventObject){
					directory.combineSearchBoxes();
					eventObject.preventDefault();
					eventObject.stopPropagation();
					return false;
				}
			});
			WDN.jQuery('#cn').val(cn);
			WDN.jQuery('#sn').val(sn);
			if(cn.length){
				WDN.jQuery('label[for=cn]').hide();
			}
			if(sn.length){
				WDN.jQuery('label[for=sn]').hide();
			}
			directory.fixLabel();
		},
		
		combineSearchBoxes : function() { //function called to prepare the simple search box
			WDN.jQuery('#sn, #cn, label.cn, label.sn').remove();
			WDN.jQuery('#advancedSearch').unbind('click').removeClass('simple').addClass('advanced').text('Advanced Search').bind({
				focus : function(){
					WDN.jQuery("#peoplefinder label").remove();
				},
				click : function(eventObject){
					directory.splitSearchBoxes('','');
					eventObject.preventDefault();
					eventObject.stopPropagation();
					return false;
				}
			});
			WDN.jQuery('#peoplefinder li').prepend('<label for="q" id="queryString">Enter a name to begin your search</label><input type="text" value="" id="q" name="q" class="q" />');
			directory.fixLabel();
		}
	};
}();

WDN.jQuery(document).ready(function() {
	WDN.loadJS('wdn/templates_3.0/scripts/plugins/hashchange/jQuery.hashchange.1-3.min.js', function() {
		WDN.jQuery(window).bind('hashchange', function(eventObject){
			var hash = location.hash;
			if (hash.match(/^#q\//)) {
				hash = hash.split('/'); //hash[1]
				splitName = false;
				if(hash.length == 3){ // if 3, then we're looking for first and last name individually.
					splitName = true;
					cn = hash[1];
					sn = hash[2];
					directory.splitSearchBoxes(cn, sn);
				} else { // it's all one search term.
					query = hash[1];
					if (WDN.jQuery('#cn').length){
						directory.combineSearchBoxes();
					}
					WDN.jQuery('#q').val(hash[1]);
				}
				
				service_peoplefinder.presentPeopleFinderResults();
				
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
		//WDN.toolbar_peoplefinder.serviceURL = window.location.protocol + '//' + window.location.host + window.location.pathname;
		WDN.toolbar_peoplefinder.serviceURL = PF_URL;
		WDN.toolbar_peoplefinder.configuedWebService = true;
		if (window.location.hash) {
			WDN.jQuery(window).trigger('hashchange');
		}
	});
	directory.initializeSearchBoxes();
	WDN.jQuery('#advancedSearch').bind({
		focus : function(){
			WDN.jQuery("#queryString").remove();
		},
		click : function(){
			directory.splitSearchBoxes('','');
			return false;
		}
	});
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