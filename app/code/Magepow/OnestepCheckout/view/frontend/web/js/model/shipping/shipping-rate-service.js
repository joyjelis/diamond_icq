/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address'
    ],
    function (quote, defaultProcessor, customerAddressProcessor) {
        'use strict';

        var processors = [];

        processors.default = defaultProcessor;
        processors['customer-address'] = customerAddressProcessor;

        return {
            isAddressChange: false,
            registerProcessor: function (type, processor) {
                processors[type] = processor;
            },
            estimateShippingMethod: function () {
                var type = quote.shippingAddress().getType();

                if (processors[type]) {
                    processors[type].getRates(quote.shippingAddress());
                } else {
                    processors.default.getRates(quote.shippingAddress());
                }
            }
        }
    }
);
