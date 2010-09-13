WDN.jQuery(document).ready(function(){
	WDN.loadJS('/wdn/templates_3.0/scripts/plugins/jeditable/jquery.jeditable.js', function(){
		
	});
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
			saveSortOrder(this);
		},
		items : '> li'
	});

});

function saveSortOrder(list) {//this function determines the order of the list and sends it to the DB.
	WDN.jQuery(list).sortable('refresh');
	var results = WDN.jQuery(list).sortable('toArray');

	for (i = 0; i<results.length; i++) {
		if (WDN.jQuery('#'+results[i]+' form.sortform input[name=sort_order]').attr('value') != i+1) {
			WDN.jQuery('#'+results[i]+' form.sortform input[name=sort_order]').attr('value', i+1);
			WDN.jQuery.post(WDN.jQuery('#'+results[i]+' form.sortform').attr('action'), WDN.jQuery('#'+results[i]+' form.sortform').serialize());
		}
	}
}