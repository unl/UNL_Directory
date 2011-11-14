var admin_editting = function() {
	return {
		initialize : function() { //called each time maincontent loads
			admin_editting.appendHref();
			admin_editting.bindSortable();
			admin_editting.bindMinibutton();
			admin_editting.bindColorbox();
		},
		
		submitForm : function() {
			WDN.jQuery('#colorbox form.zenform').submit(function() {
				WDN.jQuery.post(
					window.location.href.replace(window.location.hash, '') + '?format=partial&redirect=0',
					WDN.jQuery(this).serialize(),
					function(data) { //reload the maincontent with the new changes
						WDN.jQuery('#maincontent').html(data);
						WDN.tabs.initialize();
						WDN.jQuery.colorbox.close();
						admin_editting.initialize();
					});
				return false;
			});
		},

		appendHref : function() {
			WDN.jQuery('a.edit[href*=format=editing], a.addchild[href*=format=editing]').each(function(){
				href = this.href;
				WDN.jQuery(this).attr('href', href.replace('format=editing', 'format[]=editing&format[]=partial'));
			});
		},
		
		//new approach to remove dependency on colorbox
		bindMinibutton : function(){
			WDN.jQuery('a.minibutton.edit').not('.selected').click(function(e){
				WDN.jQuery(this).addClass('selected');
				WDN.jQuery(this).siblings('.action_control').children('.form').load(WDN.jQuery(this).attr('href'), function() {
					WDN.jQuery('body').append('<div class="context-overlay" />');
					WDN.jQuery('.context-overlay').click(function(){
						WDN.jQuery('.action_control').hide();
						WDN.jQuery('a.minibutton').removeClass('selected');
						WDN.jQuery('.context-overlay').remove();
					});
					WDN.jQuery('.action_control').show();
				});
				e.preventDefault();
				return false;
			});
			WDN.log('button bound');
		},

		bindColorbox : function() {
			WDN.jQuery('a.edit, a.addchild').not('.minibutton').colorbox({
				width: '740px',
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
					WDN.jQuery.post(WDN.jQuery('#'+results[i]+' > div.edit form.sortform').attr('action')+'?redirect=0',
									WDN.jQuery('#'+results[i]+' > div.edit form.sortform').serialize());
				}
			}
		},
		
		formatDepartmentLists : function() {
			WDN.jQuery('#editBox li').not(':last-child').children('a').after(',');
		}
	};
}();
WDN.jQuery(document).ready(function(){
	admin_editting.initialize();
	WDN.jQuery('#userDepts').hover(function(){
		WDN.get(WDN.jQuery(this).children('a').attr('href')+'&format=partial', function(data){
			listHTML = '<div id="mydeptlist">' + data + '</div>';
			WDN.log(listHTML);
			WDN.jQuery('#userDepts').append(listHTML);
		});
	}, function(){
		WDN.jQuery('#mydeptlist').remove();
	});
	
});
