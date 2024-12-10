define(['jquery', 'uiComponent', 'ko', 'mage/validation'],
    function($, Component, ko, modal, addressList) {
        'use strict'
        return Component.extend({
            region_html: ko.observable(0),
            country_html: ko.observable(0),
            country_url: ko.observable(0),
            bank_country_html: ko.observable(0),
            error_msg: ko.observable(0),
            save_url: ko.observable(0),
            step: ko.observable(1),
            city: ko.observable(""),
            landmark: ko.observable(""),
            postcode: ko.observable(""),
            street: ko.observable(""),
            mobile: ko.observable(""),
            email: ko.observable(""),
            last_name: ko.observable(""),
            first_name: ko.observable(""),
            swift_code: ko.observable(""),
            account_no: ko.observable(""),
            account_name: ko.observable(""),
            bank_name: ko.observable(""),
            id: ko.observable(""),

            initialize: function(config) {
                this._super();
                var self = this;
                self.country_url = config.country_url;
                self.bank_country_html = config.bank_country_html;
                self.error_msg = config.error_msg;
                self.save_url = config.save_url;
                self.country_html = config.country_html;
                self.id(config.sell_data.sell_id);
                self.first_name(window.customersellconfig.customer.fname);
                self.last_name(window.customersellconfig.customer.lname);
                self.email(config.sell_data.email);
                self.mobile(config.sell_data.mobile);
                self.bank_name(config.sell_data.bank_name);
                self.account_name(config.sell_data.account_name);
                self.account_no(config.sell_data.account_no);
                self.swift_code(config.sell_data.swift_code);
                self.street(config.sell_data.street);
                self.landmark(config.sell_data.landmark);
                self.postcode(config.sell_data.postcode);
                self.city(config.sell_data.city);
            },
            next: function() {
                if (this.validateForm('#sell-form')) {
                    this.step(2);
                }
            },

            validateForm: function(form) {
                return $(form).validation() && $(form).validation('isValid');
            },

            previous: function() {
                this.step(1);
            },

            submit: function() {
                var self = this;
                if (this.validateForm('#sell-form')) {
                    var bank_details = {
                        id: self.id(),
                        city: self.city(),
                        landmark: self.landmark(),
                        postcode: self.postcode(),
                        street: self.street(),
                        mobile: self.mobile(),
                        email: self.email(),
                        last_name: self.last_name(),
                        first_name: self.first_name(),
                        swift_code: self.swift_code(),
                        account_no: self.account_no(),
                        account_name: self.account_name(),
                        bank_name: self.bank_name(),
                        trade_type: 1,
                        pickup_country: $('#country').val(),
                        country: $('#country_bank').val(),
                        ajax: 1
                    };

                    $.ajax({
                        showLoader: true,
                        url: self.save_url_quote,
                        data: bank_details,
                        type: "POST",
                        dataType: 'json'
                    }).done(function(data) {
                        location.reload();
                        $('#loader').addClass('hidden');
                    });
                }
            },

            country: function() {
                var self = this;
                $(document).on('change', '#country', function() {
                    var param = 'country=' + $('#country').val();
                    $('#loader').removeClass('hidden');
                    $.ajax({
                        showLoader: true,
                        url: self.country_url,
                        data: param,
                        type: "GET",
                        dataType: 'json'
                    }).done(function(data) {
                        $('#state').empty();
                        if (data.value == '') {
                            $('#region').show();
                            $('#region').val("");
                            $('#region_id_div').hide();
                        } else {
                            $('#state').append(data.value);
                            $('#region').hide();
                            $('#region_id_div').show();
                        }

                        $('#loader').addClass('hidden');
                    });
                });
            }
        });
    })