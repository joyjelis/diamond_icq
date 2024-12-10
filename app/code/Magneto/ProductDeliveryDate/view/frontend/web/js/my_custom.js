require(['jquery'], function($){ 
		//$("#cart-estimate-delivery-date").show();
var findcarttotals = setInterval(function(){ 
	if($("#cart-totals .grand.totals").length>0){
		$("#cart-estimate-delivery-date").insertAfter("#cart-totals .grand.totals");
		$("#cart-estimate-delivery-date").show();
		clearInterval(findcarttotals);
	}
 }, 200);

var findmniwrapper = setInterval(function(){ 
	if($(".minicart-items .minicart-items-wrapper").length>0){
		$("#cart-estimate-delivery-date").insertAfter(".minicart-items .minicart-items-wrapper");
		setTimeout(function(){ $("#cart-estimate-delivery-date").show(); }, 1500);
		clearInterval(findmniwrapper);
	}
 }, 200);


})