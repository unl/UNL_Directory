

WDN.jQuery(document).ready(function(){
	
	WDN.jQuery('ul.sortable').sortable({ //make all the lists on the edit interface sortable
		revert: false,
		scroll: true,
		delay: 250,
		opacity: 0.45,
		tolerance: 'pointer',
		helper: 'clone',
		start: function(event, ui){
			
		},
		stop: function(event, ui){
			saveSortOrder(this);
		}
	});

});

function saveSortOrder(list) {//this function determines the order of the list and sends it to the DB.
	WDN.jQuery(list).sortable('refresh');
	var results = WDN.jQuery(list).sortable('toArray');

	for (i = 0; i<results.length; i++) {
		WDN.jQuery('#'+results[i]+' form input[name=sort]').attr('value', i);
		WDN.jQuery.post(WDN.jQuery('#'+results[i]+' form').attr('action'), WDN.jQuery('#'+results[i]+' form').serialize());
	}
}