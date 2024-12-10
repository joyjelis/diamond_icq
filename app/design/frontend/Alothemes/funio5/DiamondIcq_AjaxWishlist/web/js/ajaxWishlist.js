define([
    'jquery',
], function($, _, Component, customerData){
        "use strict";
        return function(config, element) {
            // remove data-role when in my wishlist page to prevent default submit
            $('#wishlist-view-form [data-role=remove]').attr('data-role','');

            $('body').on('click', '.actions-secondary .action.towishlist, .btn-remove.action.delete', function(event) {
                var self = this,
                    post = $(self).data(),
                    url  = (post.postRemove) ? post.postRemove.action : post.post.action,
                    data = (post.postRemove) ? post.postRemove.data : post.post.data,
                    data = $.extend(data, {isAjax:1, form_key: $('input[name="form_key"]').val()});
    
                $.ajax(url, {
                    method: 'POST',
                    data: data,
                    showLoader: true,
                    success: function (result) {
						if (result.wishlist) {
							$('#wishlist-product-list').remove();
							$('.column.main').append(result.wishlist);
						}
                        if (result.data) {
                            if (post.postRemove) {
                                $(self).data('post-remove', JSON.parse(result.data));
                                if (!$(self).hasClass('deleted')) {
                                    $(self).addClass('deleted');
                                } else {
                                    $(self).removeClass('deleted');
                                }
                            } else {
                                $(self).data('post', JSON.parse(result.data));
                                if (!$(self).hasClass('already-in-wishlist')) {
                                    $(self).addClass('already-in-wishlist');
                                } else {
                                    $(self).removeClass('already-in-wishlist');
                                }
                            }
                        }
                    }
                }).done(function(data) {
                    //
                }).fail(function () {
                    console.warn("Something went wrong updating the wishlist.");
                });
                
                return false;
            });
        }
    }
)