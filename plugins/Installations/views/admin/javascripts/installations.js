
var Installations = {
	
	approve: function() {
		id = jQuery(this).attr('id').substring(8);
		Installations.element = this;
		jQuery.post("approve/", {'id': id}, Installations.approveResponseHandler);
	
	},
	
	approveResponseHandler: function(response, a, b) {
		response = JSON.parse(response);
		jQuery(Installations.element).replaceWith(response.added);
	}
};



jQuery(document).ready(function() {
	jQuery('.approve').click(Installations.approve);	
}); 