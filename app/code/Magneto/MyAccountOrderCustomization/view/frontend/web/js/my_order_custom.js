require(['jquery'], function($){ 
	$(".viewOptionBtn").click(function() {
	  var currentItemId = $(this).attr('data-current-item-id');
	  $("#order_item_options_"+currentItemId).toggleClass("show");
	});

})