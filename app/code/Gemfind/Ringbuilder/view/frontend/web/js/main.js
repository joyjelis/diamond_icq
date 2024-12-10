require(["jquery", "jquery/ui"], function ($) {
    var $searchModule = $("#search-diamonds");
    $(document).ready(function () {
        $("#search-diamonds-form #submitby").val("ajax");
        $("form#search-diamonds-form").submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#search-diamonds-form").attr("action"),
                data: $("#search-diamonds-form").serialize(),
                type: "POST",
                dataType: "json",
                cache: true,
                beforeSend: function (settings) {
                    $('.loading-mask.gemfind-loading-mask').css('display', 'block');
                },
                success: function (response) {
                    $(".result").html(response.output);
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );
                    if (
                        parseInt($("div.number-of-search strong").html()) <
                            20 &&
                        $("#currentpage").val() > 1
                    ) {
                        $("#currentpage").val(1);
                        $("#search-diamonds-form #submit").trigger("click");
                    }
                    var mode = $("input#viewmode").val();
                    if (mode == "grid") {
                        $("li.grid-view a").addClass("active");
                        $("li.list-view a").removeClass("active");
                        $("#list-mode").addClass("cls-for-hide");
                        $(
                            "#grid-mode, #gridview-orderby, div.grid-view-sort"
                        ).removeClass("cls-for-hide");
                    }

                    $(".change-view-result li a").click(function () {
                        $(this).addClass("active");
                        if ($(this).parent("li").attr("class") == "list-view") {
                            $("li.grid-view a").removeClass("active");
                            $("#list-mode").removeClass("cls-for-hide");
                            $("#grid-mode, div.grid-view-sort").addClass(
                                "cls-for-hide"
                            );
                            $("input#viewmode").val("list");
                        } else {
                            $("li.list-view a").removeClass("active");
                            $("#list-mode").addClass("cls-for-hide");
                            $("#grid-mode, div.grid-view-sort").removeClass(
                                "cls-for-hide"
                            );
                            $("input#viewmode").val("grid");
                        }
                    });

                    $(".search-product-grid .trigger-info").click(function (e) {
                        $(this).parent().next().toggleClass("active");
                        e.stopPropagation();
                    });

                    $(".search-product-grid .product-inner-info").click(
                        function (e) {
                            e.stopPropagation();
                        }
                    );

                    $(document).click(function (e) {
                        $(
                            ".search-product-grid .product-inner-info"
                        ).removeClass("active");
                    });

                    $("#gridview-orderby option").each(function () {
                        if ($(this).val() == $("input#orderby").val()) {
                            $(this).attr("selected", "selected");
                            return;
                        }
                    });
                    if ($("input#direction").val() == "ASC") {
                        $("#ASC").addClass("active");
                        $("#DESC").removeClass("active");
                    } else {
                        $("#DESC").addClass("active");
                        $("#ASC").removeClass("active");
                    }

                    $("#pagesize option").each(function () {
                        if ($(this).val() == $("input#itemperpage").val()) {
                            $(this).attr("selected", "selected");
                            return;
                        }
                    });
                    /*$("#gemfind_diamond_origin").change(function() {
                        $("#search-diamonds-form #submit").trigger("click");
                    });*/

                    $("th#" + $("input#orderby").val()).addClass(
                        $("input#direction").val()
                    );
                    $("#gridview-orderby").SumoSelect({
                        forceCustomRendering: true,
                        triggerChangeCombined: false,
                    });

                    $("#pagesize").SumoSelect({
                        forceCustomRendering: true,
                        triggerChangeCombined: false,
                    });
                    $(".pagesize.SumoUnder").insertAfter(
                        ".sumo_pagesize .CaptionCont.SelectBox"
                    );
                    $(".gridview-orderby.SumoUnder").insertAfter(
                        ".sumo_gridview-orderby .CaptionCont.SelectBox"
                    );

                    $("#searchdidfield").keydown(function (e) {
                        if (e.keyCode == 13) {
                            $("#searchdid").trigger("click");
                        }
                    });
                    $("#searchdid").click(function () {
                        if ($("#searchdidfield").val() != "") {
                            $("input#did").val($("#searchdidfield").val());
                            $("#search-diamonds-form #submit").trigger("click");
                        } else {
                            $("input#searchdidfield").effect(
                                "highlight",
                                { color: "#f56666" },
                                2000
                            );
                            return false;
                        }
                    });
                    if ($("input#did").val()) {
                        $("#searchintable").addClass("executed");
                    }
                    $("#searchdidfield").val($("input#did").val());
                    $("input#did").val("");
                    $("#resetsearchdata").click(function () {
                        $("#searchdidfield").val();
                        $("input#did").val("");
                        $("#search-diamonds-form #submit").trigger("click");
                    });
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );
                    try {
                        SaveFilter();
                    } catch (e) {
                        console.log("Undefined SaveFilter");
                    }
                },
                error: function (xhr, status, errorThrown) {
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );
                    window.location.href = window.location.origin + '/under-maintenance';
                    console.log("Error happens. Try again.");
                    console.log(errorThrown);
                },
                timeout: 30000
            });
        });
    });
});

