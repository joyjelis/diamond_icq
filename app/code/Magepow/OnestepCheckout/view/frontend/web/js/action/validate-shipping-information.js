define([
    'Magepow_OnestepCheckout/js/model/shipping-save-processor/validate'
], function (validateProcessor) {
    'use strict';

    return function () {
        return validateProcessor.saveShippingInformation();
    };
});