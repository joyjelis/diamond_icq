/* eslint-disable */
require([
    "jquery",
    "jquery/ui",
    "slick",
    "accordion",
    "mage/translate",
], function ($, $t) {
    function freezeTheScreen() {
        if ($(this).width() <= 320) {
            $("body").css({ zoom: $(this).width() / 320 });
        }
    }
    jQuery(document).ready(function () {
        freezeTheScreen();
        $(window).resize(function () {
            freezeTheScreen();
        });
        var flag = (checkoutpage = flag2 = 0);
        setInterval(function () {
            if (flag2 == 0 && $("#top-cart-btn-checkout").length) {
                flag2 = 1;
                //$('#top-cart-btn-checkout').attr('href',$('#customcheckoutUrl').val());
                $(
                    "#minicart-content-wrapper .block-content .action.viewcart"
                ).attr("href", $("#customcartUrl").val());
            }

            if (flag == 0 && $("#mini-cart .product-item-details").length) {
                flag = 1;
                var font = $("#engravingfont").val();
                var text = $("#engravingtext").val();
                $("#mini-cart .product-item-details .engfont").text(font);
                $("#mini-cart .product-item-details .engtext").text(text);
            }

            if (
                checkoutpage == 0 &&
                $(
                    ".opc-block-summary .minicart-items-wrapper .product-item-details"
                ).length
            ) {
                checkoutpage = 1;
                var font = $("#engravingfont").val();
                var text = $("#engravingtext").val();
                $(".opc-block-summary .minicart-items-wrapper .engfont").text(
                    font
                );
                $(".opc-block-summary .minicart-items-wrapper .engtext").text(
                    text
                );
            }
        }, 1000);

        if ($(window).width() < 1025) {
            jQuery("#footer-ac").accordion({
                openedState: "active",
                collapsible: true,
            });
        }

        jQuery("#ring-filter").accordion({
            openedState: "active",
        });

        // jQuery(".open").on("click", function() {
        //   jQuery(".popup-overlay, .popup-content").addClass("active");
        // });

        // //removes the "active" class to .popup and .popup-content when the "Close" button is clicked
        // jQuery(".close, .popup-overlay").on("click", function() {
        //   jQuery(".popup-overlay, .popup-content").removeClass("active");
        // });

        jQuery("#forfooterappoi, #forfooterappoimob").on("click", function () {
            jQuery("#make_an_appointment").click();
        });
    });
});

// This function is used to validate characters with ' ' space
// This will be used for first name, last name and other alphabet text box
require(["jquery"], function ($) {
    $("body").on(
        "keypress",
        "input[name=firstname],input[name=lastname],input[name=name],input[name=recipient_name],input[name=friend_name],input[name=account_name]",
        function () {
            var keyCode = event.which ? event.which : event.keyCode;
            var chr = String.fromCharCode(keyCode);
            var tes = /^[\p{L} ]*$/gu.test(chr);
            return tes;
        }
    );
});

// This function is used to validate numbers with '.' sign
// This will be used for telephone number text box
require(["jquery"], function ($) {
    $("body").on(
        "keypress",
        "input[name=telephone],input[name=phone],input[name=account_no],input[name=mobile]",
        function () {
            var keyCode = event.which ? event.which : event.keyCode;
            var chr = String.fromCharCode(keyCode);
            return /^[\p{N}]*$/gu.test(chr);
        }
    );
});

// Search form validation
function validateMiniForm() {
    var x = document.forms["searchform"]["q"].value;
    if (x != "" && x.length < 3) {
        jQuery(".searcherror").css("display", "block");
        return false;
    }
}
/* eslint-enable */
