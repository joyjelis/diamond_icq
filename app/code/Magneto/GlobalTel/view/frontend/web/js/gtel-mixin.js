define([
    'jquery',
    'jquery/ui',
    'intlTelInput',
    'jquery/validate',
    'mage/translate'
    ], function ($) {
        return function () {
            $.validator.addMethod(
                'g-validate-init-number',
                function (v, elm) {
                    if (!v) {
                        return true;
                    }

                    with (elm) {
                        var ext = value;
                        if (ext.indexOf('+') !== 0) {
                            var iti = window.intlTelInputGlobals.getInstance(elm);
                            var getCode = iti.getSelectedCountryData().dialCode;
                            if (typeof getCode !== 'undefined') {
                                ext = ('+' + getCode).concat(ext.trim().replace(/^[0]+/,''));
                            }
                        }
                        if(window.intlTelInputUtils.isValidNumber(ext) == true) {
                            elm.value = ext;
                            return true;
                        }
                    }
                    return false;
                },
                $.mage.__("Incorrect Number")
                );
        };
    });