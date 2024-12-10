require(['jquery', 'jquery/ui'], function($){
	jQuery(document).ready(function () {

			var flag = flag1 = 0;

			setInterval(function(){
				if (jQuery('.customer-address-form .page-title-wrapper').length && flag == 0) {
					flag = 1;
					var back = '<a href="javascript:history.back()" class="back-arrow" id="backorder"></a>';
					jQuery('.page-title-wrapper').prepend(back);
				}

				if(jQuery('.title.block-collapsible-nav-title').length && flag1 == 0){
					flag1 = 1;
					jQuery('.title.block-collapsible-nav-title').html(jQuery('#block-collapsible-nav .current').html());
				}
			}, 500);


			$('#backorder').on('click', function(e){
			    e.preventDefault();
			    window.history.back();
			});

	});
});
