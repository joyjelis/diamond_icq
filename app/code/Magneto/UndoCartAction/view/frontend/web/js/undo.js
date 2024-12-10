define([
    'jquery',
    'underscore',
    'uiComponent', 
    'ko',
    'Magento_Ui/js/modal/modal', 
    'Magento_Customer/js/model/address-list'
], function($, _, Component, ko, modal, addressList) {
    'use strict'

    $('body').on('click', '.rootUndoRemovedProduct .restoreaction', function(event) {
        $('.rootUndoRemovedProduct .hideafterload').each(function(i, obj) {
            $(obj).data('id', '');
            $('.progressBar', obj).addClass('paused');
        });
    });

    return Component.extend({
        undodata: ko.observable(0),
        restoreItems: ko.observableArray([]),
        timeout: ko.observable(0),

        initialize: function(config) {
            this._super();
            var self = this;
            self.undodata = JSON. parse(atob(config.undodata));
            self.timeout = self.undodata.setting.timeout; 
            if(self.undodata.setting.is_enable == 1){
                _.each(self.undodata.products_ids, function (data) {
                        var url = self.undodata.restore_url;
                        url = url.replace("RESTORE_ID", data.productId);
                        var remove_url = self.undodata.remove_url;
                        remove_url = remove_url.replace("RESTORE_ID", data.productId);
                        var product = {
                            productId: data.productId,
                            b_restore_text: self.undodata.button_label,
                            b_restore_product_name_text: data.info.name,
                            url: url,
                            remove_url: remove_url,
                            data_post: JSON.stringify({action: url, data: {}})
                        };
                        self.restoreItems.push(product);
                });
            }
        },
        loadJsAfterKoRender: function(){
            var self = this;
            if(parseInt(self.timeout) > 0){
                var autohide = setInterval(function() {
                    if ($('.hideafterload').length > 0) {
                        clearInterval(autohide);
                        var timeout = parseInt(self.timeout) * 1000;
                        setTimeout(function(){
                            $('.hideafterload').each(function(i, obj) {
                                var param = {
                                    'remove_id': $(obj).data('id'),
                                };
                                if (!param.remove_id) return;
                                
                                $.ajax({
                                    showLoader: false,
                                    url: $(obj).data('url'),
                                    data: param,
                                    type: "POST",
                                    dataType: 'json'
                                }).done(function(data) {
                                    if (data.success == true) {
                                        $(obj).remove();
                                    }
                                });
                            });
                        }, timeout);
                    }
                }, 100);
            }

        }
    });
})