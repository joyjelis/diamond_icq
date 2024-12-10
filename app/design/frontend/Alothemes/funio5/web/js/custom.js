/*
 * @Author: Aloteam
 * @Date:   2020-11-16
 * @Last Modified by:   Aloteam
 * @Last Modified time: 2021-01-29 11:28:51
 */

require(["jquery", "Swiper"], function ($, Swiper) {
  $(document).ready(function () {
    // home-page-video
    // toggle top menu
    $(".topmenu-close").on("click", function (event) {
      event.preventDefault();
      $("html").toggleClass("toggle-top-menu");
    });

    // close-cookie
    $(".cookie-close").on("click", function (event) {
      event.preventDefault();
      $(".magepow-gdpr-cookie-notice").addClass("disable");
    });
    setInterval(function () {
      var leftTimeNode = $("body").find("#product-addtocart-button");
      leftTimeNode.addClass("_show");
      setTimeout(function () {
        leftTimeNode.removeClass("_show");
      }, 1000);
    }, 6000);

    // active vertical
    ("use strict");
    $(".page-footer").append('<div class="overlay-bg"></div>');
    $(".block-title-vmagicmenu").on("click", function () {
      if ($(".block-title-vmagicmenu").hasClass("active")) {
        $(".block-title-vmagicmenu").removeClass("active");
        $("html").removeClass("open-nav-vertical");
      } else {
        $(".block-title-vmagicmenu").addClass("active");
        $("html").addClass("open-nav-vertical");
      }
    });

    // close open content in mobile
    $(window).resize(function () {
      var windowWidth = $(window).width();
      if (windowWidth > 767) {
        $(".collapsible .toggle-content").slideDown();
      }
      if (windowWidth == 767) {
        $(".collapsible").removeClass("opened");
        $(".collapsible .toggle-content").slideUp();
      }
    });
    $(".toggle-tab-mobile").on("click", function (event) {
      if ($(window).width() > 767) {
        event.stopPropagation();
      } else {
        $(this).parent().hasClass("opened")
          ? $(this).parent().removeClass("opened")
          : $(this).parent().addClass("opened");
        $(this).next().slideToggle();
      }
    });
  });

  // hover mouse to menu (nav-destop)
  ("use strict");
  $("body").append('<div class="menu-overlay"></div>');
  $(".nav-desktop").mouseenter(function () {
    $("body").addClass("menu-open");

    $(".menu-overlay").fadeIn();
  });
  $(".nav-desktop").mouseleave(function () {
    $("body").removeClass("menu-open");
    $(".menu-overlay").hide();
  });
  $(".menu-overlay").mouseenter(function () {
    $(this).hide();
  });

  // detai page tab
  $(".product.info.detailed").on(
    "click",
    ".tab-scroll-content .tab-items .tab-item",
    function (event) {
      event.preventDefault();
      $(this).parent().find(".tab-item").removeClass("active");
      $(this).addClass("active");
    }
  );

  $(window).scroll(function () {
    if ($("body").hasClass("catalog-product-view")) {
      let item_sticky = $(".product.info.detailed");
      if (item_sticky.length) {
        item_sticky_position = item_sticky.offset().top;
        body = $("html, body");
        item_no_sticky = $(".block-product-bottom");
        item_no_sticky_position = $(".block-product-bottom").offset().top;
        mouse_position = body.scrollTop();

        if (mouse_position < item_no_sticky_position) {
          if (mouse_position >= item_sticky_position) {
            body.addClass("detail-tab-sticky");
          } else {
            body.removeClass("detail-tab-sticky");
          }
        } else {
          body.removeClass("detail-tab-sticky");
        }
      }
    }
  });

  if ($(".magicmenu .level0.home").has(".submenu").length > 0) {
    $(".magicmenu .level0.home").addClass("hasChild");
  }

  // input focus
  $(".input-text").on("click", function () {
    $(this).parents(".field").addClass("active");
  });
  if ($("body").hasClass("catalog-product-view")) {
    var anchor, addReviewBlock;
    var windowHref = window.location.href;
    anchor = windowHref.replace(/^.*?(#|$)/, "");
    if (anchor) {
      addReviewBlock = $("#" + anchor);
      anchor == "review-form"
        ? addReviewBlock.parents("#reviews").show().trigger("click")
        : addReviewBlock.show().trigger("click");
      $("html, body").animate(
        {
          scrollTop: addReviewBlock.offset().top - 250,
        },
        2000
      );
    }
  }

  // onmap toggle
  $(".onmap .toggle-tab").on("click", function () {
    $("html").addClass("open-map");
  });

  $(".onmap .btn-close").on("click", function () {
    $("html").removeClass("open-map");
  });

  $(".block.filter .block-content").on("scroll", function () {
    var h_scroll = $(this).scrollTop();

    if (h_scroll < 50) {
      $("body").removeClass("scroll-filter-content");
    } else {
      $("body").addClass("scroll-filter-content");
    }
  });
  window.getCookie = function (name) {
    var match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
    if (match) return match[2];
  };
  function isEmpty(val) {
    return val === undefined ||
      val == null ||
      val.length <= 0 ||
      val === "undefined"
      ? true
      : false;
  }
  $(window).on("load", function () {
    $(".home_shape_slider", document).on("click", function () {
      try {
        let _shape = $(this).data("shape");
        if (_shape != "") {
          let getFilterData = getCookie("savefiltercookie");
          let decode = window.decodeURIComponent;
          let saveFilters = decode(getFilterData);
          let _parseobj = {};
          if (!isEmpty(saveFilters)) {
            _parseobj = JSON.parse(saveFilters);
            if (_parseobj && _parseobj.hasOwnProperty("shapeList")) {
              // _parseobj.shapeList = (_parseobj.shapeList).split(',');
              // _parseobj.shapeList.push(_shape);
              // _parseobj.shapeList = _parseobj.shapeList.join(',');
              _parseobj.shapeList = _shape;
            } else {
              _parseobj.shapeList = _shape;
            }
          } else {
            _parseobj.shapeList = _shape;
          }
          var expire = new Date();
          expire.setDate(expire.getDate() + 10 * 24 * 60 * 60 * 1000);
          $.cookie("savefiltercookie", JSON.stringify(_parseobj), {
            path: "/",
            expires: expire,
          });
        }
        window.location.href = "ringbuilder/diamond/";
      } catch (error) {
        console.log("Home page video click event error", error);
      }
    });
  });
  var swiper = new Swiper(".shapeSwiper", {
    loop: true,
    autoplay: {
      delay: 8000,
      disableOnInteraction: true,
    },
    slidesPerView: 2,
    spaceBetween: 0,
    pagination: false,
    centeredSlides: true,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      640: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
    },
    /* ON INIT AUTOPLAY THE FIRST VIDEO */
    on: {
      init: function () {
        var currentVideo = $(
          "[data-swiper-slide-index=" + this.realIndex + "]"
        ).find("video");
        currentVideo.trigger("play");
      },
    },
  });

  /* GET ALL VIDEOS */
  var sliderVideos = $(".swiper-slide video");

  /* SWIPER API - Event will be fired after animation to other slide (next or previous) */
  swiper.on("slideChange", function () {
    /* stop all videos  */
    sliderVideos.each(function (index) {
      this.currentTime = 0;
      this.pause();
    });

    /* SWIPER GET CURRENT AND PREV SLIDE (AND The VIDEO INSIDE) */
    var prevVideo = $(`[data-swiper-slide-index="${this.previousIndex}"]`).find(
      "video"
    );
    var currentVideo = $(`[data-swiper-slide-index="${this.realIndex}"]`).find(
      "video"
    );
    currentVideo.trigger("play");
    prevVideo.trigger("stop");
  });
  /* Home Video */
  var homeVideoWrap = $(".home-video-wrap .pagebuilder-video-container");
  var muteString =
    "data:image/svg+xml;base64,PHN2ZyBkYXRhLXYtNDllNDFiYjM9IiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgY2xhc3M9ImgtMTIgdy0xMiI+PHRpdGxlIGRhdGEtdi00OWU0MWJiMz0iIj5Wb2x1bWUgTXV0ZTwvdGl0bGU+PHBhdGggZGF0YS12LTQ5ZTQxYmIzPSIiIGZpbGw9Im5vbmUiIHN0cm9rZT0iY3VycmVudENvbG9yIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgc3Ryb2tlLXdpZHRoPSIzMiIgZD0iTTQxNiA0MzJMNjQgODAiPjwvcGF0aD48cGF0aCBkYXRhLXYtNDllNDFiYjM9IiIgZD0iTTIyNCAxMzYuOTJ2MzMuOGE0IDQgMCAwMDEuMTcgMi44MmwyNCAyNGE0IDQgMCAwMDYuODMtMi44MnYtNzQuMTVhMjQuNTMgMjQuNTMgMCAwMC0xMi42Ny0yMS43MiAyMy45MSAyMy45MSAwIDAwLTI1LjU1IDEuODMgOC4yNyA4LjI3IDAgMDAtLjY2LjUxbC0zMS45NCAyNi4xNWE0IDQgMCAwMC0uMjkgNS45MmwxNy4wNSAxNy4wNmE0IDQgMCAwMDUuMzcuMjZ6TTIyNCAzNzUuMDhsLTc4LjA3LTYzLjkyYTMyIDMyIDAgMDAtMjAuMjgtNy4xNkg2NHYtOTZoNTAuNzJhNCA0IDAgMDAyLjgyLTYuODNsLTI0LTI0YTQgNCAwIDAwLTIuODItMS4xN0g1NmEyNCAyNCAwIDAwLTI0IDI0djExMmEyNCAyNCAwIDAwMjQgMjRoNjkuNzZsOTEuMzYgNzQuOGE4LjI3IDguMjcgMCAwMC42Ni41MSAyMy45MyAyMy45MyAwIDAwMjUuODUgMS42OUEyNC40OSAyNC40OSAwIDAwMjU2IDM5MS40NXYtNTAuMTdhNCA0IDAgMDAtMS4xNy0yLjgybC0yNC0yNGE0IDQgMCAwMC02LjgzIDIuODJ6TTEyNS44MiAzMzZ6TTM1MiAyNTZjMC0yNC41Ni01LjgxLTQ3Ljg4LTE3Ljc1LTcxLjI3YTE2IDE2IDAgMDAtMjguNSAxNC41NEMzMTUuMzQgMjE4LjA2IDMyMCAyMzYuNjIgMzIwIDI1NnEwIDQtLjMxIDguMTNhOCA4IDAgMDAyLjMyIDYuMjVsMTkuNjYgMTkuNjdhNCA0IDAgMDA2Ljc1LTJBMTQ2Ljg5IDE0Ni44OSAwIDAwMzUyIDI1NnpNNDE2IDI1NmMwLTUxLjE5LTEzLjA4LTgzLjg5LTM0LjE4LTEyMC4wNmExNiAxNiAwIDAwLTI3LjY0IDE2LjEyQzM3My4wNyAxODQuNDQgMzg0IDIxMS44MyAzODQgMjU2YzAgMjMuODMtMy4yOSA0Mi44OC05LjM3IDYwLjY1YTggOCAwIDAwMS45IDguMjZsMTYuNzcgMTYuNzZhNCA0IDAgMDA2LjUyLTEuMjdDNDEwLjA5IDMxNS44OCA0MTYgMjg5LjkxIDQxNiAyNTZ6Ij48L3BhdGg+PHBhdGggZGF0YS12LTQ5ZTQxYmIzPSIiIGQ9Ik00ODAgMjU2YzAtNzQuMjYtMjAuMTktMTIxLjExLTUwLjUxLTE2OC42MWExNiAxNiAwIDEwLTI3IDE3LjIyQzQyOS44MiAxNDcuMzggNDQ4IDE4OS41IDQ0OCAyNTZjMCA0Ny40NS04LjkgODIuMTItMjMuNTkgMTEzYTQgNCAwIDAwLjc3IDQuNTVMNDQzIDM5MS4zOWE0IDQgMCAwMDYuNC0xQzQ3MC44OCAzNDguMjIgNDgwIDMwNyA0ODAgMjU2eiI+PC9wYXRoPjwvc3ZnPg==";
  var unmuteString =
    "data:image/svg+xml;base64,PHN2ZyBkYXRhLXYtNDllNDFiYjM9IiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgY2xhc3M9ImgtMTIgdy0xMiI+PHRpdGxlIGRhdGEtdi00OWU0MWJiMz0iIj5Wb2x1bWUgT248L3RpdGxlPjxwYXRoIGRhdGEtdi00OWU0MWJiMz0iIiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2Utd2lkdGg9IjMyIiBkPSJNMTI2IDE5Mkg1NmE4IDggMCAwMC04IDh2MTEyYTggOCAwIDAwOCA4aDY5LjY1YTE1LjkzIDE1LjkzIDAgMDExMC4xNCAzLjU0bDkxLjQ3IDc0Ljg5QTggOCAwIDAwMjQwIDM5MlYxMjBhOCA4IDAgMDAtMTIuNzQtNi40M2wtOTEuNDcgNzQuODlBMTUgMTUgMCAwMTEyNiAxOTJ6TTMyMCAzMjBjOS43NC0xOS4zOCAxNi00MC44NCAxNi02NCAwLTIzLjQ4LTYtNDQuNDItMTYtNjRNMzY4IDM2OGMxOS40OC0zMy45MiAzMi02NC4wNiAzMi0xMTJzLTEyLTc3Ljc0LTMyLTExMk00MTYgNDE2YzMwLTQ2IDQ4LTkxLjQzIDQ4LTE2MHMtMTgtMTEzLTQ4LTE2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48L3BhdGg+PC9zdmc+";
  var homeVideo = $(".home-video-wrap video")[0];
  if (homeVideoWrap.length > 0) {
    homeVideo.muted = true;
    homeVideoWrap.append(
      '<a class="hv-control"><img style="width:3rem" src="' +
        muteString +
        '"/></a>'
    );
    homeVideo.removeAttribute("controls");
    homeVideo.setAttribute("loop", "true");
    homeVideo.setAttribute("webkit-playsinline", "");
    homeVideo.setAttribute("playsinline", "");
    homeVideo.play();
    $(".hv-control").on("click", function () {
      homeVideo.muted = !homeVideo.muted;
      $("img", this).attr("src", homeVideo.muted ? muteString : unmuteString);
    });
  }
  $(window).scroll(fixSidebarOnScroll);
  var $topheader = $(".header-sticker");
  var headerTop = $topheader.position().top;
  var bodyHeight = $("body").outerHeight() - 10;
  function fixSidebarOnScroll() {
    if (window.innerWidth < 1200) {
      var windowScrollTop = $(window).scrollTop();
      if (windowScrollTop >= bodyHeight || windowScrollTop <= headerTop) {
        $topheader.removeClass("header-container-fixed");
      } else if (windowScrollTop >= headerTop) {
        if (!$topheader.hasClass("header-container-fixed")) {
          $topheader.addClass("header-container-fixed");
        }
      }
    }
  }
  //});
});
