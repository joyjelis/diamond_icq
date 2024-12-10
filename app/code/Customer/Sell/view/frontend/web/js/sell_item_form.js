define([
    "jquery",
    "mageUtils",
    "uiComponent",
    "ko",
    "mage/translate",
    "intlTelInput",
    "domReady!",
    "mage/validation",
], function ($, utils, Component, ko, $t) {
    $.validator.addMethod(
        "validate-fileextensions",
        function (v, elm) {
            var extensions = ["jpeg", "jpg", "png", "gif"];
            if (!v) {
                return true;
            }
            with (elm) {
                var ext = value.substring(value.lastIndexOf(".") + 1);
                for (i = 0; i < extensions.length; i++) {
                    if (ext == extensions[i]) {
                        return true;
                    }
                }
            }
            return false;
        },
        $.mage.__("Disallowed file type.")
    );

    return Component.extend({
        step: ko.observable("imageupload"),
        isValid: ko.observable(true),
        imagetype: ko.observable("certificate_images"),
        images: ko.observableArray([]),
        imagecount: ko.observable(0),
        uploadenable: ko.observable(1),
        invalidimages: ko.observable(0),
        maxcount: ko.observable(10),
        closeimage: ko.observable(""),
        newimage: ko.observable(""),
        mobile: ko.observable(),
        utilsScript: ko.observable(""),
        linkimage: ko.observable(""),
        saveData: ko.observable(),
        checkappointmenturl: ko.observable(),
        lastupdatedobj: ko.observable(),
        availablePerferredMethod: ko.observableArray([
            { name: $t("Trade for Cash"), id: 1 },
            { name: $t("Trade for Bigger Diamond"), id: 2 },
            { name: $t("Request Quote"), id: 3 },
        ]),
        preferredmethod: ko.observable(1),
        fname: ko.observable(),
        lname: ko.observable(),
        email: ko.observable(),
        uploadimagetext: ko.observable(
            $t("Upload Certificate and Jewellery Images")
        ),
        checkappointmentbook: ko.observable(true),
        appointmentbooked: ko.observable(false),
        desc: ko.observable(),
        quote_id: ko.observable(),
        date: ko.observable(),
        upload: ko.observable($t("Upload")),

        initialize: function (config) {
            this._super();
            this.jsconfig = JSON.parse(atob(config.jsconfig));
            this.closeimage = config.close_file;
            this.newimage = config.newimage;
            this.linkimage = config.linkimage;
            this.utilsScript = config.utilsScript;
            this.saveData = config.saveData;
            this.checkappointmenturl = config.checkappointmenturl;

            if (this.jsconfig.customer.id) {
                this.fname(this.jsconfig.customer.fname);
                this.lname(this.jsconfig.customer.lname);
                this.email(this.jsconfig.customer.email);
            }

            var self = this;

            this.step.subscribe(function (newValue) {
                $(".showonlystep1only").hide();
                if (newValue == "imageupload") {
                    self.ShowSteps("step1");
                    $(".showonlystep1only").show();
                }

                if (newValue == "appointment") {
                    self.ShowSteps("step2inperson");
                }

                if (newValue == "form") {
                    $("#sell-your-jewellery-section .left-tag").click(
                        function () {
                            if (
                                $(
                                    "#sell-your-jewellery-section #online"
                                ).hasClass("active")
                            ) {
                                self.ShowSteps("step2online");
                            } else {
                                self.ShowSteps("step2inperson");
                            }
                        }
                    );

                    if (
                        $("#sell-your-jewellery-section #online").hasClass(
                            "active"
                        )
                    ) {
                        self.ShowSteps("step2online");
                    } else {
                        self.ShowSteps("step2inperson");
                    }
                }

                if (newValue == "thankyousec") {
                    self.ShowSteps("step3");
                }
            });

            this.imagetype.subscribe(function (newValue) {
                if (newValue == "noimages") {
                    self.uploadenable(0);
                    self.upload($t("Next"));
                } else {
                    self.upload($t("Upload"));
                    self.uploadenable(1);
                }

                if (newValue == "certificate_images") {
                    self.uploadimagetext(
                        $t("Upload Certificate and Jewellery Images")
                    );
                }

                if (newValue == "productimages") {
                    self.uploadimagetext($t("Upload Jewellery Images"));
                }
            });
        },

        validateForm: function (form) {
            return $(form).validation() && $(form).validation("isValid");
        },

        addmoreimages: function () {
            $("#image-uploader-box").click();
        },

        showhideimageserror: function () {
            if (this.imagecount() >= this.maxcount()) {
                return false;
            } else {
                return true;
            }
        },

        removeimage: function (data, event) {
            var context = ko.contextFor(event.target);
            var validFilesCount = context.$parent.imagecount();
            context.$parent.images.remove(data);
            validFilesCount--;
            context.$parent.imagecount(validFilesCount);
            if (validFilesCount == 0) {
                $("#image-uploader-box").val("");
            }
        },

        uploadImage: function (data, evt) {
            let files = evt.target.files,
                validFilesCount = data.imagecount();
            for (var i = 0, f; (f = files[i]); i++) {
                if (data.showhideimageserror() == true) {
                    if (!f.type.match("image.*")) {
                        continue;
                    }

                    validFilesCount++;
                    data.imagecount(validFilesCount);
                    let reader = new FileReader();

                    reader.onload = (function (theFile, i) {
                        return function (e) {
                            var imagedata = {
                                index: utils.uniqueid(),
                                data: e.target.result,
                            };
                            data.images.push(imagedata);
                        };
                    })(f, i);

                    reader.readAsDataURL(f);
                }
            }
        },

        gotoform: function () {
            if (this.appointmentbooked() == true) {
                this.savedata("form");
            } else {
                this.openiframe();
            }
        },

        initTel: function (e) {
            var code = e;
            var self = this;

            var config = {
                nationalMode: false,
                utilsScript: self.utilsScript,
                initialCountry: "hk",
            };

            $(code).attr("placeholder", $t("Mobile No.*"));
            var iti = window.intlTelInput(code, config);

            $(code).on("blur keyup change", function () {
                var iti = window.intlTelInputGlobals.getInstance(this);
                var getCode = iti.getSelectedCountryData().dialCode;
                if (typeof getCode !== "undefined") {
                    var stdcode = parseInt(getCode.length) + 1;

                    if ($(this).val().length >= stdcode) {
                        return true;
                    }

                    if ($(this).val().length <= stdcode) {
                        $(this).val("");
                        $(this).val("+" + getCode);
                        return true;
                    }

                    $(this).focusin().focusout();
                }
            });
        },

        ShowSteps: function (activestep) {
            if ($(window).width() < 768) {
                var steps = ["step1", "step2inperson", "step2online", "step3"];
                for (element of steps) {
                    if (element == activestep) {
                        $("#" + activestep).show();
                    } else {
                        $("#" + element).hide();
                    }
                }
            }
        },

        saveupload: function () {
            if (this.valid()) {
                if (this.imagetype() != "certificate_images") {
                    this.savedata("appointment");
                } else {
                    this.savedata("form");
                }
            }
        },

        goback: function (data) {
            if (data == "appointment") {
                if (this.imagetype() == "certificate_images") {
                    this.step("imageupload");
                } else {
                    this.step(data);
                }
            } else {
                this.step(data);
            }
        },

        submitform: function () {
            if (this.valid()) {
                this.savedata("thankyousec");
            }
        },

        buildUrl: function (url, parameters) {
            var qs = "";
            for (var key in parameters) {
                var value = parameters[key];
                qs +=
                    encodeURIComponent(key) +
                    "=" +
                    encodeURIComponent(value) +
                    "&";
            }
            if (qs.length > 0) {
                qs = qs.substring(0, qs.length - 1); //chop off last "&"
                url = url + "?" + qs;
            }
            return url;
        },

        openiframe: function () {
            var calendarLayer = document.querySelector(
                ".meeting-sec .calendar-layer"
            );
            var node = document.createElement("iframe");
            var xButton = document.createElement("span");
            var overlay = document.createElement("div");
            xButton.classList.add("x-btn");
            overlay.classList.add("overlay");

            var url = window.customersellconfig.koalendar.private_url;
            var quote_field = window.customersellconfig.koalendar.quote_id;
            if (url) {
                var parameters = {};
                parameters[quote_field] = this.lastupdatedobj();

                node.setAttribute("src", this.buildUrl(url, parameters));
                calendarLayer.classList.add("opened-layer");
                calendarLayer.appendChild(node);
                calendarLayer.appendChild(xButton);
                calendarLayer.appendChild(overlay);
            }

            document.querySelector(".x-btn").addEventListener("click", () => {
                var self = this;

                async function checkAppointment() {
                    await self.checkAppointment("form");
                    calendarLayer.classList.remove("opened-layer");
                    calendarLayer.innerHTML = " ";
                }

                checkAppointment();
            });

            document.querySelector(".overlay").addEventListener("click", () => {
                calendarLayer.classList.remove("opened-layer");
                calendarLayer.innerHTML = " ";
            });
        },

        checkAppointment: function (nextstep) {
            var self = this;
            self.checkappointmentbook(false);
            setTimeout(function () {
                $.ajax({
                    showLoader: true,
                    url: self.checkappointmenturl,
                    data: { lastupdatedquote: self.lastupdatedobj() },
                    cache: false,
                    type: "POST",
                    dataType: "json",
                }).done(function (data) {
                    if (data.success) {
                        self.appointmentbooked(true);
                        self.checkappointmentbook(true);
                        self.step(nextstep);
                    } else {
                        self.checkappointmentbook(true);
                    }
                });
            }, 1500);
        },

        savedata: function (nextstep) {
            var category_filter = {
                step: this.step(),
                imagetype: this.imagetype(),
                images: this.images(),
                mobile: this.mobile(),
                fname: this.fname(),
                lname: this.lname(),
                email: this.email(),
                method: this.preferredmethod(),
            };

            if (this.step() != "form") {
                delete category_filter.images;
            }

            var self = this;
            $.ajax({
                showLoader: true,
                url: this.saveData,
                data: {
                    data: btoa(JSON.stringify(category_filter)),
                    lastupdatedquote: this.lastupdatedobj(),
                },
                cache: false,
                type: "POST",
                dataType: "json",
            }).done(function (data) {
                if (data.success) {
                    self.lastupdatedobj(data.data);
                    self.quote_id($t("Quote ID") + ": " + data.data);
                    self.date($t("Date") + ": " + data.date);
                    self.step(nextstep);
                }
            });
        },

        resetform: function () {
            this.valid(true);
            this.imagetype("certificate_images");
            this.images([]);
            this.imagecount(0);
            this.uploadenable(1);
            this.invalidimages(0);
            this.maxcount(10);
            this.mobile("");
            this.lastupdatedobj("");
            this.preferredmethod("Trade for Cash");
            this.fname("");
            this.lname("");
            this.email("");
            this.quote_id("");
            this.date("");
            this.upload("Upload");
            this.checkappointmentbook(true);
            this.desc("");
            this.step("imageupload");
        },

        valid: function () {
            this.invalidimages(0);
            if (this.uploadenable()) {
                if (this.images().length == 0) {
                    this.isValid(false);
                    this.invalidimages(1);
                } else {
                    this.invalidimages(0);
                }
            }

            if (!this.validateForm("#sell-form")) {
                return;
            }
            return true;
        },
    });
});
