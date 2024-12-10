require([

    'jquery'

], function($) {

    $(document).ready(function() {

      ringbuildermain();

    });

});



function ringbuildermain() {

    require([
        'jquery',
        'nouislider',
        'wnumb',
        'jquery/ui'
    ], function($,noUiSlider,wNumb) {

        var $searchModule = $('#search-rings');

        $.ajax({

            url: $('#search-rings-form #baseurl').val() + 'ringbuilder/settings/loadringfilter/',

            data: $('#search-rings-form').serialize(),

            type: 'POST',

            dataType: 'json',

            cache: true,

            beforeSend: function(settings) {

            },

            success: function(response) {

                $('#filter-main-div').html(response.output);
                $('.top-section').css('display', 'block');
                $('.placeholder-content-top').css('display', 'none');
                $("#search-rings-form #submit").trigger("click");
                $('.loading-mask.gemfind-loading-mask').css('display', 'none');
                $('input:checkbox').change(function() {

                    if ($(this).is(':checked')) {

                        $('#shapeul li > div').removeClass('selected active');

                        $("input:checkbox").attr("checked", false);

                        $(this).attr("checked", true);
                        
                        $(this).parent().addClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    } else {

                        $(this).parent().removeClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    }

                });

                $('input[type=radio][name=ring_collection]').on('click', function() {

                    $self = $(this);

                    if ($self.hasClass('is-checked')) {

                        $self.prop('checked', false).removeClass('is-checked');

                        $(this).parent().removeClass('selected active');

                        $('#collections-section ul li').removeClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    } else {

                        $('input[type=radio][name=ring_collection]').removeClass('is-checked');

                        $('#collections-section ul li').removeClass('selected active');

                        $self.addClass('is-checked');

                        $(this).parent().addClass('selected active');

                        $(this).parent().parent().addClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    }

                });

                $('input[type=radio][name=ring_metal]').on('click', function() {

                    $self = $(this);

                    if ($self.hasClass('is-checked')) {

                        $self.prop('checked', false).removeClass('is-checked');

                        $(this).parent().removeClass('selected active');

                        $('.metaltypeli ul li').removeClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    } else {

                        $('input[type=radio][name=ring_metal]').removeClass('is-checked');

                        $('.metaltypeli ul li').removeClass('selected active');

                        $self.addClass('is-checked');

                        $(this).parent().addClass('selected active');

                        $("#search-rings-form #submit").trigger("click");

                    }

                });

                $('#collections-section input[type=radio][name=ring_collection]').on('click', function() {

                    $.ajax({

                        url: $('#search-rings-form #baseurl').val() + 'ringbuilder/settings/updatefilter/',

                        data: $('#search-rings-form').serialize(),

                        type: 'POST',

                        dataType: 'json',

                        cache: true,

                        success: function(response) {

                            if (response.hiddenshape) {

                                $(response.hiddenshape).css('opacity', 0.5);

                                $(response.hiddenshape).css('pointer-events', 'none');

                            } else {

                                $('.filter-for-shape ul li').css('opacity', 1);

                                $('.filter-for-shape ul li').css('pointer-events', 'auto');

                            }



                            if (response.hiddenmetaltype) {

                                $(response.hiddenmetaltype).css('opacity', 0.5);

                                $(response.hiddenmetaltype).css('pointer-events', 'none');

                            } else {

                                $('.metaltypeli li').css('opacity', 1);

                                $('.metaltypeli li').css('pointer-events', 'auto');

                            }

                        }

                    });

                });

                $('#shapeul input:checkbox').change(function() {

                    $.ajax({

                        url: $('#search-rings-form #baseurl').val() + 'ringbuilder/settings/updatefilter/',

                        data: $('#search-rings-form').serialize(),

                        type: 'POST',

                        dataType: 'json',

                        cache: true,

                        success: function(response) {

                            if (response.hiddencollection) {

                                $(response.hiddencollection).css('opacity', 0.5);

                                $(response.hiddencollection).css('pointer-events', 'none');

                                $(response.hiddencollection).attr("checked", false);

                            } else {

                                $('#collections-section ul li').css('opacity', 1);

                                $('#collections-section ul li').css('pointer-events', 'auto');

                            }

                            if (response.hiddenmetaltype) {

                                $(response.hiddenmetaltype).css('opacity', 0.5);

                                $(response.hiddenmetaltype).css('pointer-events', 'none');

                            } else {

                                $('.metaltypeli li').css('opacity', 1);

                                $('.metaltypeli li').css('pointer-events', 'auto');

                            }


                        }

                    });

                });
            var sliders = $("#noui_price_slider_rb")[0];
            var min_input = $(sliders).find("input[data-type='min']");
            var max_input = $(sliders).find("input[data-type='max']");
            
            var price_min_val = parseFloat($(sliders).attr('data-min'))
            var price_max_val = parseFloat($(sliders).attr('data-max'));
			
			var $price_min_val = parseFloat($(sliders).attr('data-min'))
            var $price_max_val = parseFloat($(sliders).attr('data-max'));
            
            var start_price_min = parseFloat(min_input.val());
            var start_price_max = parseFloat(max_input.val());
            
            var first_half_interval = 50;
            var last_half_interval = 2500;
            
            if($price_max_val >= 0 && $price_max_val <= 10000){
                var range = {
                    'min': [$price_min_val, first_half_interval],
                    '20%': [500, 100],
                    '50%': [5000, 250],
                    '70%': [7000, 500],
                    'max': [$price_max_val]
                }
            } else if( ( $price_max_val >= 10001 ) && ( $price_max_val <= 100000 ) ){
                var range = {
                    'min': [$price_min_val, first_half_interval],
                    '10%': [500, 100],
                    '30%': [5000, 250],
                    '50%': [10000, 500],
                    '70%': [15000, 1000],
                    '80%': [50000, 2500],                    
                    'max': [$price_max_val]
                }
            } else {                
                var range = {
                    'min': [$price_min_val, first_half_interval],
                    '10%': [500, 100],
                    '30%': [5000, 250],
                    '40%': [10000, 500],
                    '50%': [15000, 1000],
                    '60%': [50000, 2500],
                    '70%': [100000, 10000],
                    '80%': [250000, 25000],              
                    'max': [$price_max_val]
                }
            }

            var slider_object = noUiSlider.create(sliders, {
                start: [start_price_min, start_price_max],
                //tooltips: [true, wNumb({decimals: 1})],
                connect: true,
				step: 1,
                range: range,
                format: wNumb({
                    decimals: 0,
                    prefix: '',
                    thousand: ',',
                })
            });
            sliders.noUiSlider.on('update', function( values, handle ) {
                setTimeout(function() { 
                    var value_show = values[handle];
                    if ( handle ) {
                        max_input.val(value_show);
                    } else {
                        min_input.val(value_show);
                    }
                },1000);
            });
            
            sliders.noUiSlider.on('change', function( values, handle ) {
                $("#search-rings-form #submit").trigger("click");
            });
            var $price_input1 = $(sliders).find("input.slider-left");
            var $price_input2 = $(sliders).find("input.slider-right");
            var price_inputs = [$price_input1, $price_input2];
            $("#ringstep1_price_min").val($("#rb_min_price").val());
            $("#ringstep1_price_max").val($("#rb_max_price").val());
            slider_update_textbox(price_inputs,sliders);
                /*if ($('#price_slider').length){

                    new numberSlider('price', true);

                }*/
            $('.top-section').css('display', 'block');
            },
            error: function(xhr, status, errorThrown) {

                console.log('Error happens. Try again.');

                console.log(errorThrown);

            }

        });

            



//If search module container exists hook slider to DOM

if ($searchModule.length) {

   

    $searchModule.find('.ui-slider-handle:even').addClass('left-handle');

    $searchModule.find('.ui-slider-handle:odd').addClass('right-handle');

}

    });

}
function slider_update_textbox(slider_inputs,slidername){
    // Listen to keydown events on the input field.
    slider_inputs.forEach(function (input, handle) {
        input.change(function () {
            console.log('change');
            var vals = parseFloat(this.value);
            if(handle){
                slidername.noUiSlider.set([null, vals]);
            } else {
                slidername.noUiSlider.set([vals, null]);
            }
            jQuery("#search-rings-form #submit").trigger("click");
        });                
        input.keyup(function (e) {
            var values = slidername.noUiSlider.get();
            var value = parseFloat(values[handle]);
            // [[handle0_down, handle0_up], [handle1_down, handle1_up]]
            var steps = slidername.noUiSlider.steps();
            // [down, up]
            var step = steps[handle];
            var position;
            // 13 is enter,
            // 38 is key up,
            // 40 is key down.
            switch (e.which) {

                case 13:
                var vals = parseFloat(this.value);
                if(handle){
                    slidername.noUiSlider.set([null, vals]);
                } else {
                    slidername.noUiSlider.set([vals, null]);
                }                        
                jQuery("#search-rings-form #submit").trigger("click");
                break;

                case 38:
                position = step[1];
                    // false = no step is set
                    if (position === false) {
                        position = 1;
                    }
                    // null = edge of slider
                    if (position !== null) {
                        var vals = parseFloat(value + position);
                        if(handle){
                            slidername.noUiSlider.set([null, vals]);
                        } else {
                            slidername.noUiSlider.set([vals, null]);
                        }
                    }
                    jQuery("#search-rings-form #submit").trigger("click");
                    break;
                case 40:
                    position = step[0];
                    if (position === false) {
                        position = 1;
                    }

                    if (position !== null) {
                        var vals = parseFloat(value - position);
                        if(handle){
                            slidername.noUiSlider.set([null, vals]);
                        } else {
                            slidername.noUiSlider.set([vals, null]);
                        }                                
                    }
                    jQuery("#search-rings-form #submit").trigger("click");
                    break;
                }
        });
    });
}