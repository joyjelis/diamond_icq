/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

define(
    [
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magepow_OnestepCheckout/js/model/resource-url-manager',
        'mage/storage',
        'Magepow_OnestepCheckout/js/model/one-step-checkout-data',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (ko,
              $,
              _,
              quote,
              resourceUrlManager,
              storage,
              OneStepCheckoutData,
              paymentService,
              methodConverter,
              errorProcessor,
              fullScreenLoader,
              selectBillingAddressAction) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload,
                    addressInformation = {},
                    additionInformation = OneStepCheckoutData.getData();
                if (window.checkoutConfig.mageConfig.giftMessageOptions.isOrderLevelGiftOptionsEnabled) {
                    additionInformation.giftMessage = this.saveGiftMessage();
                }
                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }

                if (!quote.isVirtual()) {
                    addressInformation = {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    };
                } else if ($.isEmptyObject(additionInformation)) {
                    return $.Deferred().resolve();
                }

                var customAttributes = {};
                if (_.isObject(quote.billingAddress().customAttributes)) {
                    _.each(quote.billingAddress().customAttributes, function (attribute, key) {
                        if (_.isObject(attribute)) {
                            customAttributes[attribute.attribute_code] = attribute.value
                        } else if (_.isString(attribute)) {
                            customAttributes[key] = attribute
                        }
                    });
                }

                payload = {
                    addressInformation: addressInformation,
                    customerAttributes: customAttributes,
                    additionInformation: additionInformation
                };

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetCheckoutInformation(quote),
                    JSON.stringify(payload)
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        fullScreenLoader.stopLoader();
                    }
                );
            },
            saveGiftMessage: function () {
                var giftMessage = {};
                if (!$("#onestepcheckout-gift-message").is(":checked")) $('.gift-options-content').find('input:text,textarea').val('');
                giftMessage.sender = $("#gift-message-whole-from").val();
                giftMessage.recipient = $("#gift-message-whole-to").val();
                giftMessage.message = $("#gift-message-whole-message").val();
                return JSON.stringify(giftMessage);
            }
        };
    }
);
