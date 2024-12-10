define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'mage/validation'
    ], function($, ko, stepNavigator, $t) {
        'use strict';

        var mixin = {

            validateShippingInformation: function () {
                var error = this._super();

                if(jQuery('#co-shipping-form').validation() && jQuery('#co-shipping-form').validation('isValid')){
                    if(error == true){
                        return true;
                    }
                }

                return false;
            },

            initialize: function() {
                this._super();
                $.each(stepNavigator.steps(), function(index, step) {
                if (step.code === 'shipping') {
                    step.title = $t('Shipping Details');
                }

                if (step.code === 'payment') {
                    step.title = $t('Review and Payment');
                }
            });
            }
        };

        return function(target) {
            return target.extend(mixin);
        }
    });