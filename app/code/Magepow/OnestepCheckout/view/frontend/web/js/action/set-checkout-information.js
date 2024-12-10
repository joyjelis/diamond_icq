/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define(
    [
        'Magento_Checkout/js/model/shipping-save-processor',
        'Magepow_OnestepCheckout/js/model/checkout'
    ],
    function (shippingSaveProcessor, Processor) {
        'use strict';

        shippingSaveProcessor.registerProcessor('onestepcheckout', Processor);

        return function () {
            return shippingSaveProcessor.saveShippingInformation('onestepcheckout');
        }
    }
);
