var admin_editting = function() {
	return {
		initialize : function() { //called each time maincontent loads
			WDN.tabs.initialize();
			admin_editting.bindEditLinks();
			admin_editting.bindSortable();
			admin_editting.bindColorbox();
		},
		
		submitForm : function() {
			WDN.jQuery('#colorbox form').submit(function() {
				WDN.jQuery.post(WDN.jQuery('.departmentInfo form').eq(0).attr('action') + '&format=partial&redirect=0', 
				WDN.jQuery(this).serialize(),
				function(data) { //reload the maincontent with the new changes
					WDN.jQuery('#maincontent').html(data);
					WDN.jQuery.colorbox.close();
					admin_editting.initialize();
				}
			);
				return false;
			});
		},
		
		bindColorbox : function() {
			WDN.jQuery('a.edit').colorbox({
				href: function() {
					href = WDN.jQuery(this).attr('href');
					return href.replace('format=editing', 'format[]=editing&format[]=partial')
				},
				width: '75%',
				height: '75%',
				onComplete : function(){
					admin_editting.submitForm();
				}
			});
		},
		
		bindSortable : function() {
			WDN.jQuery('ul.listings').sortable({ //make all the lists on the edit interface sortable
				revert: false,
				scroll: true,
				delay: 100,
				opacity: 0.45,
				tolerance: 'pointer',
				helper: 'clone',
				start: function(event, ui){
					
				},
				stop: function(event, ui){
					admin_editting.saveSortOrder(this);
				},
				items : 'li'
			});
		},
		
		saveSortOrder : function(list) { //this function determines the order of the list and sends it to the DB.
			WDN.jQuery(list).sortable('refresh');
			var results = WDN.jQuery(list).sortable('toArray');

			for (i = 0; i<results.length; i++) {
				if (WDN.jQuery('#'+results[i]+' > div.edit form.sortform input[name=sort_order]').attr('value') != i+1) {
					WDN.jQuery('#'+results[i]+' > div.edit form.sortform input[name=sort_order]').attr('value', i+1);
					WDN.jQuery.post(WDN.jQuery('#'+results[i]+' > div.edit form.sortform').attr('action')+'&redirect=0',
									WDN.jQuery('#'+results[i]+' > div.edit form.sortform').serialize());
				}
			}
		},
		
		bindEditLinks : function() {
			WDN.jQuery('a.edit').colorbox(
				{
					width : '75%', 
					height : '75%', 
					onComplete : function(){admin_editting.submitForm();}
				}
			);
		},
		
		formatDepartmentLists : function() {
			WDN.jQuery('#editBox li').not(':last-child').children('a').after(',');
		}
	};
}();
WDN.jQuery(document).ready(function(){
	admin_editting.initialize();
});