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
			WDN.jQuery('ul.pfResult.departments li .overflow').click(function(){
				window.location = WDN.jQuery('.fn a', this).attr('href');
			});
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
			}
		},
		
		presentPeopleFinderResults : function(){
			WDN.jQuery('#filters').css({'opacity' : '0.4'});
			//WDN.jQuery('#q').siblings('label').hide();
			WDN.jQuery('#maincontent .grid6, #maincontent .footer').remove();
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
				liRecord.children('.loading').show();
				WDN.jQuery('li.current').removeClass('current');
				liRecord.addClass('selected current');
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
			directory.fixLabel();
		},
		
		fixLabel : function() { //called to reposition the label over the input and hide
			/*
			 WDN.jQuery('.directorySearch input').bind({
				focus : function(){
					WDN.jQuery(this).siblings('label[for='+this.id+']').hide();
				},
				blur : function(){
					if (WDN.jQuery(this).val() === "") {
						WDN.jQuery(this).siblings('label[for='+this.id+']').show();
					}
				},
				keyup : function(){
					if (WDN.jQuery(this).val() !== "") {
						WDN.jQuery(this).siblings('label[for='+this.id+']').hide();
					}
				},
				change : function(){
					if (WDN.jQuery(this).val() !== "") {
						WDN.jQuery(this).siblings('label[for='+this.id+']').hide();
					}
				},
				click : function(){
					if (WDN.jQuery(this).val() !== "") {
						WDN.jQuery(this).siblings('label[for='+this.id+']').hide();
					}
				}
			});
			*/
			WDN.loadJS('wdn/templates_3.0/scripts/plugins/qtip/jquery.qtip.js', function(){
				WDN.jQuery('.directorySearch input#q').qtip({
			    	content: {
			    		text: 'Enter a name to begin your search'
			    	},
			        position : {
			        	corner : {
			        		target : 'topLeft',
			        		tooltip : 'bottomMiddle'
			        	},
			        	container: WDN.jQuery('body'),
			        	adjust: {
			        		x: 200
			        	}
			        },
			        style: { 
			        	tip: { 
			        		corner: 'bottomMiddle' ,
			        		size: { x: 25, y: 15 },
			        		color: '#d7c47f'
			        	},
			        	"width":"300px",
			        	"background-color": '#f7f3c3',
			        	"color" : "#574f30",
			        	classes : {
			        		tooltip : 'searchHelp'
			        	},
			        	border : {
			        		width : 0
			        	}
			        },
			        show: {
			            when: {
			                event: 'focus'
			            }
			        },
			        hide: {
			            when: {
			                event: 'unfocus'
			            },
			            delay: 100,
			            effect: {
			            	length:100
			            }
			        },
			        api : {
			        	beforeHide : function(){
			        		WDN.setCookie('dir_qTip', '1', 3600);
			        	},
			        	
			        	beforeShow : function(){
			        		if (WDN.getCookie('dir_qTip') == 1) {
			        			return false;
			        		}
			        	}
			        }
			    });
			});
			//WDN.jQuery('#q').focus().select();
			WDN.jQuery('.directorySearch > fieldset > ol > li > label').css({'top' : '3px'}).focus(function(){
					WDN.jQuery(this).hide().siblings('input[type=text]').next().focus();
			});
		},
		
		splitSearchBoxes : function(cn, sn) { //function called to prepare the advanced search boxes
			if (WDN.jQuery('#peoplefinder').length){
				WDN.jQuery("#queryString, #q").remove();
				WDN.jQuery('label.cn, input#cn, label.sn, input#sn').remove();
				WDN.jQuery('#peoplefinder fieldset li').prepend('<label for="cn" class="cn">First Name</label><input type="text" value="" id="cn" name="cn" class="n q" /><label for="sn" class="sn">Last Name</label><input type="text" value="" id="sn" name="sn" class="s n q" />');
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
			WDN.jQuery('#peoplefinder fieldset li').prepend('<label for="q" id="queryString">Enter a name to begin your search</label><input type="text" value="" id="q" name="q" class="q" />');
			directory.fixLabel();
		},
		
		buildSearchNotice : function(originalSearch, firstName, lastName) {
			WDN.jQuery("#searchNotice").remove();
			WDN.jQuery("#directoryHelp").after('<p id="searchNotice">Your original search for <span>'+originalSearch+'</span> did not return any results. So we tried a few more advanced searches and below is what we found for <span>First Name: '+firstName+' AND Last Name: '+lastName+'</span>.');
		},
		
		showSearchNotice : function() {
			WDN.jQuery("#searchNotice").slideDown(500);
			attempts = 1;
		}
	};
}();

WDN.jQuery(document).ready(function() {
	if (WDN.jQuery('#peoplefinder').length) {
		WDN.loadJS('wdn/templates_3.0/scripts/plugins/hashchange/jQuery.hashchange.1-3.min.js', function() {
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
		});
	}
	WDN.loadJS('wdn/templates_3.0/scripts/toolbar_peoplefinder.js', function(){
		WDN.toolbar_peoplefinder.serviceURL = PF_URL;
		WDN.toolbar_peoplefinder.configuedWebService = true;
		if (window.location.hash
			&& WDN.jQuery('#peoplefinder').length) {
			WDN.log('triggering hash change');
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
	WDN.jQuery('.ppl_Sresult').live('click', function(){
			service_peoplefinder.showIndividualPeopleFinderRecord(WDN.jQuery(this));
			return false;
		}
	);
	WDN.jQuery('.ppl_Sresult .vcard').live('click', function(event){
			event.stopPropagation();
		}
	);
	WDN.jQuery('a.img-qrcode').live('click', function() {
		WDN.jQuery(this).colorbox({open:true});
		return false;
	});
	WDN.jQuery('.wdn_feedback_comments2').live('submit', function(event) {
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
	WDN.jQuery('a.dir_correctionRequest').live('click', function(){
		WDN.jQuery(this).colorbox({
			inline : true,
			open : true,
			href : WDN.jQuery('div.commentProblem'),
			width : '45%', 
			height : '45%',
			title : false,
			onOpen : function() {
				if (!(WDN.jQuery(this).hasClass('pf_record'))){
					WDN.jQuery(this).siblings('.commentProblem').children('form').children('input[name="page_address"]').val(window.location);
				}
			}
		});
		return false;
	});
});
WDN.jQuery(window).keydown(function(event) {
	if (event.which == '191'
			&& WDN.jQuery('#peoplefinder').length) {
		WDN.jQuery('#peoplefinder #q').focus().select();
		event.preventDefault();
		event.stopPropagation();
	}
});