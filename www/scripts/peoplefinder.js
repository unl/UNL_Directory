var service_peoplefinder = function() {
	return {
		updatePeopleFinderResults : function(){ //function called when the list has been rendered
			if (WDN.jQuery("#results:contains('Sorry, no results could be found.')").length > 0 && attempts < 3) {
				if (splitName == false && originalSearch.indexOf(' ') > 0) { //user did a simple search with a space, so try an advanced search
					WDN.jQuery("#results").empty();
					splitQuery = originalSearch.split(' ',2);
					service_peoplefinder.anotherAttempt(splitQuery[0] ,splitQuery[1].substring(0,1));
				}
				if (splitName == true) { //user did an adavanced search, let's try first letter first name, whole last name
					if (attempts == 2) { //on our second attempt
						splitQuery = originalSearch.split(' ',2);
						service_peoplefinder.anotherAttempt(splitQuery[0].substring(0,1) ,splitQuery[1]);
					} else { //user did first search from advanced search
						splitQuery = originalSearch.split(' ',2);
						service_peoplefinder.anotherAttempt(splitQuery[0] ,splitQuery[1].substring(0,1));
					}
				}
				
				return false;
			} else if (attempts > 1){
				//we finally have results, or else we've abandonded the search options
				directory.showSearchNotice();
			}
			WDN.loadJS('scripts/filters.js', function(){
				filters.initialize();
			});
			WDN.jQuery('ul.pfResult li').each(function(){
				//onClick = WDN.jQuery(this).find('.cInfo').attr('onclick');
				WDN.jQuery('.cInfo, .fn a', this).removeAttr('onclick');
			});

            directory.initializeSearchResultListeners();
		},
		
		anotherAttempt : function(firstName, lastName) {
			window.location.hash = '#q/' + firstName + '/' +lastName;
			directory.buildSearchNotice(originalSearch, firstName, lastName);
			attempts++;
			WDN.log(attempts);
		},
		
		updatePeopleFinderRecord : function(data, textStatus){ //function called when a record has been rendered
			if (textStatus == 'success') {
				correctionHTML = 
					'<a href="http://www1.unl.edu/comments/" class="dir_correctionRequest pf_record">Have a correction?</a>';
				WDN.jQuery('li.current').append(data);
				WDN.jQuery('li.current .vcardInfo').append(correctionHTML);
				WDN.jQuery('input[name="page_address"]').val(WDN.jQuery('li.current .permalink').attr('href'));
				if (WDN.jQuery('.wdn_annotate')) {
					if (!WDN.jQuery('head link[href="'+ ANNOTATE_URL +'css/annotate.css"]').length) {
						WDN.loadCSS(ANNOTATE_URL + 'css/annotate.css');
					}
					WDN.loadJS(ANNOTATE_URL + 'scripts/annotate_functions.js', function() {
						annotate.path = ANNOTATE_URL + '?view=annotation';
						annotate.initialize();
					});
				}
				WDN.jQuery('li.current .vcard a.planetred_profile').fadeIn(400);
				WDN.jQuery('li.current .vcard').slideDown();
				WDN.jQuery('li.selected .loading').hide();
                WDN.jQuery('.ppl_Sresult .vcard, .dep_result .vcard').on('click', function(event){
                        event.stopPropagation();
                    }
                );
                directory.initializeCorrectionForms();
			}
		},
		
		presentPeopleFinderResults : function(){
			WDN.jQuery('#filters').css({'opacity' : '0.4'});
			//WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('.help-container').remove();
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
			if (liRecord.hasClass('selected')) {
				liRecord.children('.vcard').children('a.planetred_profile').fadeOut(400);
				liRecord.children('.vcard').slideUp(function(){
					WDN.jQuery(this).remove();
				});
				liRecord.removeClass('selected');
			} else {
				if (liRecord.children('.loading').length == 0) {
					liRecord.append($progress);
				}
				
				liRecord.children('.loading').show();
				WDN.jQuery('li.current').removeClass('current');
				liRecord.addClass('selected current');
				var href = liRecord.find('a.cInfo').attr('href');
				href = href.split('?uid=');
				var url = WDN.toolbar_peoplefinder.serviceURL + 'index.php?view=hcard&format=partial&uid=' + href[1];
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
				WDN.jQuery("#searchNotice").slideUp();
				if (WDN.jQuery('#'+this.id+' input.q').val().length || WDN.jQuery('#'+this.id+' input.q:gt(0)').val().length) {
					if((WDN.jQuery('#cn').length > 0) || (WDN.jQuery('#sn').length > 0)){
						window.location.hash = '#q/' + WDN.jQuery('#cn').val() + '/' + WDN.jQuery('#sn').val();
						originalSearch = WDN.jQuery('#cn').val() + ' ' + WDN.jQuery('#sn').val();
						WDN.jQuery('#cn').focus().select();
					} else {
						window.location.hash = '#q/' + WDN.jQuery('#'+this.id+' input.q').val(); //triggering a hash change will run through the searching function
						WDN.jQuery('#q').focus().select();
						originalSearch = WDN.jQuery('#'+this.id+' input.q').val();
					}
				}
				eventObject.preventDefault();
				eventObject.stopPropagation();
				return false;
			});
			WDN.jQuery('#advancedSearch').bind({
				focus : function(){
					WDN.jQuery("#queryString").remove();
				},
				click : function(){
					directory.splitSearchBoxes('','');
					return false;
				}
			});
			directory.fixLabel();
		},
		
		fixLabel : function() { //called to reposition the label over the input and hide
			WDN.jQuery('.directorySearch > fieldset > ol > li > label').css({'top' : '3px'}).focus(function(){
					WDN.jQuery(this).hide().siblings('input[type=text]').next().focus();
			});
		},
		
		splitSearchBoxes : function(cn, sn) { //function called to prepare the advanced search boxes
			if (WDN.jQuery('#peoplefinder').length){
				WDN.jQuery("#queryString, #q").remove();
				WDN.jQuery('label.cn, input#cn, label.sn, input#sn').remove();
                WDN.jQuery('#peoplefinder').addClass('advanced');
				WDN.jQuery('#peoplefinder .input-group').prepend('<input type="text" id="cn" name="cn" title="First Name" placeholder="First Name" class="n q" /><input type="text" id="sn" name="sn" title="Last Name" placeholder="Last Name" class="s n q" />');
			}
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
			directory.fixLabel();
		},
		
		combineSearchBoxes : function() { //function called to prepare the simple search box
			WDN.jQuery('#sn, #cn, label.cn, label.sn').remove();
            WDN.jQuery('#peoplefinder').removeClass('advanced');
			WDN.jQuery('#advancedSearch').unbind('click').removeClass('simple').addClass('advanced').text('Advanced Search').bind({
				click : function(eventObject){
					directory.splitSearchBoxes('','');
					eventObject.preventDefault();
					eventObject.stopPropagation();
					return false;
				}
			});
			WDN.jQuery('#peoplefinder .input-group').prepend('<input type="text" autofocus placeholder="Enter a name"id="q" name="q" title="Enter a name to begin your search" placeholder="Enter a name" class="q" />');
			directory.fixLabel();
		},
		
		buildSearchNotice : function(originalSearch, firstName, lastName) {
			WDN.jQuery("#searchNotice").remove();
			WDN.jQuery("#directoryHelp").after('<p id="searchNotice">Your original search for <span>'+originalSearch+'</span> did not return any results. So we tried a few more advanced searches and below is what we found for <span>First Name: '+firstName+' AND Last Name: '+lastName+'</span>.');
		},
		
		showSearchNotice : function() {
			WDN.jQuery("#searchNotice").slideDown(500);
			attempts = 1;
		},
		
		showIndividualDepartmentRecord : function(liRecord) {
			if (liRecord.hasClass('selected')) {
				//liRecord.children('.departmentInfo').fadeOut(400);
				liRecord.children('.departmentInfo').slideUp(function(){
					WDN.jQuery(this).remove();
					liRecord.removeClass('selected');
				});
			} else {
				liRecord.children('.loading').show();
				WDN.jQuery('li.current').removeClass('current');
				liRecord.addClass('selected current');
				var href = liRecord.find('a.cInfo').attr('href');
				var url = href + '/summary?format=partial';
				WDN.get(url, null, service_peoplefinder.updatePeopleFinderRecord);
			}
			return false;
		},
        
        initializeCorrectionForms : function() {
            WDN.jQuery('a.dir_correctionRequest').on('click', function(){
                
                WDN.initializePlugin('modal', [function() {
                    WDN.jQuery(this).colorbox({
                        inline : true,
                        open : true,
                        href : WDN.jQuery('div.commentProblem'),
                        width : '80%',
                        height : '90%',
                        title : false,
                        onOpen : function() {
                            if (!(WDN.jQuery(this).hasClass('pf_record'))){
                                WDN.jQuery(this).siblings('.commentProblem').children('form').children('input[name="page_address"]').val(window.location);
                            }
                        }
                    });
                }]);
                return false;
            });
        },
        
        initializeSearchResultListeners : function() {
            WDN.jQuery('.ppl_Sresult').on('click', function(){
                    service_peoplefinder.showIndividualPeopleFinderRecord(WDN.jQuery(this));
                    return false;
                }
            );
            WDN.jQuery('.dep_result').on('click', function(){
                    directory.showIndividualDepartmentRecord(WDN.jQuery(this));
                    return false;
                }
            );
            WDN.jQuery('a.img-qrcode').on('click', function() {
                var href = this;
                WDN.initializePlugin('modal', [function() {
                    WDN.jQuery(href).colorbox({open:true});
                    return false;
                }]);
                return false;
            });
        }
	};
}();

