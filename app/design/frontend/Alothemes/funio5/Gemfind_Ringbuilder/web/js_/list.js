require(["jquery"], function ($) {
    $(document).ready(function () {
        diamondsearhmain();
    });
    $(window).bind("load", function () {
        $(".testSelAll.SumoUnder").insertAfter(
            ".sumo_diamond_certificates .CaptionCont.SelectBox"
        );
        /*$('.SlectBox.SumoUnder').insertAfter(".sumo_gemfind_diamond_origin .CaptionCont.SelectBox");*/
    });
});

function diamondsearhmain() {
    require(["jquery", "nouislider", "wnumb", "jquery/ui"], function (
        $,
        noUiSlider,
        wNumb
    ) {
        var $searchModule = $("#search-diamonds");
        $.ajax({
            url:
                $("#search-diamonds-form #baseurl").val() +
                "ringbuilder/diamond/loadfilter",
            data: $("#search-diamonds-form").serialize(),
            type: "POST",
            dataType: "json",
            cache: true,
            beforeSend: function (settings) {
                /*$('.loading-mask.gemfind-loading-mask').css('display', 'block');*/
                $(".placeholder-content").css("display", "block");
            },
            success: function (response) {
                $("#filter-main-div").html(response.output);
                $("#search-diamonds-form #submit").trigger("click");
                $("button.accordion").click(function (e) {
                    e.preventDefault();
                    $("button.accordion").toggleClass("active");
                    $(".filter-advanced .panel").css("max-height", "383px");
                    $(".filter-advanced .panel").toggleClass("cls-for-hide");
                });
                /*$('#gemfind_diamond_origin').SumoSelect({ 
                    forceCustomRendering: true,
                    triggerChangeCombined:false
                });*/
                /*if($('#filtermode').val() != 'navlabgrown'){*/
                $("#certi-dropdown").SumoSelect({
                    csvDispCount: 2,
                    okCancelInMulti: false,
                    selectAll: true,
                    forceCustomRendering: true,
                    triggerChangeCombined: false,
                    captionFormatAllSelected: "Show All Certificates",
                });
                $(".certificate-div p.select-all").click(function () {
                    if (
                        $(".certificate-div p.select-all").hasClass(
                            "partial"
                        ) &&
                        $(".certificate-div p.select-all").hasClass("selected")
                    ) {
                        $(
                            ".certificate-div .selall ul.options li.selected"
                        ).each(function () {
                            $(this).trigger("click");
                        });
                        $(".certificate-div .selall ul.options li").each(
                            function () {
                                $(this).trigger("click");
                            }
                        );
                    } else if (
                        !$(".certificate-div p.select-all").hasClass(
                            "partial"
                        ) &&
                        $(".certificate-div p.select-all").hasClass("selected")
                    ) {
                        $(".certificate-div .selall ul.options li").each(
                            function () {
                                $(this).trigger("click");
                            }
                        );
                    } else if (
                        $(".certificate-div p.select-all").hasClass(
                            "partial"
                        ) &&
                        !$(".certificate-div p.select-all").hasClass("selected")
                    ) {
                        $(
                            ".certificate-div .selall ul.options li.selected"
                        ).each(function () {
                            $(this).trigger("click");
                        });
                        $(".certificate-div .selall ul.options li").each(
                            function () {
                                $(this).trigger("click");
                            }
                        );
                    } else {
                        $(".certificate-div .selall ul.options li").each(
                            function () {
                                $(this).trigger("click");
                            }
                        );
                    }
                });

                var sliders = jQuery("#noui_price_slider")[0];
                var $min_input = jQuery(sliders).find("input[data-type='min']");
                var $max_input = jQuery(sliders).find("input[data-type='max']");

                var $price_min_val = parseFloat(
                    jQuery(sliders).attr("data-min")
                );
                var $price_max_val = parseFloat(
                    jQuery(sliders).attr("data-max")
                );

                var $start_price_min = parseFloat($min_input.val());
                var $start_price_max = parseFloat($max_input.val());

                var first_half_interval = 50;
                var last_half_interval = 2500;

                if ($price_max_val >= 0 && $price_max_val <= 10000) {
                    var range = {
                        min: [$price_min_val, first_half_interval],
                        "20%": [500, 100],
                        "50%": [5000, 250],
                        "70%": [7000, 500],
                        max: [$price_max_val],
                    };
                } else if (
                    $price_max_val >= 10001 &&
                    $price_max_val <= 100000
                ) {
                    var range = {
                        min: [$price_min_val, first_half_interval],
                        "10%": [500, 100],
                        "30%": [5000, 250],
                        "50%": [10000, 500],
                        "70%": [15000, 1000],
                        "80%": [50000, 2500],
                        max: [$price_max_val],
                    };
                } else {
                    var range = {
                        min: [$price_min_val, first_half_interval],
                        "10%": [500, 100],
                        "30%": [5000, 250],
                        "40%": [10000, 500],
                        "50%": [15000, 1000],
                        "60%": [50000, 2500],
                        "70%": [100000, 10000],
                        "80%": [250000, 25000],
                        max: [$price_max_val],
                    };
                }

                var slider_object = noUiSlider.create(sliders, {
                    start: [$start_price_min, $start_price_max],
                    //tooltips: [true, wNumb({decimals: 1})],
                    connect: true,
                    step: 1,
                    range: range,
                    format: wNumb({
                        decimals: 0,
                        prefix: "",
                        thousand: ",",
                    }),
                });
                sliders.noUiSlider.on("update", function (values, handle) {
                    setTimeout(function() {
                        var value_show = values[handle];
                        if (handle) {
                            $max_input.val(value_show);
                        } else {
                            $min_input.val(value_show);
                        }
                    },1000);
                });

                sliders.noUiSlider.on("change", function (values, handle) {
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                });
                var $price_input1 = jQuery(sliders).find("input.slider-left");
                var $price_input2 = jQuery(sliders).find("input.slider-right");
                var price_inputs = [$price_input1, $price_input2];
                slider_update_textbox(price_inputs, sliders);

                var carat_slider = jQuery("#noui_carat_slider")[0];
                var $carat_min_input = jQuery(carat_slider).find(
                    "input[data-type='min']"
                );
                var $carat_max_input = jQuery(carat_slider).find(
                    "input[data-type='max']"
                );

                var $carat_min_val = parseFloat(
                    jQuery(carat_slider).attr("data-min")
                );
                var $carat_max_val = parseFloat(
                    jQuery(carat_slider).attr("data-max")
                );

                var $start_carat_min = parseFloat($carat_min_input.val());
                var $start_carat_max = parseFloat($carat_max_input.val());

                if (($carat_max_val-$carat_min_val) <= 1) {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        max: [$carat_max_val],
                    };
                } else if ($carat_min_val >= 1.5) {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        "70%": [2.0, 0.1],
                        "80%": [3.5, 0.25],
                        "90%": [4.0, 0.5],
                        max: [$carat_max_val],
                    };
                } else if ($carat_min_val >= 6.01 && $carat_min_val <= 10) {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        "60%": [7.1, 0.05],
                        "70%": [8.0, 0.1],
                        "80%": [9.5, 0.25],
                        "90%": [10.0, 0.5],
                        max: [$carat_max_val],
                    };
                } else if ($carat_max_val >= 0 && $carat_max_val <= 6) {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        "60%": [1.1, 0.05],
                        "70%": [2.0, 0.1],
                        "80%": [3.5, 0.25],
                        "90%": [4.0, 0.5],
                        max: [$carat_max_val],
                    };
                } else if ($carat_max_val >= 6.01 && $carat_max_val <= 10) {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        "60%": [1.1, 0.05],
                        "70%": [2.0, 0.1],
                        "80%": [3.5, 0.25],
                        "90%": [5.0, 0.5],
                        max: [$carat_max_val],
                    };
                } else {
                    var range = {
                        min: [$carat_min_val, 0.01],
                        "50%": [1.1, 0.05],
                        "60%": [2.0, 0.1],
                        "70%": [3.5, 0.25],
                        "80%": [5.0, 0.5],
                        "90%": [10.0, 1],
                        max: [$carat_max_val],
                    };
                }

                var carat_slider_object = noUiSlider.create(carat_slider, {
                    start: [$start_carat_min, $start_carat_max],
                    //tooltips: [true, wNumb({decimals: 2})],
                    connect: true,
                    step: 0.01,
                    range: range,
                    format: wNumb({
                        decimals: 2,
                        prefix: "",
                        thousand: "",
                    }),
                });

                carat_slider.noUiSlider.on("update", function (values, handle) {
                    var carat_value_show = values[handle];
                    if (handle) {
                        $carat_max_input.val(carat_value_show);
                    } else {
                        $carat_min_input.val(carat_value_show);
                    }
                });

                carat_slider.noUiSlider.on("change", function (values, handle) {
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                });

                var $carat_input1 =
                    jQuery(carat_slider).find("input.slider-left");
                var $carat_input2 =
                    jQuery(carat_slider).find("input.slider-right");
                var carat_inputs = [$carat_input1, $carat_input2];
                slider_update_textbox(carat_inputs, carat_slider);

                //jQuery(document).on("keyup", "#zipCode", function(e){
                // depth slider
                var depth_slider = jQuery("#noui_depth_slider")[0];
                var $depth_min_input = jQuery(depth_slider).find(
                    "input[data-type='min']"
                );
                var $depth_max_input = jQuery(depth_slider).find(
                    "input[data-type='max']"
                );

                var $depth_min_val = parseFloat(
                    jQuery(depth_slider).attr("data-min")
                );
                var $depth_max_val = parseFloat(
                    jQuery(depth_slider).attr("data-max")
                );

                var $start_depth_min = parseFloat($depth_min_input.val());
                var $start_depth_max = parseFloat($depth_max_input.val());

                var depth_slider_object = noUiSlider.create(depth_slider, {
                    start: [$start_depth_min, $start_depth_max],
                    //tooltips: [true, wNumb({decimals: 2})],
                    connect: true,
                    step: 1,
                    range: {
                        min: $depth_min_val,
                        max: $depth_max_val,
                    },
                    format: wNumb({
                        decimals: 0,
                        prefix: "",
                        thousand: "",
                    }),
                });

                depth_slider.noUiSlider.on("update", function (values, handle) {
                    var depth_value_show = values[handle];
                    if (handle) {
                        $depth_max_input.val(depth_value_show);
                    } else {
                        $depth_min_input.val(depth_value_show);
                    }
                });

                depth_slider.noUiSlider.on("change", function (values, handle) {
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                });
                var $depth_input1 =
                    jQuery(depth_slider).find("input.slider-left");
                var $depth_input2 =
                    jQuery(depth_slider).find("input.slider-right");
                var depth_inputs = [$depth_input1, $depth_input2];
                slider_update_textbox(depth_inputs, depth_slider);

                var table_slider = jQuery("#noui_tableper_slider")[0];
                var $table_min_input = jQuery(table_slider).find(
                    "input[data-type='min']"
                );
                var $table_max_input = jQuery(table_slider).find(
                    "input[data-type='max']"
                );

                var $table_min_val = parseFloat(
                    jQuery(table_slider).attr("data-min")
                );
                var $table_max_val = parseFloat(
                    jQuery(table_slider).attr("data-max")
                );

                var $start_table_min = parseFloat($table_min_input.val());
                var $start_table_max = parseFloat($table_max_input.val());

                var table_slider_object = noUiSlider.create(table_slider, {
                    start: [$start_table_min, $start_table_max],
                    //tooltips: [true, wNumb({decimals: 2})],
                    connect: true,
                    step: 0.01,
                    range: {
                        min: $table_min_val,
                        max: $table_max_val,
                    },
                    format: wNumb({
                        decimals: 0,
                        prefix: "",
                        thousand: "",
                    }),
                });

                table_slider.noUiSlider.on("update", function (values, handle) {
                    var table_value_show = values[handle];
                    if (handle) {
                        $table_max_input.val(table_value_show);
                    } else {
                        $table_min_input.val(table_value_show);
                    }
                });

                table_slider.noUiSlider.on("change", function (values, handle) {
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                });
                var $table_input1 =
                    jQuery(table_slider).find("input.slider-left");
                var $table_input2 =
                    jQuery(table_slider).find("input.slider-right");
                var table_inputs = [$table_input1, $table_input2];
                slider_update_textbox(table_inputs, table_slider);

                //If search module container exists hook slider to DOM
                if ($searchModule.length) {
                    if ($("#carat_slider").length)
                        new numberSlider("carat", true);
                    if ($("#price_slider").length)
                        new numberSlider("price", true);
                    if ($("#tableper_slider").length)
                        new numberSlider("tableper", true);
                    if ($("#depth_slider").length)
                        new numberSlider("depth", true);
                    $searchModule
                        .find(".ui-slider-handle:even")
                        .addClass("left-handle");
                    $searchModule
                        .find(".ui-slider-handle:odd")
                        .addClass("right-handle");
                }
                $("input:checkbox").change(function () {
                    if ($(this).attr("name") == "diamond_shape[]") {
                        if ($(this).parent().hasClass("lock-shape-type")) {
                            return false;
                        }
                    }
                    if ($(this).attr("name") == "diamond_fancycolor[]") {
                        $(".loading-mask.gemfind-loading-mask").css(
                            "display",
                            "block"
                        );
                        $("ul#shapeul li").css("display", "block");
                    }
                    if ($(this).is(":checked")) {
                        $(this).parent().addClass("selected active");
                        if ($(this).attr("id") == "diamond_shape_round") {
                            $("#cut_filter_container").show();
                        }
                        jQuery(".loading-mask.gemfind-loading-mask").css(
                            "display",
                            "block"
                        );
                        $("#search-diamonds-form #submit").trigger("click");
                    } else {
                        $(this).parent().removeClass("selected active");
                        if ($(this).attr("id") == "diamond_shape_round") {
                            $("#cut_filter_container").hide();
                            var cutElem = document.getElementById("cut-slider");
                            if (cutElem) {
                                $("#cut-slider")[0].noUiSlider.set(
                                    [1, 6],
                                    false,
                                    false
                                );
                            }
                            $("input[name='diamond_cut[]']").val("");
                            $("input[name='diamond_cut[]']").attr(
                                "data-stop",
                                $("#cut-slider").attr("data-steps")
                            );
                        }
                        jQuery(".loading-mask.gemfind-loading-mask").css(
                            "display",
                            "block"
                        );
                        $("#search-diamonds-form #submit").trigger("click");
                    }
                });

                if ($("#filtermode").val() == "navfancycolored") {
                    var element = document.getElementById("navfancycolored");
                    if (typeof element != "undefined" && element != null) {
                        document.getElementById("navfancycolored").className =
                            "active";
                    }
                    if (
                        typeof document.getElementById("navstandard") !=
                            "undefined" &&
                        document.getElementById("navstandard") != null
                    ) {
                        document.getElementById("navstandard").className = "";
                    }
                    if (
                        typeof document.getElementById("navlabgrown") !=
                            "undefined" &&
                        document.getElementById("navlabgrown") != null
                    ) {
                        document.getElementById("navlabgrown").className = "";
                    }
                } else if ($("#filtermode").val() == "navlabgrown") {
                    var element = document.getElementById("navlabgrown");
                    if (typeof element != "undefined" && element != null) {
                        document.getElementById("navlabgrown").className =
                            "active";
                    }
                    if (
                        typeof document.getElementById("navstandard") !=
                            "undefined" &&
                        document.getElementById("navstandard") != null
                    ) {
                        document.getElementById("navstandard").className = "";
                    }
                    if (
                        typeof document.getElementById("navfancycolored") !=
                            "undefined" &&
                        document.getElementById("navfancycolored") != null
                    ) {
                        document.getElementById("navfancycolored").className =
                            "";
                    }
                } else {
                    var element = document.getElementById("navstandard");
                    if (typeof element != "undefined" && element != null) {
                        document.getElementById("navstandard").className =
                            "active";
                    }
                    if (
                        typeof document.getElementById("navfancycolored") !=
                            "undefined" &&
                        document.getElementById("navfancycolored") != null
                    ) {
                        document.getElementById("navfancycolored").className =
                            "";
                    }
                    if (
                        typeof document.getElementById("navlabgrown") !=
                            "undefined" &&
                        document.getElementById("navlabgrown") != null
                    ) {
                        document.getElementById("navlabgrown").className = "";
                    }
                }
            },
            error: function (xhr, status, errorThrown) {},
        });
    });
}