function fnSort(strSort) {
    var orderBy = document.getElementById("orderby").value;
    var direction = document.getElementById("direction").value;
    if (strSort == orderBy) {
        if (direction == "ASC") direction = "DESC";
        else direction = "ASC";
    } else {
        direction = "ASC";
    }
    orderBy = strSort;
    direction = direction;
    document.getElementById("orderby").value = strSort;
    document.getElementById("direction").value = direction;
    document.getElementById("currentpage").value = 1;
    document.getElementById("submit").click();
}

function gridSort(selectObject) {
    var orderBy = document.getElementById("orderby").value;
    var direction = document.getElementById("direction").value;
    var selectedvalue = selectObject.value;
    orderBy = selectedvalue;
    direction = direction;
    document.getElementById("orderby").value = selectedvalue;
    document.getElementById("direction").value = direction;
    document.getElementById("currentpage").value = 1;
    document.getElementById("submit").click();
}

function gridDire(selectedvalue) {
    var direction = document.getElementById("direction").value;
    var selectedvalue = selectedvalue;
    if (direction != selectedvalue) {
        direction = selectedvalue;
    }
    if (direction == "ASC") {
        document.getElementById("DESC").className = "";
        document.getElementById("ASC").className = "active";
    } else {
        document.getElementById("DESC").className = "active";
        document.getElementById("ASC").className = "";
    }
    document.getElementById("direction").value = direction;
    document.getElementById("currentpage").value = 1;
    document.getElementById("submit").click();
}

function ItemPerPage(selectObject) {
    var resultperpage = document.getElementById("itemperpage").value;
    var selectedvalue = selectObject.value;
    resultperpage = selectedvalue;
    document.getElementById("itemperpage").value = selectedvalue;
    document.getElementById("currentpage").value = 1;
    document.getElementById("submit").click();
}

function PagerClick(intpageNo) {
    document.getElementById("currentpage").value = intpageNo;
    document.getElementById("submit").click();
}

function mode(id) {
    if (id != "navcompare") document.getElementById("filtermode").value = id;
    require(["jquery"], function ($) {
        $(".gemfind-loading-mask").show();

        if (id=='navfancycolored') {
            // set fancy view cookie
            var expireTime = new Date();
            expireTime.setTime(expireTime.getTime() + 24 * 60 * 60 * 1000);  //expires in 24hrs
            $.cookie("saveViewType", "fancy", {path: '/', expires: expireTime});
        } else {
            // clear fancy view cookie
            var expireTime = new Date();
            expireTime.setTime(expireTime.getTime() + 24 * 60 * 60 * 1000);  //expires in 24hrs
            $.cookie("saveViewType", "", {expires: expireTime});
        }
    });
}

