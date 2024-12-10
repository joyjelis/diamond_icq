require([
    'jquery',
    "mage/calendar"
], function($) {
    $(document).ready(function() {
        $('.loading-mask.gemfind-loading-mask').css('display', 'block');
        setTimeout(function() { $("#search-diamonds-form #submit").trigger("click"); }, 1000);
        $('.loading-mask.gemfind-loading-mask').css('display', 'none');
    });

    function appoitmentTime(start, stop) {
        times = '';
        stopAMPM = stop;
        interval = 30;
        start = start.split(":");
        starth = parseInt(start[0]);
        startm = ((parseInt(start[1])) ? parseInt(start[1]) : '0');
        stop = stop.split(":");
        stopAMPM = stopAMPM.slice(-2);
        stoph = ((stopAMPM.trim() === "PM" && (stop[0] != "12" && stop[0] != "12 PM")) ? (+parseInt(stop[0].replace(":", "")) + (+12)) : parseInt(stop[0]));
        stopm = ((parseInt(stop[1])) ? parseInt(stop[1]) : '0');
        size = stoph > starth ? stoph - starth + 1 : starth - stoph + 1
        hours = [...Array(size).keys()].map(i => i + starth);
        option = '';
        for (hour in hours) {
            for (min = startm; min < 60; min += interval) {
                startm = 0
                if ((hours.slice(-1)[0] === hours[hour]) && (min > stopm)) {
                    break;
                }
                if (hours[hour] > 11 && hours[hour] !== 24) {
                    times = ('0' + (hours[hour] % 12 === 0 ? '12' : hours[hour] % 12)).slice(-2) + ':' + ('0' + min).slice(-2) + " " + 'PM';
                } else {
                    times = ('0' + (hours[hour] % 12 === 0 ? '12' : hours[hour] % 12)).slice(-2) + ':' + ('0' + min).slice(-2) + " " + 'AM';
                }
                option += "<option value='" + times + "'>" + times + "</option>";
            }
        }
        return option;
    }

    jQuery('#avail_date').datepicker({
        minDate: 0,
        onSelect: function(dateText) {
            var curDate = jQuery(this).datepicker('getDate');
            var dayName = jQuery.datepicker.formatDate('DD', curDate);
            if (jQuery(".timing_days").length) {
                var timingDays = jQuery.parseJSON(jQuery(".timing_days.active").html());
                var dayId;
                if (dayName == "Sunday") {
                    dayId = 0;
                } else if (dayName == "Monday") {
                    dayId = 1;
                } else if (dayName == "Tuesday") {
                    dayId = 2;
                } else if (dayName == "Wednesday") {
                    dayId = 3;
                } else if (dayName == "Thursday") {
                    dayId = 4;
                } else if (dayName == "Friday") {
                    dayId = 5;
                } else {
                    dayId = 6;
                }
                jQuery.each(timingDays, function(index, value) {
                    if (dayId == index) {
                        var key = Object.keys(value);
                        if (index == 0) {
                            option = appoitmentTime(value.sundayStart, value.sundayEnd);
                        } else if (index == 1) {
                            option = appoitmentTime(value.mondayStart, value.mondayEnd);
                        } else if (index == 2) {
                            option = appoitmentTime(value.tuesdayStart, value.tuesdayEnd);
                        } else if (index == 3) {
                            option = appoitmentTime(value.wednesdayStart, value.wednesdayEnd);
                        } else if (index == 4) {
                            option = appoitmentTime(value.thursdayStart, value.thursdayEnd);
                        } else if (index == 5) {
                            option = appoitmentTime(value.fridayStart, value.fridayEnd);
                        } else if (index == 6) {
                            option = appoitmentTime(value.saturdayStart, value.saturdayEnd);
                        }
                        jQuery("#appnt_time").html(option);
                    }
                });
                jQuery("#appnt_time").show();
            } else {
                jQuery(".timing_not_avail").show();
                jQuery(".book-slots").prop("disabled", true);
            }


        },
        beforeShowDay: function(d) {
            var day = d.getDay();
            var closeDay = [];
            var myarray = [];
            var timingDays = jQuery.parseJSON(jQuery(".timing_days.active").html());
            jQuery.each(timingDays, function(index, value) {
                var key = Object.keys(value);
                if (index == 0) {
                    if (value.sundayStart == '' || value.sundayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 1) {
                    if (value.mondayStart == '' || value.mondayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 2) {
                    if (value.tuesdayStart == '' || value.tuesdayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 3) {
                    if (value.wednesdayStart == '' || value.wednesdayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 4) {
                    if (value.thursdayStart == '' || value.thursdayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 5) {
                    if (value.fridayStart == '' || value.fridayEnd == '') {
                        closeDay.push(index);
                    }
                } else if (index == 6) {
                    if (value.saturdayStart == '' || value.saturdayEnd == '') {
                        closeDay.push(index);
                    }
                }
            });
            jQuery(".day_status_arr").each(function() {
                myarray.push(jQuery(this).html());
                closeDay.push(parseInt(jQuery(this).html()));
            });
            if (jQuery.inArray(day, closeDay) != -1) {
                return [false, "closed", "Closed on Monday"];
                //return [ true, "", "" ];
            } else {
                return [true, "", ""];
            }
        }
    });

    jQuery("#schview_loc").on('change', function(e) {
        $locationid = jQuery(this).find(':selected').attr("data-locationid");
        if (jQuery('#avail_date').val() != "") {
            jQuery('#avail_date').datepicker('setDate', null);
        }
        jQuery(".timing_days").removeClass("active");
        jQuery(".timing_days").each(function(index) {
            if (jQuery(this).attr("data-location") == $locationid) {
                jQuery(this).addClass("active");
                return false;
            }
        });
    });
});

function formSubmit(e, url, id) {
    require([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/validation'
    ], function($, modal) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'custom-modal',
            buttons: [],
            opened: function($Event) {
                $(".modal-footer").hide();
            }
        };
        var dataFormHint = $('#' + id);
        dataFormHint.mage('validation', {});
        if (dataFormHint.valid()) {
            if (id == 'form-drop-hint') {
                if (($('#g-recaptcha-response').val()) === '') {
                    $('#form-drop-hint #error-msg').css('display', 'block');
                    $('#form-drop-hint #error-msg').css('color', '#df280a');
                    $('#form-drop-hint #error-msg').css('font-size', 13);
                    return false;
                } else {
                    $('#form-drop-hint #error-msg').css('display', 'none');
                }
            }
            if (id == 'form-email-friend') {
                if (($('#g-recaptcha-response-1').val()) === '') {
                    $('#form-email-friend #error-msg').css('display', 'block');
                    $('#form-email-friend #error-msg').css('color', '#df280a');
                    $('#form-email-friend #error-msg').css('font-size', 13);
                    return false;
                } else {
                    $('#form-email-friend #error-msg').css('display', 'none');
                }
            }
            if (id == 'form-request-info') {
                if (($('#g-recaptcha-response-2').val()) === '') {
                    $('#form-request-info #error-msg').css('display', 'block');
                    $('#form-request-info #error-msg').css('color', '#df280a');
                    $('#form-request-info #error-msg').css('font-size', 13);
                    return false;
                } else {
                    $('#form-request-info #error-msg').css('display', 'none');
                }
            }
            if (id == 'form-schedule-view') {
                if (($('#g-recaptcha-response-3').val()) === '') {
                    $('#form-schedule-view #error-msg').css('display', 'block');
                    $('#form-schedule-view #error-msg').css('color', '#df280a');
                    $('#form-schedule-view #error-msg').css('font-size', 13);
                    return false;
                } else {
                    $('#form-schedule-view #error-msg').css('display', 'none');
                }
            }
            $.ajax({
                type: 'POST',
                url: url,
                data: $('#' + id).serialize(),
                beforeSend: function(settings) {
                    $('.loading-mask.gemfind-loading-mask').css('display', 'block');
                },
                success: function(data) {
                    if (data.output.status == 1) {
                        var parId = $('#' + id).parent().attr('id');
                        $('.loading-mask.gemfind-loading-mask').css('display', 'none');
                        $('#popup-modal .note').html(data.output.msg);
                        $('#popup-modal .note').css('display', 'block');
                        $('#popup-modal .note').css('color', 'green');
                        $('#popup-modal .note').css('background', '#c6efd5');
                        var popup = modal(options, $('#popup-modal'));
                        $("#popup-modal").modal("openModal");
                        $('#popup-modal').modal('openModal').on('modalclosed', function() {
                            $('#popup-modal .note').html('');
                            $('#popup-modal .note').css('display', 'none');
                            $(".btn-cencel").trigger("click");
                        });
                        setTimeout(function() {
                            $('#' + parId + ' .note').html('');
                            $('#' + parId + ' .note').css('display', 'none');
                            $('#' + parId + ' .note').css('background', '#fff');
                        }, 5000);
                    } else {
                        var parId = $('#' + id).parent().attr('id');
                        $('#popup-modal .note').html(data.output.msg);
                        $('.loading-mask.gemfind-loading-mask').css('display', 'none');
                        $('#popup-modal .note').css('display', 'block');
                        $('#popup-modal .note').css('color', 'red');
                        $('#popup-modal .note').css('background', '#f7c6c6');
                        var popup = modal(options, $('#popup-modal'));
                        $("#popup-modal").modal("openModal");
                        $('#popup-modal').modal('openModal').on('modalclosed', function() {
                            $('#popup-modal .note').html('');
                            $('#popup-modal .note').css('display', 'none');
                        });
                        setTimeout(function() {
                            $('#' + parId + ' .note').html('');
                            $('#' + parId + ' .note').css('display', 'none');
                            $('#' + parId + ' .note').css('background', '#fff');
                        }, 5000);
                    }
                    document.getElementById(id).reset();
                    return true;
                }
            });
        }
    });
}

function CallSpecification() {
    document.getElementById("diamond-data").style.display = "none";
    document.getElementById("diamond-specification").style.display = "block";
}

function CallDiamondDetail() {
    document.getElementById("diamond-data").style.display = "block";
    document.getElementById("diamond-content-data").style.display = "block";
    document.getElementById("diamond-specification").style.display = "none";
    var el1 = document.getElementById("drop-hint-main");
    if (el1) {
        el1.style.display = "none";
        document.getElementById("form-drop-hint").reset();
    }
    var el2 = document.getElementById("email-friend-main");
    if (el2) {
        el2.style.display = "none";
        document.getElementById("form-email-friend").reset();
    }
    var el3 = document.getElementById("req-info-main");
    if (el3) {
        el3.style.display = "none";
        document.getElementById("form-request-info").reset();
    }
    var el4 = document.getElementById("schedule-view-main");
    if (el4) {
        el4.style.display = "none";
        document.getElementById("form-schedule-view").reset();
    }
}


function CallShowform(e) {
    document.getElementById("diamond-specification").style.display = "none";
    var el1 = document.getElementById("drop-hint-main");
    if (el1) {
        el1.style.display = "none";
        document.getElementById("form-drop-hint").reset();
    }
    var el2 = document.getElementById("email-friend-main");
    if (el2) {
        el2.style.display = "none";
        document.getElementById("form-email-friend").reset();
    }
    var el3 = document.getElementById("req-info-main");
    if (el3) {
        el3.style.display = "none";
        document.getElementById("form-request-info").reset();

    }
    var el4 = document.getElementById("schedule-view-main");
    if (el4) {
        el4.style.display = "none";
        document.getElementById("form-schedule-view").reset();
    }
    document.getElementById("diamond-content-data").style.display = "none";
    var x = e.target.getAttribute("data-target");
    document.getElementById(x).style.display = "block";
    require([
        'jquery',
        'mage/calendar'
    ], function($) {
        $("form input").parent().removeClass('moveUp');
        $("form input").nextAll('span').removeClass('moveUp');
        $('#gift_deadline').datepicker({ minDate: 0 });
        $('#avail_date').datepicker({ minDate: 0 });
    });
}

function Videorun(e) {
    document.getElementById("diamondimg").style.display = "none";
    document.getElementById("diamondvideo").style.display = "block";
    document.getElementById('iframevideo').setAttribute('src', document.getElementById('iframevideo').getAttribute('src'));
}

function Imageswitch1(e) {
    document.getElementById("diamondimg").style.display = "block";
    document.getElementById("diamondvideo").style.display = "none";
    document.getElementById('diamondmainimage').setAttribute('src', document.getElementById('thumbimg1').getAttribute('src'));
}

function Imageswitch2(e) {
    document.getElementById("diamondimg").style.display = "block";
    document.getElementById("diamondvideo").style.display = "none";
    document.getElementById('diamondmainimage').setAttribute('src', document.getElementById('thumbimg2').getAttribute('src'));
}

function Closeform(e) {
    require(['jquery'], function($) {
        var x = e.target.getAttribute("data-target");
        var el1 = document.getElementById("form-drop-hint");
        if (el1) {
            $("form#form-drop-hint :input").parent().removeClass('moveUp');
            $("form#form-drop-hint :input").nextAll('span').removeClass('moveUp');
            $("form#form-drop-hint label").removeClass('is');
            el1.reset();
        }
        var el2 = document.getElementById("form-email-friend");
        if (el2) {
            $("form#form-email-friend :input").parent().removeClass('moveUp');
            $("form#form-email-friend :input").nextAll('span').removeClass('moveUp');
            $("form#form-email-friend label").removeClass('is');
            el2.reset();
        }
        var el3 = document.getElementById("form-request-info");
        if (el3) {
            $("form#form-request-info :input").parent().removeClass('moveUp');
            $("form#form-request-info :input").nextAll('span').removeClass('moveUp');
            $("form#form-request-info label").removeClass('is');
            el3.reset();
        }
        var el4 = document.getElementById("form-schedule-view");
        if (el4) {
            $("form#form-schedule-view :input").parent().removeClass('moveUp');
            $("form#form-schedule-view :input").nextAll('span').removeClass('moveUp');
            $("form#form-schedule-view label").removeClass('is');
            el4.reset();
        }
        document.getElementById(x).style.display = "none";
        document.getElementById("diamond-content-data").style.display = "block";
    });
}

function focusFunction(e) {
    require(['jquery'], function($) {
        var form = $(e).closest('form')[0];
        $("form#" + form.id + " :input").bind("change blur", function() {
            $("form#" + form.id + " :input").parent().addClass('moveUp');
            $("form#" + form.id + " :input").nextAll('span').addClass('moveUp');
        });
        if (!e.value) {
            $(e).parent().addClass('moveUp');
            $(e).nextAll('span:first').addClass('moveUp');
        }
    });
}

function focusoutFunction(e) {
    require(['jquery'], function($) {
        if (!e.value) {
            $(e).parent().removeClass('moveUp');
            $(e).nextAll('span:first').removeClass('moveUp');
        }
    });
}


