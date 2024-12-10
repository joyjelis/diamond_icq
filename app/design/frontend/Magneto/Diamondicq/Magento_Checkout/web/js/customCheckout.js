require(['jquery', 'jquery/ui'], function($){
	jQuery(document).ready(function () {

		var flag = 0

		if (flag == 0) {
			setInterval(function(){ 
				if ( jQuery("#customer-email-fieldset .emailLabel").length){
		    	  flag = 1;
		    	  jQuery("#customer-email-fieldset .emailLabel").insertAfter("#customer-email-fieldset .input-text.email-input");
				}
			}, 3000);
		}

	});
});
