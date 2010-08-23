filters = function() {
	var departments = [];
	var affiliations = [];
	return {
		initialize : function() {
			WDN.jQuery('form.filters fieldset ol').empty().parents('form').addClass('loading');
			WDN.jQuery('#filters').show();
			filters.findClasses();
		},
		
		findClasses : function() {
			WDN.jQuery('.results').each(function() {
				if (WDN.jQuery(this).hasClass('departments')) { //for department filters
					
				} else {
					WDN.jQuery(WDN.jQuery(this).find('.organization-unit')).each(function() { //find the departments from the people records
						filters.departmentArray(WDN.jQuery(this).text());
						WDN.jQuery(this).parents('li.ppl_Sresult').addClass(filters.scrubDept(WDN.jQuery(this).text().toLowerCase()));
					});
					affiliations.push(WDN.jQuery(this).children('h2').eq(0).text());
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
			WDN.jQuery('fieldset.'+type+' ol').append('<li><input type="checkbox" id="filterAll'+type+'" name="all" value="all" class="filterAll" checked="checked" /><label for="filterAll'+type+'" >All</label></li>');
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
			if(checkbox.hasClass('filterAll')){
				if (checkbox[0].checked){
					WDN.jQuery('form.filters input').not('.filterAll').removeAttr('checked');
					WDN.jQuery('.filterAll').attr('checked', 'checked');
					WDN.jQuery('li.ppl_Sresult').show();
				} 
			} else {
				WDN.jQuery('.filterAll').removeAttr('checked');
				WDN.jQuery('li.ppl_Sresult').hide();
				WDN.jQuery('form.filters input').not('.filterAll').each(function(){ //loop through all the checkboxes
					if (this.checked) {
						WDN.jQuery('li.'+WDN.jQuery(this).attr('value')).show(); //if a checkbox is checked, make sure the corresponding content is shown.
						checked.push(WDN.jQuery(this).attr('id'));
					}
				});
			}
			filters.updateSummary(checked);
		},
		
		buildSummary: function() {
			WDN.jQuery('#results').prepend('<p id="filterSummary">Displaying: <a class="all" href="#">All Options</a>');
		},
		
		updateSummary: function(ids) { //this function recives an array of all checked filters
			if (ids.length < 1) { //nothing in the array, therefore it's ALL
				WDN.jQuery('#filterSummary a').remove();
				WDN.jQuery('#filterSummary').append('<a href="#" class="all">All Options</a>');
			} else { //at least one id exists in the array
				WDN.jQuery('#filterSummary a').remove();
				WDN.jQuery.each(ids, function(key, value){
					WDN.jQuery('#filterSummary').append(' <a href="#" class="'+WDN.jQuery('#'+value).attr('value')+'"><span class="group">'+WDN.jQuery('#'+value).closest('fieldset').children('legend').text()+':</span> '+WDN.jQuery('#'+value).siblings('label').text()+'</a><span class="operator"> OR </span>');
				});
			}
			
		},
		
		scrubDept : function(string) {
			return string.split(' ').join('').replace(/&|,/gi, '');
			
		}
		
	};
}();

WDN.jQuery('h2.resultCount').after('<p id="filterSummary">Displaying: <a href="#" class="all">All Options</a></p>');
WDN.jQuery('form.filters input').each(function(){
	if (WDN.jQuery(this).attr('value') !== "all") {
		// Check and see if we actually have any of these courses
		var total = WDN.jQuery('.'+WDN.jQuery(this).attr('value')).length; //count all based on class
		if (total == 0) {
			WDN.jQuery(this).attr('disabled', 'disabled'); //disable the input/label
			WDN.jQuery(this).closest('li').addClass('disabled');
			return true;
		} else {
			if (WDN.jQuery(this).closest('form').hasClass('courseFilters')) { //otherwise calculate the count
    			total = total/2;
    		}
			WDN.jQuery('label[for='+this.id+']').append(' <span class="count">('+total+')</span>'); // add the count
		}
	}
	WDN.jQuery(this).click(function() {
		if (WDN.jQuery(this).hasClass('filterAll')) { //if all was checked, then put the checkmark next to all alls, and show everything.
			if (this.checked){
				WDN.jQuery('form.filters input').not('.filterAll').removeAttr('checked');
				WDN.jQuery('.filterAll').attr('checked', 'checked');
				WDN.jQuery('dd.course, dt.course, #majorListing li').show();
				WDN.jQuery('#filterSummary a').remove();
				WDN.jQuery('#filterSummary').append('<a href="#" class="all">All Options</a>');
				WDN.jQuery('h2.resultCount span').remove();
			}
		} else {
			WDN.jQuery('.filterAll').removeAttr('checked'); //uncheck the all checkboxes
			WDN.jQuery('dd.course, dt.course, #majorListing li').hide(); //hide all the coures and majors
			var one_checked = false;
			WDN.jQuery('#filterSummary a').remove();
			WDN.jQuery('form.filters input').not('.filterAll').each(function(){ //loop through all the checkboxes
				if (this.checked) {
				    one_checked = true;
					WDN.jQuery('li.'+WDN.jQuery(this).attr('value')+', dd.'+WDN.jQuery(this).attr('value')+', dt.'+WDN.jQuery(this).attr('value')).show(); //if a checkbox is checked, make sure the corresponding content is shown.
					WDN.jQuery('#filterSummary a.all').remove();
					//WDN.jQuery('#filterSummary').append(' <a href="#" class="'+WDN.jQuery(this).attr('value')+'"><span class="group">'+WDN.jQuery(this).closest('fieldset').children('legend').text()+':</span> '+WDN.jQuery(this).siblings('label')[0].childNodes[0].nodeValue+'</a>')
					WDN.jQuery('#filterSummary').append(' <a href="#" class="'+WDN.jQuery(this).attr('value')+'"><span class="group">'+WDN.jQuery(this).closest('fieldset').children('legend').text()+':</span> '+WDN.jQuery(this).siblings('label').text()+'</a>');
				}
			});
			totalDisplayed = WDN.jQuery('dt.course:visible, #majorListing li:visible').length;
			WDN.jQuery('h2.resultCount span').remove();
			WDN.jQuery('h2.resultCount').prepend('<span>'+totalDisplayed+' of </span> ');
			if (one_checked == false) { //no checkboxes are checked, so show all
			    WDN.jQuery('dd.course, dt.course, #majorListing li').show();
			    WDN.jQuery('.filterAll').attr('checked', 'checked');
			    WDN.jQuery('h2.resultCount span').remove();
			}
		}
	});
});