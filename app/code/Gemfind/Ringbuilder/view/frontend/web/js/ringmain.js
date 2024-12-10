require(["jquery", "jquery/ui"], function ($) {
    $(document).ready(function () {
        $("#search-rings-form #submitby").val("ajax");
        $("form#search-rings-form").submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#search-rings-form").attr("action"),
                data: $("#search-rings-form").serialize(),
                type: "POST",
                dataType: "json",
                cache: true,
                beforeSend: function (settings) {
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "block"
                    );
                },
                success: function (response) {
                    /*
							var ignoreFields = ["itemperpage", "orderby", "direction", "currentpage", "did", "submitby", "backdiamondid", "viewmode", "baseurl", "defaultshapevalue", "form_key", "filtermode"];
							var newurl = $.param($('#search-rings-form').serializeArray().filter(function(r) { 
									return ignoreFields.indexOf(r.name) < 0
								 }));
							console.log($('#search-rings-form').serializeArray());
							window.history.pushState('page2', 'Title', $('#search-rings-form #baseurl').val()+'ringbuilder/settings?'+newurl);
                            */
                    $(".result").html(response.output);
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );

                    if (
                        $("div.number-of-search strong").html() < 20 &&
                        $("#currentpage").val() > 1
                    ) {
                        $("#currentpage").val(1);
                        $("#search-rings-form #submit").trigger("click");
                    }

                    var mode = $("input#viewmode").val();
                    setTimeout(function () {
                        $("#" + mode).trigger("click");
                    }, 1000);

                    if (mode == "grid") {
                        $("li.grid-view a").addClass("active");
                        $("li.grid-view-wide a").removeClass("active");
                        $("#grid-mode-wide").addClass("cls-for-hide");
                        $(
                            "#grid-mode, #gridview-orderby, div.grid-view-sort"
                        ).removeClass("cls-for-hide");
                    }

                    $(".change-view-result li a").click(function () {
                        $(this).addClass("active");
                        if (
                            $(this).parent("li").attr("class") ==
                            "grid-view-wide"
                        ) {
                            $("li.grid-view a").removeClass("active");
                            $("#grid-mode-wide").removeClass("cls-for-hide");
                            $("#grid-mode").addClass("cls-for-hide");
                            $("input#viewmode").val("list");
                        } else {
                            $("li.grid-view-wide a").removeClass("active");
                            $("#grid-mode-wide").addClass("cls-for-hide");
                            $("#grid-mode").removeClass("cls-for-hide");
                            $("input#viewmode").val("grid");
                        }
                    });

                    $(document).click(function (e) {
                        $(
                            ".search-product-grid .product-inner-info"
                        ).removeClass("active");
                    });

                    $("#pagesize option").each(function () {
                        if ($(this).val() == $("input#itemperpage").val()) {
                            $(this).attr("selected", "selected");
                            return;
                        }
                    });

                    $(".change-view li a").click(function () {
                        var modeenabled = $(this).attr("id");
                        $("input#viewmode").val(modeenabled);
                    });
                    $("#gridview-orderby option").each(function () {
                        if ($(this).val() == $("input#orderby").val()) {
                            $(this).attr("selected", "selected");
                            return;
                        }
                    });

                    $("#searchdidfield").keydown(function (e) {
                        if (e.keyCode == 13) {
                            $("#searchsettingid").trigger("click");
                        }
                    });
                    $("#searchsettingid").click(function () {
                        if ($("#searchdidfield").val() != "") {
                            $("input#settingid").val(
                                $("#searchdidfield").val()
                            );
                            $("#search-rings-form #submit").trigger("click");
                        } else {
                            $("input#searchdidfield").effect(
                                "highlight",
                                { color: "#f56666" },
                                2000
                            );
                            return false;
                        }
                    });
                    if ($("input#settingid").val()) {
                        $("#searchintable").addClass("executed");
                    }
                    $("#searchdidfield").val($("input#settingid").val());
                    /*$('input#settingid').val('');*/
                    $("#resetsearchdata").click(function () {
                        $("#searchdidfield").val();
                        $("input#settingid").val("");
                        $("#search-rings-form #submit").trigger("click");
                    });
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );
                },
                error: function (xhr, status, errorThrown) {
                    $(".loading-mask.gemfind-loading-mask").css(
                        "display",
                        "none"
                    );
                    console.log("Error happens. Try again.");
                    console.log(errorThrown);
                },
            });
        });
    });
});

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
    document.getElementById("settingid").value =
        document.getElementById("searchdidfield").value;
    document.getElementById("submit").click();
}

function gridSort(selectObject) {
    var orderBy = document.getElementById("orderby").value;
    var selectedvalue = selectObject.value;
    orderBy = selectedvalue;
    document.getElementById("orderby").value = selectedvalue;
    document.getElementById("currentpage").value = 1;
    document.getElementById("submit").click();
}

