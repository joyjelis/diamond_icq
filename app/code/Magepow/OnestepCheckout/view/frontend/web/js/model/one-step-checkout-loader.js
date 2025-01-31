/**
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

define(
    [
        'jquery',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/totals',
        'Magepow_OnestepCheckout/js/model/payment-service'
    ],
    function ($, shippingService, totalService, paymentService) {
        'use strict';

        var blockLoader = {
            shipping: {
                queue: 0,
                service: shippingService
            },
            payment: {
                queue: 0,
                service: paymentService
            },
            total: {
                queue: 0,
                service: totalService
            }
        };

        return {
            getServices: function (blocks) {
                var services = {
                    payment: blockLoader.payment.service,
                    total: blockLoader.total.service
                };

                if (typeof blocks !== 'undefined') {
                    services = {};
                    $.each(blocks, function (index, block) {
                        if (blockLoader.hasOwnProperty(block)) {
                            services[block] = blockLoader[block].service;
                        }
                    });
                }

                return services;
            },

            /**
             * Start full page loader action
             */
            startLoader: function (blocks) {
                var services = this.getServices(blocks);
                $.each(services, function (index, service) {
                    blockLoader[index].queue += 1;
                    service.isLoading(true);
                });
            },

            /**
             * Stop full page loader action
             */
            stopLoader: function (blocks) {
                var services = this.getServices(blocks);
                $.each(services, function (index, service) {
                    blockLoader[index].queue -= 1;
                    if (blockLoader[index].queue == 0) {
                        service.isLoading(false);
                    }
                });
            }
        };
    }
);
