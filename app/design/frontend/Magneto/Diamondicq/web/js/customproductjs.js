require([
    'jquery', 'jquery/ui'
], function ($) {
    jQuery( document ).ready(function() {
    
    		jQuery('#qtyWrapper #qty').change(function () {
    			var qty = jQuery(this).val();
                var pid = $('#childProductId').text();

                var scalabeQty = jQuery('#product_addtocart_form .stock-available .value').text();
                
                jQuery('#qtyWrapper .message').addClass('no-display');
                jQuery('#product-addtocart-button').attr('disabled',false);
                if (qty <= 0) {
                    jQuery('#qtyWrapper .message.qty').removeClass('no-display');
                    jQuery('#product-addtocart-button').attr('disabled',true);
                }
                else if(parseInt(qty) >= parseInt(scalabeQty) ){
                    jQuery('#qtyWrapper .message.scalabel').removeClass('no-display');
                    jQuery('#product-addtocart-button').attr('disabled',true);
                }
                else{
                    jQuery('#qtyWrapper .message').addClass('no-display');
                    jQuery('#qtyWrapper .addedQty').text(qty);
                }
    			
    		});

        jQuery("#orgPrice .price-wrapper").on('DOMSubtreeModified', function () {
            var price = jQuery(this).children('span').html();
            var orgPrice = jQuery('.custom-price .old-price.sly-old-price .price-wrapper').children('span').html();

            if (typeof price !== "undefined") {
                jQuery('#replacePrice .price-wrapper').text(orgPrice);
                jQuery('#specialPrice .price-wrapper').text(price);
            }
            //alert("Span HTML is now " + jQuery(this).html());
        });

        jQuery("#childProductId").on('DOMSubtreeModified',function(){
            var pid = $(this).text();
            
            if (jQuery('#specialPrice').is(':visible')) {
                jQuery('#replacePrice').addClass('discountPrice');
                jQuery('#replacePrice .price-title .org').removeClass('no-display');
            }
            else{
                jQuery('#replacePrice').removeClass('discountPrice');
                jQuery('#replacePrice .price-title .org').addClass('no-display');
            }

            var baseUrl = jQuery('#baseUrl').val();

            var url = baseUrl + 'custom/index/stock/';

            if (pid != '') {
                $.ajax({
                    url : url,
                    type : 'post',
                    dataType:'json',
                    data: {
                        'pid': pid
                    },
                    showLoader: true,
                    success : function(data) { 
                        //if (data.qty) {
                            jQuery('.box-stock .count').text(data.qty);
                            jQuery('.stock-available .value').text(data.qty);
                        //}

                        jQuery('#replacePrice .discount-per .per').text(data.discount);
                        if (data.discount) {
                            jQuery('#replacePrice .discount-per').removeClass('no-display');
                        }
                        else{
                            jQuery('#replacePrice .discount-per').addClass('no-display');
                        }
                        
                    }
                });
            }
            
        });
    });                 
});
