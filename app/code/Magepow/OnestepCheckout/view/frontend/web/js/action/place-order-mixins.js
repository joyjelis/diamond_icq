/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magepow_OnestepCheckout/js/action/set-checkout-information',
], function ($, wrapper, setCheckoutInformationAction) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var deferred = $.Deferred();
            if (paymentData && paymentData.method === 'braintree_paypal') {
                setCheckoutInformationAction().done(function () {
                    originalAction(paymentData, messageContainer).done(function (response) {
                        deferred.resolve(response);
                    }).fail(function (response) {
                        deferred.reject(response);
                    })
                }).fail(function (response) {
                    deferred.reject(response);
                })
            } else {
                return originalAction(paymentData, messageContainer).fail(function (response) {
                    if ($('.message-error').length) {
                        $('html, body').scrollTop(
                            $('.message-error:visible:first').closest('div').offset().top - $(window).height() / 2
                        );
                    }
                });
            }

            return deferred;
        });
    };
});
