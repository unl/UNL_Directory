filters = function() {
	var departments = [];
	return {
		initialize : function() {
			WDN.jQuery('form.filters fieldset ol').empty().parents('form').addClass('loading');
			WDN.jQuery('#filters').show();
			filters.findClasses();
		},
		
		findClasses : function() {
			WDN.jQuery('.results').each(function() {
				if (WDN.jQuery(this).hasClass('departments')) { //get the departments listings
					WDN.jQuery(WDN.jQuery(this).find('.fn a')).each(function() {
						filters.departmentArray(WDN.jQuery(this).text());
					});
				} else {
					WDN.jQuery(WDN.jQuery(this).find('.organization-unit')).each(function() { //find the people records with departments
						filters.departmentArray(WDN.jQuery(this).text());
					});
				}
			});
			departments.sort();	
			filters.buildDepartmentFilters();
		},
		
		departmentArray : function(refDepartment) {
			WDN.log(WDN.jQuery.inArray(refDepartment, departments));
			if (WDN.jQuery.inArray(refDepartment, departments) <= 0) {
				WDN.log('not in array: '+refDepartment);
				departments.push(refDepartment);
			}
			
		},
		
		buildDepartmentFilters : function() {
			WDN.jQuery.each(departments, function(key, value) {
				WDN.jQuery('fieldset.department ol').append('<li><input type="checkbox" id="filter'+value+'" name="'+value+'" value="'+value+'" /><label for="filter'+value+'" >'+value+'</label></li>');
			});
			departments = [];	
			WDN.jQuery('form.filters').removeClass('loading');
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