function SetBackValue(diamondid) {
    require(["jquery"], function ($) {
        $('.loading-mask.gemfind-loading-mask').css('display', 'block');
        var shapeCheckboxes = $("input[name='diamond_shape[]']");
        var shapeList = [];
        shapeCheckboxes.each(function () {
            if (this.checked === true) {
                shapeList.push($(this).val());
            }
        });
        var cutCheckboxes = $("input[name='diamond_cut[]']");
        var CutGradeList = [];
        cutCheckboxes.each(function () {
            if (this.checked === true) {
                CutGradeList.push($(this).val());
            }
        });
        var colorCheckboxes = $("input[name='diamond_color[]']");
        var ColorList = [];
        colorCheckboxes.each(function () {
            if (this.checked === true) {
                ColorList.push($(this).val());
            }
        });
        var clarityCheckboxes = $("input[name='diamond_clarity[]']");
        var ClarityList = [];
        clarityCheckboxes.each(function () {
            if (this.checked === true) {
                ClarityList.push($(this).val());
            }
        });
        var polishCheckboxes = $("input[name='diamond_polish[]']");
        var polishList = [];
        polishCheckboxes.each(function () {
            if (this.checked === true) {
                polishList.push($(this).val());
            }
        });
        var symmetryCheckboxes = $("input[name='diamond_symmetry[]']");
        var SymmetryList = [];
        symmetryCheckboxes.each(function () {
            if (this.checked === true) {
                SymmetryList.push($(this).val());
            }
        });
        var fluorescenceCheckboxes = $("input[name='diamond_fluorescence[]']");
        var FluorescenceList = [];
        fluorescenceCheckboxes.each(function () {
            if (this.checked === true) {
                FluorescenceList.push($(this).val());
            }
        });

        var fancycolorCheckboxes = $("input[name='diamond_fancycolor[]']");
        var FancycolorList = [];
        fancycolorCheckboxes.each(function () {
            if (this.checked === true) {
                FancycolorList.push($(this).val());
            }
        });

        var intintensityCheckboxes = $("input[name='diamond_intintensity[]']");
        var intintensityList = [];
        intintensityCheckboxes.each(function () {
            if (this.checked === true) {
                intintensityList.push($(this).val());
            }
        });

        var certiCheckboxes = $("select#certi-dropdown");
        var certificatelist = [];
        certificatelist.push($(certiCheckboxes).val());
        var caratMin = $("div#carat_slider input.slider-left").val();
        var caratMax = $("div#carat_slider input.slider-right").val();
        var PriceMin = $("div#price_slider input.slider-left").val();
        var PriceMax = $("div#price_slider input.slider-right").val();
        var depthMin = $("div#depth_slider input.slider-left").val();
        var depthMax = $("div#depth_slider input.slider-right").val();
        var tableMin = $("div#tableper_slider input.slider-left").val();
        var tableMax = $("div#tableper_slider input.slider-right").val();
        var SOrigin = "";
        var orderBy = $("input#orderby").val();
        var direction = $("input#direction").val();
        var currentPage = $("input#currentpage").val();
        var viewMode = $("input#viewmode").val();
        var did = $("input#did").val();
        var filtermode = $("input#filtermode").val();
        var formdata = {
            shapeList: shapeList.toString(),
            caratMin: caratMin,
            caratMax: caratMax,
            PriceMin: PriceMin,
            PriceMax: PriceMax,
            certificate: certificatelist.toString(),
            SymmetryList: SymmetryList.toString(),
            polishList: polishList.toString(),
            depthMin: depthMin,
            depthMax: depthMax,
            tableMin: tableMin,
            tableMax: tableMax,
            FluorescenceList: FluorescenceList.toString(),
            FancycolorList: FancycolorList.toString(),
            IntintensityList: intintensityList.toString(),
            CutGradeList: CutGradeList.toString(),
            ColorList: ColorList.toString(),
            ClarityList: ClarityList.toString(),
            SOrigin: SOrigin,
            currentPage: currentPage,
            orderBy: orderBy,
            direction: direction,
            viewmode: viewMode,
            filtermode: filtermode,
            did: did,
            backdiamondid: diamondid,
        };
        var expire = new Date();
        expire.setTime(expire.getTime() + 0.5 * 3600 * 1000);
        if (filtermode == "navfancycolored") {
            $.cookie("savebackvaluediafancy", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        } else if (filtermode == "navstandard") {
            $.cookie("savebackvaluedia", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        } else {
            $.cookie("savebackvaluedialabgrown", JSON.stringify(formdata), {
                path: "/",
                expires: expire,
            });
        }

        var cookieExistRing = $.cookie("saveringbackvalue");
        if (cookieExistRing) {
            ringSettingArr = JSON.parse(cookieExistRing);
            ringSettingArr.fromRing = "";

            var expireRing = new Date();
            expireRing.setDate(expireRing.getDate() + 10 * 24 * 60 * 60 * 1000);
            $.cookie("saveringbackvalue", JSON.stringify(ringSettingArr), {
                path: "/",
                expires: expireRing,
            });
        }
    });
}
