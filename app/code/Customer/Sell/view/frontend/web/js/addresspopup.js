define(['jquery', 'uiComponent', 'ko', 'Magento_Ui/js/modal/modal', 'Magento_Customer/js/model/address-list', 'mage/validation'],
    function($, Component, ko, modal, addressList) {
        'use strict'
        return Component.extend({
            region_html: ko.observable(0),
            country_html: ko.observable(0),
            country_url: ko.observable(0),
            error_msg: ko.observable(0),
            save_url: ko.observable(0),
            id: ko.observable(""),

            initialize: function(config) {
                this._super();
                var self = this;
                self.country_url = config.country_url;
                self.error_msg = config.error_msg;
                self.save_url = config.save_url;
                self.region_html = atob(config.region_html);
                self.id(config.sell_data.sell_id);
                self.country_html = atob(config.country_html);
            },

            validateForm: function(form) {
                return $(form).validation() && $(form).validation('isValid');
            },

            addAddress: function() {
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: 'Add Shipping Address',
                    modalClass: 'add-address',
                    buttons: []
                };
                modal(options, $('#addaddress-content'));
                $("#addaddress-content").modal("openModal");
            },

            saveaddress: function() {
                var self = this;
                if (this.validateForm('#sell-address-popup')) {
                    var param = {
                        id: self.id(),
                        city: $('#sell-address-popup #city').val(),
                        landmark: $('#sell-address-popup #state_text').val(),
                        postcode: $('#sell-address-popup #postcode').val(),
                        street: $('#sell-address-popup #street_address_1').val(),
                        pickup_country: $('#country').val(),
                        return_shpping: 1,
                        ajax: 1
                    };

                    $.ajax({
                        showLoader: true,
                        url: self.save_url,
                        data: param,
                        type: "POST",
                        dataType: 'json'
                    }).done(function(data) {
                        $("#addaddress-content").modal("closeModal");
                        if (data.success == true) {
                            $('#returnshippingaddress').empty();
                            $('#return-shipping').val(data.address_html);
                            $('#returnshippingaddress').html(data.address_html);
                            $('#return-shipping').attr('value', 1);
                            $('#return-shipping').attr('checked', true).trigger('change');
                        }
                    });
                }
            }
        });
    })