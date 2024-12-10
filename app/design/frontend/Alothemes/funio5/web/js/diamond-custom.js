require([
	'jquery',
	'Magento_Ui/js/modal/modal'
], function ($, modal) {
	'use strict';
	
	$(document).on('click', '#hide-filter', function(e) {
		e.preventDefault();
		if ($(document).find('#layered-filter-block').length) {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$(document).find('.columns.row').removeClass('hide-filter');
			} else {
				$(this).addClass('active');
				$(document).find('.columns.row').addClass('hide-filter');
			}
		}
	});
	
	
	// typewriter 
	var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
        this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
        this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

        var that = this;
        var delta = 200 - Math.random() * 100;

        if (this.isDeleting) { delta /= 2; }

        if (!this.isDeleting && this.txt === fullTxt) {
        delta = this.period;
        this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
        this.isDeleting = false;
        this.loopNum++;
        delta = 500;
        }

        setTimeout(function() {
        that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i=0; i<elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
              new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
        // INJECT CSS
        var css = document.createElement("style");
        css.type = "text/css";
        css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
        document.body.appendChild(css);
    };


	$(document).ready(function() {
		// faq
		$(".faq_content").slideUp(200);
		$(".faq_question").on("click", function(e) {
			e.preventDefault(); 
			
			if ($(this).hasClass("active")) {
				$(this).removeClass("active");
				$(this).siblings(".faq_content").slideUp(200);
			} else {
				$(".faq_question").removeClass("active");
				$(this).addClass("active");
				$(".faq_content").slideUp(200);
				$(this).siblings(".faq_content").slideDown(200);
			}
		});

		// sticky get quote button
		window.onscroll = function() {scrollFunction()};

		function scrollFunction() {
		  if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
		    $('.sticky_get_quote').addClass("active");
		    $('.sticky_get_quote').removeClass("disactive");
		  } else {
		  	$('.sticky_get_quote').removeClass("active");
		    $('.sticky_get_quote').addClass("disactive");
		  }
		}
	});
});