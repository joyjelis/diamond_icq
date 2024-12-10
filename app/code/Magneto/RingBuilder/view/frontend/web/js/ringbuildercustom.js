require(['jquery', 'Magento_Ui/js/lib/view/utils/dom-observer'], function($) {

    domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');

    $(document).ready(function() {
        domObserver.get('#gift_deadline', function(elem) {
            $(elem).change(function() {
                if ($(this).val() == "") {
                    $('label[for="' + $(this).attr('id') + '"]').removeClass('hasvalue');
                } else {
                    $('label[for="' + $(this).attr('id') + '"]').addClass('hasvalue');
                }
            });
        });

        domObserver.get('#metalcustomoption .option', function(elem) {
            $(elem).click(function() {
                $('#metalcustomoption .option.active').removeClass('active');
                var option = $(this).data('value');
                var name = $(this).data('name');
                $(this).addClass('active');
                $('#metalcustomoptionactive').text(name);
                $('#metal_type').val(option).trigger('change');

            });

            $('#metalcustomoptionactive').text(jQuery("#metal_type option:selected").text());
            $('#metalcustomoption .option[data-value="' + $('#metal_type').val() + '"]').addClass('active');
        });

        domObserver.get('#stonecustomoption .option', function(elem) {
            $(elem).click(function() {
                $('#stonecustomoption .option.active').removeClass('active');
                var option = $(this).data('value');
                var name = $(this).data('name');
                $(this).addClass('active');
                $('#stonecustomoptionactive').text(name);
                $('#stonequality').val(option).trigger('change');

            });

            $('#stonecustomoptionactive').text(jQuery("#stonequality option:selected").text());
            $('#stonecustomoption .option[data-value="' + $('#stonequality').val() + '"]').addClass('active');
        });

        domObserver.get('#centerstonecustomoption .option', function(elem) {
            $(elem).click(function() {
                $('#centerstonecustomoption .option.active').removeClass('active');
                var option = $(this).data('value');
                var name = $(this).data('name');
                $(this).addClass('active');
                $('#centerstonecustomoptionactive').text(name);
                $('#centerstonesize').val(option).trigger('change');

            });

            $('#centerstonecustomoptionactive').text(jQuery("#centerstonesize option:selected").text());
            $('#centerstonecustomoption .option[data-value="' + $('#centerstonesize').val() + '"]').addClass('active');
        });

        domObserver.get('#ringsizecustomoption .option', function(elem) {
            $(elem).click(function() {
                $('#ringsizecustomoption .option.active').removeClass('active');
                var option = $(this).data('value');
                var name = $(this).data('name');
                $(this).addClass('active');
                $('#ringsizecustomoptionactive').text(name);
                $('#ring_size').val(option).trigger('change');

            });

            $('#ringsizecustomoptionactive').text(jQuery("#ring_size option:selected").text());
            $('#ringsizecustomoption .option[data-value="' + $('#ring_size').val() + '"]').addClass('active');
        });

        domObserver.get('#ringbuilderfilter', function(elem) {
            $(elem).click(function() {
                $('#mobilefiltercontent').addClass('active');
            });
        });

        domObserver.get('.videoicon', function(elem) {
            $(elem).click(function() {
                $('.fotorama__stage').hide();
                $('#ringvideo').show();
            });
        });

        domObserver.get('#zoom_me', function(elem) {
            $(elem).click(function() {
                $('.fotorama__stage').show();
                $('#ringvideo').hide();
            });
        });

        domObserver.get('.fotorama__thumb-border', function(elem) {
            $(elem).click(function() {
                $('.fotorama__stage').show();
                $('#ringvideo').hide();
            });
        });

        domObserver.get('#mobilefiltercontent #closefilter', function(elem) {
            $(elem).click(function() {
                $('#mobilefiltercontent').removeClass('active');
            });
        });

        // Mobile Filters
        domObserver.get('#mobilefiltercontent .mobile_filter_ring_style', function(elem) {
            $(elem).click(function() {
                $('#mobilefiltercontent #ringfilter .active').removeClass('active');
                var selectedfilter = $(this).data('value');
                var OriginalFilter = jQuery('input[type="radio"][name="ring_collection"][value="' + selectedfilter + '"]');
                $('input[type=radio][name="ring_collection"]').removeClass('is-checked');
                if ($(OriginalFilter).hasClass('is-checked')) {
                    $(OriginalFilter).prop('checked', false);
                } else {
                    $(OriginalFilter).prop('checked', true);
                    $(OriginalFilter).addClass('is-checked');
                }

                $(elem).parent().addClass('active');
            });
        });

        domObserver.get('#mobilefiltercontent .mobile_filter_shape_filter', function(elem) {
            $(elem).click(function() {
                var selectedfilter = $(this).data('value');
                var OriginalFilter = jQuery('input[type="checkbox"][name="ring_shape"][value="' + selectedfilter + '"]');
                $('input[type=checkbox][name="ring_shape"]:checkbox').attr("checked", false);
                $('#mobilefiltercontent #shapefilter .active').removeClass('active');

                if ($(OriginalFilter).is(":checked")) {
                    $(OriginalFilter).attr('checked', false)
                    $(elem).parent().removeClass('active');
                } else {
                    $(OriginalFilter).attr('checked', true)
                    $(OriginalFilter).addClass('is-checked');
                    $(elem).parent().addClass('active');
                }
            });
        });

        domObserver.get('#mobilefiltercontent .mobile_filter_metal_type', function(elem) {
            $(elem).click(function() {
                $('#mobilefiltercontent #metalfilter .active').removeClass('active');
                var selectedfilter = $(this).data('value');
                var OriginalFilter = jQuery('input[type="radio"][name="ring_metal"][value="' + selectedfilter + '"]');
                $('input[type=radio][name="ring_metal"]').removeClass('is-checked');
                if ($(OriginalFilter).hasClass('is-checked')) {} else {
                    $(OriginalFilter).addClass('is-checked');
                }

                $(elem).parent().addClass('active');
            });
        });

        domObserver.get('#mobilefiltercontent #ringstep1_price_min', function(elem) {
            $(elem).keyup(function() {
                $('#rb_min_price').val($('#ringstep1_price_min').val());
            });
        });

        domObserver.get('#mobilefiltercontent #ringstep1_price_max', function(elem) {
            $(elem).keyup(function() {
                $('#rb_max_price').val($('#ringstep1_price_max').val());
            });
        });

        domObserver.get('#mobilefiltercontent #mringbuilder_step1_clear_call', function(elem) {
            $(elem).click(function() {
                $('#ringfilter .collection-type.active').removeClass('active');
                $('#shapefilter .collection-type.active').removeClass('active');
                $('#metalfilter .one.active').removeClass('active');
                $('#ringstep1_price_min').val($('#ringstep1_price_min').data('original'));
                $('#ringstep1_price_max').val($('#ringstep1_price_max').data('original'));

                $('input[type=checkbox][name="ring_shape"]:checkbox').attr("checked", false);
                $('input[type=radio][name="ring_collection"]').removeClass('is-checked');
                $('input[type=radio][name="ring_collection"]').prop('checked', false);
                $('input[type=radio][name="ring_metal"]').removeClass('is-checked');
                $('input[type=radio][name="ring_metal"]').prop('checked', false);
                $('#rb_min_price').val($('#ringstep1_price_min').data('original'));
                $('#rb_max_price').val($('#ringstep1_price_max').data('original'));
                $('#mobilefiltercontent #closefilter').click();
                $("#search-rings-form #submit").trigger("click");
            });
        });

        domObserver.get('#mobilefiltercontent #mringbuilder_step1_apply_call', function(elem) {
            $(elem).click(function() {
                $('input[type="radio"][name="ring_metal"].is-checked').prop('checked', true);
                $('#mobilefiltercontent #closefilter').click();
                $("#search-rings-form #submit").trigger("click");
            });
        });
    });
});