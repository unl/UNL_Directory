var filters = function() {
	var departments = [];
	var affiliations = [];

	/**
	 * Expand / collapse filters
	 */
	WDN.jQuery(".filters legend").on('click keypress', function (e) {
		if (e.keyCode !== undefined && !(e.keyCode == 0 || e.keyCode == 13)) {
			//Not a space or enter key press
			return;
		}
		$header  = WDN.jQuery(this);
		$container = $header.next();
		$content = $container.find('ol');
		$content.slideToggle(100, function () {
			if ($content.is(":visible")) {
				//Expanded
				$header.find('.toggle').text("(Collapse)");
				$container.attr('aria-expanded', 'true');
				$content.focus();
			} else {
				//Collapsed
				$header.find('.toggle').text("(Expand)");
				$container.attr('aria-expanded', 'false');
			}
		});
	});

	return {
		initialize : function() {
			WDN.jQuery('form.filters fieldset ol').empty().parents('form').addClass('loading');
			WDN.jQuery('#filters').show();
			filters.findClasses();

			//Hide the filters if there is only a few results (on mobile)
			WDN.jQuery('#filters').removeClass('few-results');
			WDN.jQuery('#filters').removeClass('many-results');
			var total = WDN.jQuery('.ppl_Sresult');
			if (total.length <= 10) {
				WDN.jQuery('#filters').addClass('few-results');
			} else {
				WDN.jQuery('#filters').addClass('many-results');
			}
		},
		
		findClasses : function() {
			WDN.jQuery('.results').each(function() {
				if (WDN.jQuery(this).hasClass('departments')) { //for department filters
					
				} else {
					WDN.jQuery(WDN.jQuery(this).find('.organization-unit')).each(function() { //find the departments from the people records
						filters.departmentArray(WDN.jQuery(this).text());
						WDN.jQuery(this).parents('li.ppl_Sresult,tr.ppl_Sresult').addClass(filters.scrubDept(WDN.jQuery(this).text().toLowerCase()));
					});
					affiliations.push(WDN.jQuery(this).children('h3').eq(0).text());
				}
			});
			departments.sort();	
			affiliations.sort();
			
			filters.buildFilters(departments, 'department');
			filters.buildFilters(affiliations, 'affiliation');
			
			filters.cleanUp();
		},
		
		departmentArray : function(refDepartment) {
			//WDN.log(refDepartment+': '+WDN.jQuery.inArray(refDepartment, departments));
			if (WDN.jQuery.inArray(refDepartment, departments) < 0) {
				departments.push(refDepartment);
			}
			
		},
		
		buildFilters : function(array, type) {
			if (WDN.jQuery('#filterAll'+type).length == 0) {
				WDN.jQuery('fieldset.'+type+' ol').append('<li><input type="checkbox" id="filterAll'+type+'" name="all" value="all" class="filterAll" checked="checked" /><label for="filterAll'+type+'" >All</label></li>');
			}
			WDN.jQuery.each(array, function(key, value){
				WDN.jQuery('fieldset.'+type+' ol').append('<li><input type="checkbox" id="filter'+filters.scrubDept(value.toLowerCase())+'" name="'+filters.scrubDept(value.toLowerCase())+'" value="'+filters.scrubDept(value.toLowerCase())+'" /><label for="filter'+filters.scrubDept(value.toLowerCase())+'" >'+value+'</label></li>');
			});
		},
		
		cleanUp: function() {
			WDN.jQuery('form.filters').removeClass('loading').parents('#filters').css({'opacity' : 1});
			WDN.jQuery('form.filters input').bind('click', function(){
				filters.action(WDN.jQuery(this));
			});
			filters.buildSummary();
			departments = [];
			affiliations = [];
		},
		
		action : function(checkbox) {
			checked = [];
			if (checkbox.hasClass('filterAll')) {
				if (checkbox[0].checked){
					filters.showAll();
				}
			} else {
				WDN.jQuery('.filterAll').removeAttr('checked');
				WDN.jQuery('div.affiliation, div.results ul li, div.results tbody tr').hide();
				WDN.jQuery('form.filters input').not('.filterAll').each(function(){ //loop through all the checkboxes
					if (this.checked) {
						WDN.jQuery('li.'+WDN.jQuery(this).attr('value')+',tr.'+WDN.jQuery(this).attr('value')).show().parents('.affiliation').show(); //if a checkbox is checked, make sure the corresponding content is shown.
						checked.push(WDN.jQuery(this).attr('id'));
					}
				});
			}
			filters.updateSummary(checked);
		},
		
		buildSummary: function() {
			WDN.jQuery('#results').prepend('<p id="filterSummary">Displaying People: <span class="all selected-options">All Options</span>');
		},
		
		updateSummary: function(ids) { //this function recives an array of all checked filters
			if (ids.length < 1) { //nothing in the array, therefore it's ALL
				WDN.jQuery('#filterSummary .selected-options, span.operator').remove();
				WDN.jQuery('#filterSummary').append('<span class="all selected-options">All Options</span>');
				WDN.jQuery('.filterAll').attr('checked', 'checked');
				filters.showAll();
			} else { //at least one id exists in the array
				WDN.jQuery('#filterSummary .selected-options, span.operator').remove();
				WDN.jQuery.each(ids, function(key, value){
					var $legend = WDN.jQuery('#'+value).closest('fieldset').children('legend').children('span');
					console.log($legend.text());
					var text = $legend.clone().children().remove().end().text();
					console.log(text);
					WDN.jQuery('#filterSummary').append(' <span class="'+WDN.jQuery('#'+value).attr('value')+' selected-options"><span class="group">'+text+':</span> '+WDN.jQuery('#'+value).siblings('label').text()+'</span><span class="operator"> OR </span>');
				});
			}
		},
		
		scrubDept : function(string) {
			return string.split(' ').join('').replace(/&|,/gi, '');
		},
		
		showAll : function() {
			WDN.jQuery('form.filters input').not('.filterAll').removeAttr('checked');
			WDN.jQuery('.filterAll').attr('checked', 'checked');
			WDN.jQuery('div.affiliation, div.results ul li, div.results tbody tr').show();
		}
		
	};
}();