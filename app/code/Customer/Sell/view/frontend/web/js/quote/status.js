define([
    "jquery",
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    "domReady!"
], function($, confirmation, $t) {
    "use strict";
    return function(config) {
        domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');
        jQuery(document).ready(function() {
            function parse_query_string(query) {
                var vars = query.split("&");
                var query_string = {};
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    var key = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1]);
                    if (typeof query_string[key] === "undefined") {
                        query_string[key] = decodeURIComponent(value);
                    } else if (typeof query_string[key] === "string") {
                        var arr = [query_string[key], decodeURIComponent(value)];
                        query_string[key] = arr;
                    } else {
                        query_string[key].push(decodeURIComponent(value));
                    }
                }

                if (query_string.hasOwnProperty('changebooking') && query_string.changebooking == "1") {
                    var existCondition = setInterval(function() {
                        if ($('#booking_appointment_sell').length) {
                            openbookingform();
                            clearInterval(existCondition);
                        }
                    }, 100);
                }

                return query_string;
            }

            function IsBookingTaga(query) {
                var vars = query.split("&");
                var query_string = {};
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    var key = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1]);
                    if (typeof query_string[key] === "undefined") {
                        query_string[key] = decodeURIComponent(value);
                    } else if (typeof query_string[key] === "string") {
                        var arr = [query_string[key], decodeURIComponent(value)];
                        query_string[key] = arr;
                    } else {
                        query_string[key].push(decodeURIComponent(value));
                    }
                }

                if (query_string.hasOwnProperty('changebooking') && query_string.changebooking == "1") {
                    return true;
                }

                return false;
            }


            function buildUrl(url, parameters) {
                var qs = "";
                for (var key in parameters) {
                    var value = parameters[key];
                    qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
                }
                if (qs.length > 0) {
                    qs = qs.substring(0, qs.length - 1); //chop off last "&"
                    url = url + "?" + qs;
                }
                return url;
            }

            var query = window.location.search.substring(1);
            var qs = parse_query_string(query);

            function openbookingform() {
                var calendarLayer = document.querySelector("#booking_appointment_sell");
                var node = document.createElement("iframe");
                var xButton = document.createElement("span");
                var overlay = document.createElement("div");
                xButton.classList.add("x-btn");
                overlay.classList.add("overlay");

                var url = window.customersellconfig.koalendar.private_url;

                if (url) {
                    var fname = window.customersellconfig.customer.fname;
                    var lname = window.customersellconfig.customer.lname;
                    var email = window.customersellconfig.customer.email;
                    var fullname = "";
                    if (fname != null && lname != null) {
                        fullname = fname.concat(" ", lname);
                    }

                    if (email == null) {
                        email = "";
                    }

                    var parameters = {
                        'name': fullname,
                        'email': email,
                        "3riFs": config.quote
                    };

                    node.setAttribute("src", buildUrl(url, parameters));
                    calendarLayer.classList.add("opened-layer");
                    calendarLayer.appendChild(node);
                    calendarLayer.appendChild(xButton);
                    calendarLayer.appendChild(overlay);
                }

                document.querySelector(".x-btn").addEventListener("click", () => {
                    var query = window.location.search.substring(1);
                    calendarLayer.classList.remove("opened-layer");
                    calendarLayer.innerHTML = " ";
                    if (IsBookingTaga(query)) {
                        var url = window.location.href;

                        if (url.indexOf("?") > -1) {
                            url = url.substr(0, url.indexOf("?"));
                        }

                        window.location.href = url;

                    } else {
                        window.location.reload();
                    }

                })

                document.querySelector(".overlay").addEventListener("click", () => {
                    calendarLayer.classList.remove("opened-layer");
                    calendarLayer.innerHTML = " ";
                })

                return false;
            }

            domObserver.get('.inlinebuttons .reschedule', function(elem) {
                $(elem).click(function(e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    var reschedule = window.open(href, '_blank');
                    var rescheduletimer = setInterval(function() {
                        if (reschedule.closed) {
                            clearInterval(rescheduletimer);
                            window.location.reload();
                        }
                    }, 1000);
                    return true;
                });
            });

            domObserver.get('.inlinebuttons .reschedule', function(elem) {
                $(elem).click(function(e) {
                    var href = $(this).attr('href');
                    e.preventDefault();
                    confirmation({
                        title: $.mage.__('Cancel Appointment?'),
                        content: $.mage.__('Are you sure you want to cancel the appointment?'),
                        buttons: [{
                            text: $.mage.__('No'),
                            class: 'action-secondary action-dismiss',
                            click: function(event) {
                                this.closeModal(event);
                                return false;
                            }
                        }, {
                            text: $.mage.__('Yes'),
                            class: 'action-primary action-accept',
                            click: function(event) {
                                this.closeModal(event, true);
                                var cancel = window.open(href, '_blank');
                                var canceltimer = setInterval(function() {
                                    if (cancel.closed) {
                                        clearInterval(canceltimer);
                                        window.location.reload();
                                    }
                                }, 1000);

                                return true;
                            }
                        }]
                    });
                });
            });

            domObserver.get('#trade_type_selection', function(elem) {
                $(elem).click(function(e) {
                    var tradeType = $('input[type=radio][name=trade_type]:checked').val();
                    if (tradeType == 1) {
                        $('.in-person').addClass('hidden');
                        $('.online').removeClass('hidden');
                        $('.action.submit.primary').removeClass('hidden');
                    } else if (tradeType == 2) {
                        openbookingform();
                    }

                    $(this).hide();
                });
            });

            domObserver.get('input[type=radio][name=trade_type]', function(elem) {
                $(elem).change(function(e) {
                    var tradeType = $('input[type=radio][name=trade_type]:checked').val();
                    if (tradeType == 2) {
                        $('.online').addClass('hidden');
                        $('#trade_type_selection span').html($t('Book an Appointment'));
                    } else {
                        $('#trade_type_selection span').html($t('Submit'));
                    }

                    $('#trade_type_selection').show();
                });
            });

            domObserver.get('input[type=radio][name=accept_offer]', function(elem) {
                $(elem).change(function(e) {
                    if (this.value == 1) {
                        var tradeType = $('input[type=radio][name=trade_type]:checked').val();
                        if (tradeType == 1) {
                            $('.in-person').addClass('hidden');
                            $('.online').removeClass('hidden');
                            $('.action.submit.primary').removeClass('hidden');
                        } else if (tradeType == 2) {
                            $('.in-person').removeClass('hidden');
                            $('.online').addClass('hidden');
                            $('.action.submit.primary').addClass('hidden');
                        }

                        $('.tradefieldset').removeClass('hidden');

                    } else if (this.value == 1) {
                        $('.in-person').addClass('hidden');
                        $('.tradefieldset').addClass('hidden');
                        $('.online').addClass('hidden');
                    }
                });
            });

            // domObserver.get('input[type=radio][name=trade_type]', function(elem){
            //     $(elem).change(function(e){
            //         var tradeType = $('input[type=radio][name=trade_type]:checked').val();
            //         if (tradeType == 1) {
            //             $('.in-person').addClass('hidden');
            //             $('.online').removeClass('hidden');
            //             $('.action.submit.primary').removeClass('hidden');
            //         } else {
            //             $('.in-person').removeClass('hidden');
            //             $('.online').addClass('hidden');
            //             $('.action.submit.primary').addClass('hidden');
            //         }
            //     });
            // });

            domObserver.get('input[type=radio][name=offer_consignemnt]', function(elem) {
                $(elem).change(function(e) {
                    if (this.value == 1) {
                        var tradeType = $('input[type=radio][name=trade_type]:checked').val();
                        if (tradeType == 1) {
                            $('.in-person').addClass('hidden');
                            $('.online').removeClass('hidden');
                        } else if (tradeType == 2) {
                            $('.in-person').removeClass('hidden');
                            $('.online').addClass('hidden');
                        }
                        $('.fieldtrade').removeClass('hidden');
                        $('.fieldtrade').parent('fieldset').removeClass('hidden');

                    } else if (this.value == 2) {
                        $('.in-person').addClass('hidden');
                        $('.fieldtrade').addClass('hidden');
                        $('.online').addClass('hidden');
                    }
                });
            });
        });


    }
});