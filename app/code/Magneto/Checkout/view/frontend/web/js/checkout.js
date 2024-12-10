require([
    'jquery',
    'mageUtils',
    'mage/translate',
    'uiComponent',
    'Magento_Ui/js/lib/registry/registry',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'intlTelInput',
], function($, utils, $t) {
    domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');
    $(document).ready(function() {
        domObserver.get('.opc-progress-bar', function(elem) {
            var progressbar = $(elem).detach();
            $("#maincontent").prepend(progressbar);
        });

        //  domObserver.get('#customer-email-fieldset label', function(elem){
        //    $('#customer-email-fieldset label').each(function( index ) {
        //       var isexits = '#' + $( this ).attr("for");
        //       var text = $( this ).text();
        //       domObserver.get(isexits, function(elem){
        //          $(elem).attr('placeholder', $.trim(text));
        //       });
        //    });
        // });

        domObserver.get('#opc-shipping_method', function(elem) {
            var checkExist = setInterval(function() {
                if (!$(elem).hasClass('_block-content-loading')) {
                    if (jQuery('#checkout-step-shipping_method .no-quotes-block').length) {
                        jQuery('#checkout-step-shipping_method .no-quotes-block').show();
                    }
                    clearInterval(checkExist);
                }
            }, 100); // check every 100ms  
        });
    });
});