define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'mage/url'
    ],
    function(ko, $, Component, url) {
        'use strict';
        var customCheckbox_Yes_radio = setInterval(function() {
            if ($("#customCheckbox_Yes").length > 0) {
                //$("#customCheckbox_Yes").click();   
                $("#customCheckbox_Yes").prop("checked", true).trigger("click");
                clearInterval(customCheckbox_Yes_radio);
            }
        }, 150);
        return Component.extend({
            defaults: {
                template: 'Magneto_IncludePriceReceipt/checkout/customCheckbox'
            },
            initObservable: function() {

                this._super()
                    .observe({
                        CheckVals: ko.observable(false)
                    });
                var checkVal = 0;
                self = this;
                this.CheckVals.subscribe(function(newValue) {
                    var linkUrls = url.build('module/checkout/saveInQuote');
                    if (newValue == 1) {
                        checkVal = 1;
                    } else {
                        checkVal = 0;
                    }
                    checkVal = newValue;
                    
                    $.ajax({
                        showLoader: true,
                        url: linkUrls,
                        data: { checkVal: checkVal },
                        type: "POST",
                        dataType: 'json'
                    }).done(function(data) {
                        
                    });
                });
                return this;
            }
        });
    }
);