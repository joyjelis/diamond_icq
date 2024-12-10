require(['jquery', 'mageUtils', 'mage/translate', 'uiComponent', 'Magento_Ui/js/lib/registry/registry', 'Magento_Ui/js/lib/view/utils/dom-observer', 'intlTelInput', 'mage/validation'], function($, utils, $t) {
    domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');

    $(document).ready(function() {
        function InitTel(elem) {
            $(elem).addClass("g-validate-init-number");
            if ($(elem).hasClass("required")) {
                $(elem).attr('data-validate', "{'g-validate-init-number': true, required:true }");
            } else {
                $(elem).attr('data-validate', "{'g-validate-init-number': true }");
            }
            var code = elem;
            var config = {
                nationalMode: false,
                autoPlaceholder: 'off',
                utilsScript: window.globalTel.utilsScript,
                initialCountry: 'hk'
            };

            var iti = window.intlTelInput(code, config);
            $(elem).on("blur keyup keydown change", function(e) {
                var iti = window.intlTelInputGlobals.getInstance(this);
                var getCode = iti.getSelectedCountryData().dialCode;
                if (typeof getCode !== 'undefined') {
                    var stdcode = parseInt(getCode.length) + 1;

                    if ($(this).val().length >= stdcode) {
                        return true;
                    }

                    if ($(this).val().length <= stdcode) {
                        //$(this).val("");
                        //$(this).val('+' + getCode);
                        return true;
                    }

                    $(this).focusin().focusout();
                }
            });
        }

        domObserver.get('.floatingLabelObserver', function(elem) {
            setInterval(function() {
                if ($(elem).val() != "") {
                    if (!$(elem).hasClass('not-empty')) {
                        $(elem).addClass('not-empty');
                        $(elem).focusin().focusout();
                    }
                } else {
                    if ($(elem).hasClass('not-empty')) {
                        $(elem).removeClass('not-empty');
                        $(elem).focusin().focusout();
                    }
                }
            }, 500);
        });

        if (window.globalTel.enable == 1) {
            domObserver.get('input[name="telephone"], input[name="phone_number"]', function(elem) {
                InitTel(elem);
                setInterval(function() {
                    if ($(elem).val() != "") {
                        if (!$(elem).hasClass('not-empty')) {
                            $(elem).addClass('not-empty');
                            $(elem).focusin().focusout();
                        }
                    }
                }, 500);
            });

            domObserver.get('.iti.iti--allow-dropdown', function(elem) {
                var placeholder = $(elem).find('input').attr('placeholder');
                if (placeholder != "" && typeof placeholder !== 'undefined') {
                    $(elem).addClass('placeholder1');
                    $(elem).find('input').after('<label class="label floating-label1 label">' + placeholder + '</label>');
                    $(elem).find('input').attr('placeholder', '');
                    $(elem).find('input').before('<div class="field placeholder1"></div>');
                    var input = $(elem).find('input');
                    var label = $(elem).find('.label.floating-label1.label');
                    $(elem).find('.field.placeholder1').append(input.detach());
                    $(elem).find('.field.placeholder1').append(label.detach());
                    $(elem).find('input').floatingLabel();
                }
            });

            domObserver.get(window.globalTel.telselector, function(elem) {
                InitTel(elem);
                setInterval(function() {
                    if ($(elem).val() != "") {
                        if (!$(elem).hasClass('not-empty')) {
                            $(elem).addClass('not-empty');
                            $(elem).focusin().focusout();
                        }
                    }
                }, 500);
            });
        }
    });
});