define([
    "jquery",
    "Magento_Ui/js/modal/modal",
    'mage/translate',
    'mage/validation',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    "domReady!"
], function($, modal, $t) {
    "use strict";
    return function(config) {
        domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');
        jQuery(document).ready(function() {

            var options = {
                type: 'popup',
                responsive: true,
                modalClass: 'trmethod-popup',
                title: $t('Add Bank Details'),
                buttons: [{
                    text: $t('Add Bank Detail'),
                    class: '',
                    click: function() {
                        if (updatemethods()) {
                            this.closeModal();
                        }
                    }
                }]
            };

            function updatemethods() {
                if ($("#transfer-methods-form").validation() && $("#transfer-methods-form").validation('isValid')) {
                    $.ajax({
                        showLoader: true,
                        url: config.save_url,
                        data: $("#transfer-methods-form").serialize(),
                        cache: false,
                        type: "POST",
                        dataType: 'json'
                    }).done(function(data) {
                        if (data.success) {
                            location.reload();
                        }
                    });

                    return true;
                }

                return false;
            }

            function updatefields(data) {
                reset();
                data.action = (data.action) ? data.action : '';
                for (var k in data) {
                    var input = $('#transfer-methods-form [name="' + k + '"]').val(data[k]);
                    if (input.attr('type') !== 'hidden') {
                        input.attr('disabled', (data.action == 'delete'));
                    }
                }
                $('#transfer-methods-form input').focusin().focusout();
            }

            function reset() {
                $("#transfer-methods-form").trigger('reset');
            }

            domObserver.get('#transfer-methods .deletemethod', function(elem) {
                $(elem).click(function(e) {
                    options.title = $t("Delete Bank Details");
                    options.buttons[0].text = $t("Delete Bank Details");
                    modal(options, $('#transfer-methods-modal'));
                    e.preventDefault();
                    var data = JSON.parse(atob($(this).data('load')));
                    data.action = 'delete';
                    updatefields(data);
                    $('#transfer-methods-modal').modal('openModal');
                    return true;
                });
            });

            domObserver.get('#transfer-methods .updatemethod', function(elem) {
                $(elem).click(function(e) {
                    options.title = $t("Update Bank Details");
                    options.buttons[0].text = $t("Update Bank Details");
                    modal(options, $('#transfer-methods-modal'));
                    e.preventDefault();
                    var data = JSON.parse(atob($(this).data('load')));
                    updatefields(data);
                    $('#transfer-methods-modal').modal('openModal');
                    return true;
                });
            });

            domObserver.get('.addmethod', function(elem) {
                $(elem).click(function(e) {
                    e.preventDefault();
                    options.title = $t("Add Bank Details");
                    options.buttons[0].text = $t("Add Bank Details");
                    modal(options, $('#transfer-methods-modal'));
                    var data = JSON.parse(atob($(this).data('load')));
                    updatefields(data);
                    $('#transfer-methods-modal').modal('openModal');
                    return false;
                });
            });
        });
    }
});