function SaveFilter() {
    require(["jquery"], function ($) {
        $(".loading-mask.gemfind-loading-mask").css("display", "block");
        var shapeCheckboxes = $("input[name='diamond_shape[]']");
        var shapeList = [];
        shapeCheckboxes.each(function () {
            if (this.checked === true) {
                shapeList.push($(this).val());
            }
        });
        /*Cut*/
        var element = ".diamond_cut";
        var diamondCut = "";
        var cutStart = "";
        var cutStop = "";
        if ($(element).length) {
            var roundFound = false;
            shapeList.forEach((res) => {
                if (res == "round") {
                    roundFound = true;
                }
            });
            var cutCheckboxes = jQuery("input[name='diamond_cut[]']");
            if (roundFound) {
                var diamondCut = jQuery(element).val();
            }
            var cutStart = jQuery(element).attr("data-start");
            var cutStop = jQuery(element).attr("data-stop");
        }

        /*Clarity*/
        var element = ".diamond_clarity";
        var diamondClarity = "";
        var ClarityStart = "";
        var ClarityStop = "";
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_clarity[]']");
            var diamondClarity = jQuery(element).val();
            var ClarityStart = jQuery(element).attr("data-start");
            var ClarityStop = jQuery(element).attr("data-stop");
        }

        /*diamond_fancycolor*/
        //        var element = ".diamond_diamondcolor";
        //        var diamondFancycolor = "";
        var FancycolorStart = "";
        var FancycolorStop = "";
        /*
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_fancycolor[]']");
            var diamondFancycolor = jQuery(element).val();
            var FancycolorStart = jQuery(element).attr("data-start");
            var FancycolorStop = jQuery(element).attr("data-stop");
        }
*/

        var fancyColorCheckboxes = $("input[name='diamond_fancycolor[]']");
        var diamondFancycolor = [];
        fancyColorCheckboxes.each(function () {
            if (this.checked === true) {
                diamondFancycolor.push($(this).val());
            }
        });

        /*Mined Color*/
        var element = ".diamond_color";
        var ColorList = "";
        var ColorStart = "";
        var ColorStop = "";
        var diamondColorList = "";
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_color[]']");
            diamondColorList = jQuery(element).val();
            var ColorStart = jQuery(element).attr("data-start");
            var ColorStop = jQuery(element).attr("data-stop");
        }

        /*Polish List*/
        var element = ".diamond_polish";
        var PolishList = "";
        var PolishStart = "";
        var PolishStop = "";
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_polish[]']");
            var PolishList = jQuery(element).val();
            var PolishStart = jQuery(element).attr("data-start");
            var PolishStop = jQuery(element).attr("data-stop");
        }

        /*Fluorescence List*/
        var element = ".diamond_fluorescence";
        var FluorescenceList = "";
        var FluorescenceStart = "";
        var FluorescenceStop = "";
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_fluorescence[]']");
            var FluorescenceList = jQuery(element).val();
            var FluorescenceStart = jQuery(element).attr("data-start");
            var FluorescenceStop = jQuery(element).attr("data-stop");
        }

        /*Fluorescence List*/
        var element = ".diamond_symmetry";
        var SymmetryList = "";
        var SymmetryStart = "";
        var SymmetryStop = "";
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_symmetry[]']");
            var SymmetryList = jQuery(element).val();
            var SymmetryStart = jQuery(element).attr("data-start");
            var SymmetryStop = jQuery(element).attr("data-stop");
        }

        /*FANCY INTENSITY List*/
        //        var element = ".diamond_intensity";
        //        var IntintensityList = "";
        var IntintensityStart = "";
        var IntintensityStop = "";
        /*
        if ($(element).length) {
            var cutCheckboxes = jQuery("input[name='diamond_intintensity[]']");
            var IntintensityList = jQuery(element).val();
            var IntintensityStart = jQuery(element).attr("data-start");
            var IntintensityStop = jQuery(element).attr("data-stop");
        }
*/

        var fancyIntensityCheckboxes = $(
            "input[name='diamond_intintensity[]']"
        );
        var IntintensityList = [];
        fancyIntensityCheckboxes.each(function () {
            if (this.checked === true) {
                IntintensityList.push($(this).val());
            }
        });

        var certiCheckboxes = $("select#certi-dropdown");
        var certificatelist = [];
        certificatelist.push($(certiCheckboxes).val());
        var caratMin = $("div#noui_carat_slider input.slider-left").val();
        var caratMax = $("div#noui_carat_slider input.slider-right").val();
        var PriceMin = $("div#noui_price_slider input.slider-left").val();
        var PriceMax = $("div#noui_price_slider input.slider-right").val();
        var depthMin = $("div#noui_depth_slider input.slider-left").val();
        var depthMax = $("div#noui_depth_slider input.slider-right").val();
        var tableMin = $("div#noui_tableper_slider input.slider-left").val();
        var tableMax = $("div#noui_tableper_slider input.slider-right").val();
        var SOrigin = "";
        var orderBy = $("input#orderby").val();
        var direction = $("input#direction").val();
        var currentPage = $("input#currentpage").val();
        var itemperpage = $("input#itemperpage").val();
        var viewMode = $("input#viewmode").val();
        var filtermode = $("input#filtermode").val();
        var did = $("input#did").val();
        var formdata = {
            shapeList: shapeList.toString(),
            caratMin: caratMin,
            caratMax: caratMax,
            PriceMin: PriceMin,
            PriceMax: PriceMax,
            certificate: certificatelist.toString(),
            depthMin: depthMin,
            depthMax: depthMax,
            tableMin: tableMin,
            tableMax: tableMax,
            FluorescenceList: FluorescenceList.toString(),
            FluorescenceStart: FluorescenceStart,
            FluorescenceStop: FluorescenceStop,
            CutGradeList: diamondCut.toString(),
            CutStart: cutStart,
            CutStop: cutStop,
            ClarityList: diamondClarity.toString(),
            ClarityStart: ClarityStart,
            ClarityStop: ClarityStop,
            FancycolorList: diamondFancycolor.toString(),
            FancycolorStart: FancycolorStart,
            FancycolorStop: FancycolorStop,
            ColorList: diamondColorList.toString(),
            ColorStart: ColorStart,
            ColorStop: ColorStop,
            IntintensityList: IntintensityList.toString(),
            IntintensityStart: IntintensityStart,
            IntintensityStop: IntintensityStop,
            SymmetryList: SymmetryList.toString(),
            SymmetryStart: SymmetryStart,
            SymmetryStop: SymmetryStop,
            polishList: PolishList.toString(),
            PolishStart: PolishStart,
            PolishStop: PolishStop,
            Filtermode: filtermode,
            SOrigin: SOrigin,
            currentPage: currentPage,
            orderBy: orderBy,
            direction: direction,
            viewmode: viewMode,
            itemperpage: itemperpage,
            did: did,
        };
        var expire = new Date();
        expire.setDate(expire.getDate() + 10 * 24 * 60 * 60 * 1000);
        if (filtermode == "navfancycolored") {
            $.cookie("savefiltercookiefancy", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        } else if (filtermode == "navstandard") {
            $.cookie("savefiltercookie", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        } else {
            $.cookie("savefiltercookielabgrown", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        }

        setTimeout(function () {
            $(".loading-mask.gemfind-loading-mask").css("display", "none");
        }, 400);
    });
}

