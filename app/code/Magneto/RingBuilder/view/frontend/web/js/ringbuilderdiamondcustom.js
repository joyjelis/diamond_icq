require([
  "jquery",
  "mage/translate",
  "Magento_Ui/js/lib/view/utils/dom-observer",
], function ($, $t) {
  domObserver = require("Magento_Ui/js/lib/view/utils/dom-observer");

  $(document).ready(function () {
    domObserver.get(".SumoSelect.sumo_gridview-orderby", function (elem) {
      $(elem).before('<div class="sort-label cu">' + $t("Sort By") + "</div>");
    });

    domObserver.get(
      ".search-product-grid .product-slide-button .trigger-info",
      function (elem) {
        $(elem).click(function () {
          $(elem).parent().toggleClass("active");
        });

        $(document).click(function (e) {
          $(".search-product-grid .product-slide-button.active").removeClass(
            "active"
          );
        });
      }
    );

    domObserver.get("#gift_deadline", function (elem) {
      $(elem).change(function () {
        if ($(this).val() == "") {
          $('label[for="' + $(this).attr("id") + '"]').removeClass("hasvalue");
        } else {
          $('label[for="' + $(this).attr("id") + '"]').addClass("hasvalue");
        }
      });
    });

    domObserver.get(".filter-advanced .accordion.accordian3", function (elem) {
      $(elem).click(function () {
        $(elem).toggleClass("active");
      });
    });

    domObserver.get(".pagination-div a", function (elem) {
      $(elem).click(function () {
        if (!$(elem).hasClass("active")) {
          $(".gemfind-loading-mask").show();
        }
      });
    });

    domObserver.get('input[name="compare"]', function (elem) {
      $(elem).click(function () {
        var maxAllowed = 5;
        var cnt = $("input[name='compare']:checked").length;
        if (cnt > maxAllowed) {
          return true;
        }

        var label = $(elem).attr("id");
        if (label != "") {
          if ($(elem).is(":checked")) {
            var OriginalText = $('label[for="' + label + '"]').text();
            $('label[for="' + label + '"]').text($t("Added"));
            $('label[for="' + label + '"]').data(
              "original_label",
              OriginalText
            );
          } else {
            var OriginalText = $('label[for="' + label + '"]').data(
              "original_label"
            );
            $('label[for="' + label + '"]').text(OriginalText);
          }
        }
      });
    });

    domObserver.get("#metalcustomoption .option", function (elem) {
      $(elem).click(function () {
        $("#metalcustomoption .option.active").removeClass("active");
        var option = $(this).data("value");
        var name = $(this).data("name");
        $(this).addClass("active");
        $("#metalcustomoptionactive").text(name);
        $("#metal_type").val(option).trigger("change");
      });

      $("#metalcustomoptionactive").text(
        $("#metal_type option:selected").text()
      );
      $(
        '#metalcustomoption .option[data-value="' +
          $("#metal_type").val() +
          '"]'
      ).addClass("active");
    });

    domObserver.get("#stonecustomoption .option", function (elem) {
      $(elem).click(function () {
        $("#stonecustomoption .option.active").removeClass("active");
        var option = $(this).data("value");
        var name = $(this).data("name");
        $(this).addClass("active");
        $("#stonecustomoptionactive").text(name);
        $("#stonequality").val(option).trigger("change");
      });

      $("#stonecustomoptionactive").text(
        $("#stonequality option:selected").text()
      );
      $(
        '#stonecustomoption .option[data-value="' +
          $("#stonequality").val() +
          '"]'
      ).addClass("active");
    });

    domObserver.get("#centerstonecustomoption .option", function (elem) {
      $(elem).click(function () {
        $("#centerstonecustomoption .option.active").removeClass("active");
        var option = $(this).data("value");
        var name = $(this).data("name");
        $(this).addClass("active");
        $("#centerstonecustomoptionactive").text(name);
        $("#centerstonesize").val(option).trigger("change");
      });

      $("#centerstonecustomoptionactive").text(
        $("#centerstonesize option:selected").text()
      );
      $(
        '#centerstonecustomoption .option[data-value="' +
          $("#centerstonesize").val() +
          '"]'
      ).addClass("active");
    });

    domObserver.get("#ringsizecustomoption .option", function (elem) {
      $(elem).click(function () {
        $("#ringsizecustomoption .option.active").removeClass("active");
        var option = $(this).data("value");
        var name = $(this).data("name");
        $(this).addClass("active");
        $("#ringsizecustomoptionactive").text(name);
        $("#ring_size").val(option).trigger("change");
      });

      $("#ringsizecustomoptionactive").text(
        $("#ring_size option:selected").text()
      );
      $(
        '#ringsizecustomoption .option[data-value="' +
          $("#ring_size").val() +
          '"]'
      ).addClass("active");
    });

    domObserver.get("#ringbuilderfilter", function (elem) {
      $(elem).click(function () {
        $("#mobilefiltercontent").addClass("active");
      });
    });

    domObserver.get("#mobilefiltercontent #closefilter", function (elem) {
      $(elem).click(function () {
        $("#mobilefiltercontent").removeClass("active");
      });
    });

    // Mobile Filters
    domObserver.get(
      "#mobilefiltercontent .mobile_filter_shape_filter",
      function (elem) {
        $(elem).click(function () {
          if ($(this).parent().hasClass("lock-shape-type")) {
            return false;
          }
          if ($(this).parent().hasClass("active")) {
            $(this).parent().removeClass("active");
            $(this).removeClass("active");
          } else {
            $(this).parent().addClass("active");
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .mobile_filter_color_rang_filter",
      function (elem) {
        $(elem).click(function () {
          if ($(this).parent().hasClass("active")) {
            $(this).parent().removeClass("active");
          } else {
            $(this).parent().addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .mobile_filter_intensity_filter",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_cut_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_carat_min",
      function (elem) {
        $(elem).keyup(function () {
          $('input[name="diamond_carats[from]"]').val($(this).val());
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_carat_max",
      function (elem) {
        $(elem).keyup(function () {
          $('input[name="diamond_carats[to]"]').val($(this).val());
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_price_min",
      function (elem) {
        $(elem).keyup(function () {
          $("#rb_min_price").val($("#ringstep2_price_min").val());
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_price_max",
      function (elem) {
        $(elem).keyup(function () {
          $("#rb_max_price").val($("#ringstep2_price_max").val());
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_depth_min",
      function (elem) {
        $(elem).keyup(function () {
          $('input[type="number"][name="diamond_depth[from]"]').val(
            $(elem).val()
          );
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_depth_max",
      function (elem) {
        $(elem).keyup(function () {
          $('input[type="number"][name="diamond_depth[to]"]').val(
            $(elem).val()
          );
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_table_min",
      function (elem) {
        $(elem).keyup(function () {
          $('input[type="number"][name="diamond_table[from]"]').val(
            $(elem).val()
          );
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent #ringstep2_table_max",
      function (elem) {
        $(elem).keyup(function () {
          $('input[type="number"][name="diamond_table[to]"]').val(
            $(elem).val()
          );
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_color_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_clarity_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_polish_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_fluorescence_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_symmetry_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );

    domObserver.get(
      "#mobilefiltercontent .diamond_certificates_filter_selection",
      function (elem) {
        $(elem).click(function () {
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
          } else {
            $(this).addClass("active");
          }
        });
      }
    );
    //  Clear All
    domObserver.get(
      "#mobilefiltercontent #mringbuilder_step2_clear_call",
      function (elem) {
        $(elem).click(function () {
          $("input[name='diamond_cut[]']").val();
          $('input[type="checkbox"][name="diamond_shape[]"]').attr(
            "checked",
            false
          );
          $('input[type="checkbox"][name="diamond_fancycolor[]"]').attr(
            "checked",
            false
          );
          $('input[type="checkbox"][name="diamond_intintensity[]"]').attr(
            "checked",
            false
          );

          $("#mobilefiltercontent .mobile_filter_shape_filter.active")
            .parent()
            .removeClass("active");

          $(
            "#mobilefiltercontent .mobile_filter_shape_filter.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .mobile_filter_color_rang_filter.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .mobile_filter_intensity_filter.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_clarity_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_color_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_cut_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_polish_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_fluorescence_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_symmetry_filter_selection.active"
          ).removeClass("active");
          $(
            "#mobilefiltercontent .diamond_certificates_filter_selection.active"
          ).removeClass("active");

          $(
            "#mobilefiltercontent .active > .mobile_filter_color_rang_filter"
          ).each(function () {
            $(this).parent().removeClass("active");
          });

          $("input[name='diamond_cut[]']").val("");
          $("input[name='diamond_color[]']").val("");
          $("input[name='diamond_clarity[]']").val("");
          $("input[name='diamond_polish[]']").val("");
          $("input[name='diamond_fluorescence[]']").val("");
          $("input[name='diamond_symmetry[]']").val("");
          $("select[name='diamond_certificates[]'] option").attr(
            "selected",
            false
          );

          $("#rb_min_carat").val($("#ringstep2_carat_min").data("original"));
          $("#rb_max_carat").val($("#ringstep2_carat_max").data("original"));

          $("#rb_min_price").val($("#ringstep2_price_min").data("original"));
          $("#rb_max_price").val($("#ringstep2_price_max").data("original"));

          $('input[type="number"][name="diamond_depth[from]"]').val(
            $("#ringstep2_depth_min").data("original")
          );
          $('input[type="number"][name="diamond_depth[to]"]').val(
            $("#ringstep2_depth_max").data("original")
          );

          $('input[type="number"][name="diamond_table[from]"]').val(
            $("#ringstep2_table_min").data("original")
          );
          $('input[type="number"][name="diamond_table[to]"]').val(
            $("#ringstep2_table_max").data("original")
          );

          $("#mobilefiltercontent #closefilter").click();
          $("#search-diamonds-form #submit").trigger("click");
          $(".gemfind-loading-mask").show();
        });
      }
    );
    // Apply Filter
    domObserver.get(
      "#mobilefiltercontent #mringbuilder_step2_apply_call",
      function (elem) {
        $(elem).click(function () {
          $('input[type="checkbox"][name="diamond_shape[]"]').attr(
            "checked",
            false
          );
          $('input[type="checkbox"][name="diamond_fancycolor[]"]').attr(
            "checked",
            false
          );
          $('input[type="checkbox"][name="diamond_intintensity[]"]').attr(
            "checked",
            false
          );

          var cutfilter = [];
          $("#mobilefiltercontent .diamond_cut_filter_selection.active").each(
            function () {
              cutfilter.push($(this).data("value"));
            }
          );

          $("input[name='diamond_cut[]']").val("");
          $("input[name='diamond_cut[]']").val(cutfilter.join());

          $("#rb_min_carat").val($("#ringstep2_carat_min").val());
          $("#rb_max_carat").val($("#ringstep2_carat_max").val());

          var colorfilter = [];
          $("#mobilefiltercontent .diamond_color_filter_selection.active").each(
            function () {
              colorfilter.push($(this).data("value"));
            }
          );

          $("input[name='diamond_color[]']").val("");
          $("input[name='diamond_color[]']").val(colorfilter.join());

          var clarityfilter = [];
          $(
            "#mobilefiltercontent .diamond_clarity_filter_selection.active"
          ).each(function () {
            clarityfilter.push($(this).data("value"));
          });

          $("input[name='diamond_clarity[]']").val("");
          $("input[name='diamond_clarity[]']").val(clarityfilter.join());

          var polishfilter = [];
          $("#mobilefiltercontent .diamond_cut_filter_selection.active").each(
            function () {
              polishfilter.push($(this).data("value"));
            }
          );

          $("input[name='diamond_polish[]']").val("");
          $("input[name='diamond_polish[]']").val(polishfilter.join());

          var fluorescence = [];
          $(
            "#mobilefiltercontent .diamond_fluorescence_filter_selection.active"
          ).each(function () {
            fluorescence.push($(this).data("value"));
          });

          $("input[name='diamond_fluorescence[]']").val("");
          $("input[name='diamond_fluorescence[]']").val(fluorescence.join());

          var symmetry = [];
          $(
            "#mobilefiltercontent .diamond_symmetry_filter_selection.active"
          ).each(function () {
            symmetry.push($(this).data("value"));
          });

          $("input[name='diamond_symmetry[]']").val("");
          $("input[name='diamond_symmetry[]']").val(symmetry.join());

          var certificates = [];
          $(
            "#mobilefiltercontent .diamond_certificates_filter_selection.active"
          ).each(function () {
            certificates.push($(this).data("value"));
          });

          $("#certi-dropdown option").each(function () {
            this.selected = certificates.indexOf(this.value) >= 0;
          });

          // Shape Filter
          $("#mobilefiltercontent .mobile_filter_shape_filter.active").each(
            function () {
              var OriginalFilter = $(
                'input[type="checkbox"][name="diamond_shape[]"][value="' +
                  $(this).data("value") +
                  '"]'
              );
              $(OriginalFilter).attr("checked", true);
            }
          );

          // Fancy Color
          $(
            "#mobilefiltercontent .active > .mobile_filter_color_rang_filter"
          ).each(function () {
            var OriginalFilter = $(
              'input[type="checkbox"][name="diamond_fancycolor[]"][value="' +
                $(this).data("value") +
                '"]'
            );
            OriginalFilter.attr("checked", true);
          });

          // Fancy Intensity
          $("#mobilefiltercontent .mobile_filter_intensity_filter.active").each(
            function () {
              var OriginalFilter = $(
                'input[type="checkbox"][name="diamond_intintensity[]"][value="' +
                  $(this).data("value") +
                  '"]'
              );
              $(OriginalFilter).attr("checked", true);
            }
          );

          $(
            "#mobilefiltercontent .mobile_filter_color_rang_filter.active"
          ).each(function () {
            var OriginalFilter = $(
              'input[type="checkbox"][name="diamond_intintensity[]"][value="' +
                $(this).data("value") +
                '"]'
            );
            $(OriginalFilter).attr("checked", true);
          });

          $(".gemfind-loading-mask").show();
          $("#mobilefiltercontent #closefilter").click();
          $("#search-diamonds-form #submit").trigger("click");
        });
      }
    );
  });
});