function SetBackValue(diamondid, requesteddata) {
    require(["jquery"], function ($) {
        $('.loading-mask.gemfind-loading-mask').css('display', 'block');
        var ringcollection = $("input[name='ring_collection']");
        var ringcollectionList = [];
        ringcollection.each(function () {
            if (this.checked === true) {
                ringcollectionList.push($(this).val());
            }
        });
        var ringshapeCheckboxes = $("input[name='ring_shape']");
        var ringshapeList = [];
        ringshapeCheckboxes.each(function () {
            if (this.checked === true) {
                ringshapeList.push($(this).val());
            }
        });
        var ringmetalCheckboxes = $("input[name='ring_metal']");
        var ringmetalList = [];
        ringmetalCheckboxes.each(function () {
            if (this.checked === true) {
                ringmetalList.push($(this).val());
            }
        });

        var PriceMin = $("div#noui_price_slider_rb input.slider-left").val();
        var PriceMax = $("div#noui_price_slider_rb input.slider-right").val();
        var orderBy = $("input#orderby").val();
        var direction = $("input#direction").val();
        var currentPage = $("input#currentpage").val();
        var itemperpage = $("input#itemperpage").val();
        var filtermode = $("input#filtermode").val();
        var settingid = $("input#settingid").val();
        var viewmode = $("input#viewmode").val();
        var formdata = {
            shapeList: ringshapeList.toString(),
            ringcollection: ringcollectionList.toString(),
            ringmetalList: ringmetalList.toString(),
            PriceMin: PriceMin,
            PriceMax: PriceMax,
            Filtermode: filtermode,
            currentPage: currentPage,
            orderBy: orderBy,
            direction: direction,
            itemperpage: itemperpage,
            viewmode: viewmode,
            SID: settingid,
            fromRing: "1",
        };

        $.cookie("saveringbackvalue", "", {
            path: "/",
            expires: -1,
        });
        var expire = new Date();
        expire.setDate(expire.getDate() + 10 * 24 * 60 * 60 * 1000);
        $.cookie("saveringbackvalue", JSON.stringify(formdata), {
            path: "/",
            expires: expire,
        });
    });
}

function SaveFilter() {
    require(["jquery"], function ($) {
        $(".loading-mask.gemfind-loading-mask").css("display", "block");
        var ringcollection = $("input[name='ring_collection']");
        var ringcollectionList = [];
        ringcollection.each(function () {
            if (this.checked === true) {
                ringcollectionList.push($(this).val());
            }
        });
        var ringshapeCheckboxes = $("input[name='ring_shape']");
        var ringshapeList = [];
        ringshapeCheckboxes.each(function () {
            if (this.checked === true) {
                ringshapeList.push($(this).val());
            }
        });
        var ringmetalCheckboxes = $("input[name='ring_metal']");
        var ringmetalList = [];
        ringmetalCheckboxes.each(function () {
            if (this.checked === true) {
                ringmetalList.push($(this).val());
            }
        });

        var PriceMin = $("div#noui_price_slider_rb input.slider-left").val();
        var PriceMax = $("div#noui_price_slider_rb input.slider-right").val();
        var orderBy = $("input#orderby").val();
        var direction = $("input#direction").val();
        var currentPage = $("input#currentpage").val();
        var itemperpage = $("input#itemperpage").val();
        var filtermode = $("input#filtermode").val();
        var settingid = $("input#settingid").val();
        var viewmode = $("input#viewmode").val();
        var formdata = {
            shapeList: ringshapeList.toString(),
            ringcollection: ringcollectionList.toString(),
            ringmetalList: ringmetalList.toString(),
            PriceMin: PriceMin,
            PriceMax: PriceMax,
            Filtermode: filtermode,
            currentPage: currentPage,
            orderBy: orderBy,
            direction: direction,
            itemperpage: itemperpage,
            viewmode: viewmode,
            SID: settingid,
        };
        $.cookie("saveringbackvalue", "", {
            path: "/",
            expires: -1,
        });
        var expire = new Date();
        expire.setDate(expire.getDate() + 10 * 24 * 60 * 60 * 1000);
        $.cookie("saveringfiltercookie", JSON.stringify(formdata), {
            path: "/",
            expires: expire,
        });

        setTimeout(function () {
            $(".loading-mask.gemfind-loading-mask").css("display", "none");
        }, 400);
    });
}

function ResetFilter() {
    require(["jquery"], function ($) {
        $.cookie("saveringfiltercookie", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("saveringbackvalue", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("savebackvaluedia", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("ringsetting", "", {
            path: "/",
            expires: -1,
        });
        $.cookie("diamondsetting", "", {
            path: "/",
            expires: -1,
        });
        window.location.reload();
    });
}