function ResetBackCookieFilter() {
    require(["jquery"], function ($) {
        $.cookie("savebackvaluediafancy", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("savebackvaluedia", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("savebackvaluedialabgrown", "", {
            path: "/",
            expires: -1,
        });
    });
}

function ResetFilter() {
    require(["jquery"], function ($) {
        $.cookie("savefiltercookie", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savefiltercookie=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.cookie("savefiltercookiefancy", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savefiltercookiefancy=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.cookie("savefiltercookielabgrown", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savefiltercookielabgrown=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.cookie("savebackvaluediafancy", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savebackvaluediafancy=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.cookie("savebackvaluedia", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savebackvaluedia=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $.cookie("savebackvaluedialabgrown", "", {
            path: "/",
            expires: -1,
        });
        document.cookie =
            "savebackvaluedialabgrown=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.reload();
    });
}
function slider_update_textbox(slider_inputs, slidername) {
    // Listen to keydown events on the input field.
    slider_inputs.forEach(function (input, handle) {
        input.change(function () {
            var vals = parseFloat(this.value);
            if (handle) {
                slidername.noUiSlider.set([null, vals]);
            } else {
                slidername.noUiSlider.set([vals, null]);
            }
            jQuery(".loading-mask.gemfind-loading-mask").css(
                "display",
                "block"
            );
            jQuery("#search-diamonds-form #submit").trigger("click");
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
                    if (handle) {
                        slidername.noUiSlider.set([null, vals]);
                    } else {
                        slidername.noUiSlider.set([vals, null]);
                    }
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
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
                        if (handle) {
                            slidername.noUiSlider.set([null, vals]);
                        } else {
                            slidername.noUiSlider.set([vals, null]);
                        }
                    }
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                    break;
                case 40:
                    position = step[0];
                    if (position === false) {
                        position = 1;
                    }

                    if (position !== null) {
                        var vals = parseFloat(value - position);
                        if (handle) {
                            slidername.noUiSlider.set([null, vals]);
                        } else {
                            slidername.noUiSlider.set([vals, null]);
                        }
                    }
                    jQuery(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                    jQuery("#search-diamonds-form #submit").trigger("click");
                    break;
            }
        });
    });
}
