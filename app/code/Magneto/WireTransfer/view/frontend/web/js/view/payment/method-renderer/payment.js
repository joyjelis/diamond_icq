define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    ], function (Component, $) {
     'use strict';
     return Component.extend({
         defaults: {
            template: 'Magneto_WireTransfer/payment/payment'
        },

        getCode: function() {
            return 'wire_transfer';
        },

        getTitle: function(){
            var title = this._super();
            return title;
        },

        getInstructions: function(){
            return window.checkoutConfig.payment.instructions[this.item.method];
        }
    });
 });