WDN.jQuery(document).ready(function() {
    WDN.toolbar_peoplefinder.serviceURL = PF_URL;
    WDN.toolbar_peoplefinder.configuedWebService = true;
    directory.initializeCorrectionForms();
    if (window.location.hash
        && WDN.jQuery('#peoplefinder').length) {
        WDN.log('triggering hash change');
        WDN.jQuery(window).trigger('hashchange');
    }
    
	if (WDN.jQuery('#peoplefinder').length) {
		WDN.loadJS('/wdn/templates_4.0/scripts/plugins/hashchange/jquery.hashchange.min.js', function() {
			attempts = 1; // var used to control how many attempts the automatic search guessing goes through
			WDN.jQuery(window).bind('hashchange', function(eventObject){
				hash = location.hash;
				if (hash.match(/^#q\//)) {
					hash = hash.split('/'); //hash[1]
					splitName = false;
					if(hash.length >= 3){ // if 3, then we're looking for first and last name individually.
						splitName = true;
						cn = unescape(hash[1]);
						sn = unescape(hash[2]);
						directory.splitSearchBoxes(cn, sn);
					} else { // it's all one search term.
						query = unescape(hash[1]);
						if (WDN.jQuery('#cn').length){
							directory.combineSearchBoxes();
						}
						WDN.jQuery('#q').val(query);
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
			if (location.hash != '') {
				//trigger a hash change if a hash has been provided on load
				WDN.jQuery(window).trigger('hashchange');
			}
		});
	}
	
	directory.initializeSearchBoxes();
    directory.initializeSearchResultListeners();
	
	WDN.jQuery('.wdn_feedback_comments2').on('submit', function(event) {
			var comments = WDN.jQuery(this).children('textarea').val();
			//var page_address = WDN.jQuery(this).children().val('input[name="page_address"]');
			if (comments.length < 4) {
				// Users must enter in at least 4 words.
				alert('Please enter more information.');
				return false;
			}
			WDN.post(
				'http://www1.unl.edu/comments/',
				WDN.jQuery(this).serialize(),
				function () {
				}
			);
			WDN.jQuery(this).replaceWith('<h4>Thanks!</h4><p>Your request has been submitted.</p><p>Click the "X" in the top right to close this box.</p>');
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
	);
});
WDN.jQuery(window).keydown(function(event) {
	if (event.which == '191'
			&& WDN.jQuery('#peoplefinder').length) {
		WDN.jQuery('#peoplefinder #q').focus().select();
		event.preventDefault();
		event.stopPropagation();
	}
});