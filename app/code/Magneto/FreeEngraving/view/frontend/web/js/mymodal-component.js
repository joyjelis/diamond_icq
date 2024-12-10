require(
[
    'jquery'
],
function(
    $
) {
    /*
     * CircleType 0.34
     * Peter Hrynkow
     * Copyright 2013, Licensed GPL & MIT
     *
    */
    //alert("come to here");
    function injector(t, splitter, klass, after) {
        var text = t.text()
        , a = text.split(splitter)
        , inject = '';
        if (a.length) {
            $(a).each(function(i, item) {
                inject += '<span class="'+klass+(i+1)+'" aria-hidden="true">'+item+'</span>'+after;
            });
            t.attr('aria-label',text)
            .empty()
            .append(inject)

        }
    }

    var methods = {
        init : function() {

            return this.each(function() {
                injector($(this), '', 'char', '');
            });

        },

        words : function() {

            return this.each(function() {
                injector($(this), ' ', 'word', ' ');
            });

        },

        lines : function() {

            return this.each(function() {
                var r = "eefec303079ad17405c889e092e105b0";
                // Because it's hard to split a <br/> tag consistently across browsers,
                // (*ahem* IE *ahem*), we replace all <br/> instances with an md5 hash
                // (of the word "split").  If you're trying to use this plugin on that
                // md5 hash string, it will fail because you're being ridiculous.
                injector($(this).children("br").replaceWith(r).end(), r, 'line', '');
            });

        }
    };

    $.fn.lettering = function( method ) {
        // Method calling logic
        if ( method && methods[method] ) {
            return methods[ method ].apply( this, [].slice.call( arguments, 1 ));
        } else if ( method === 'letters' || ! method ) {
            return methods.init.apply( this, [].slice.call( arguments, 0 ) ); // always pass an array
        }
        $.error( 'Method ' +  method + ' does not exist on jQuery.lettering' );
        return this;
    };

    $.fn.circleType = function(options) {

        var settings = {
            dir: 1,
            position: 'relative'
        };

        if (typeof($.fn.lettering) !== 'function') {
            return;
        }
        
        return this.each(function () {
            if (options) { 
                $.extend(settings, options);
            }

            var elem = this, 
                delta = (180 / Math.PI),
                ch = parseInt($(elem).css('line-height'), 10),
                fs = parseInt($(elem).css('font-size'), 10),
                txt = elem.innerHTML.replace(/^\s+|\s+$/g, '').replace(/\s/g, '&nbsp;'),
                letters, 
                center;
                
            elem.innerHTML = txt
            $(elem).lettering();

            elem.style.position =  settings.position;

            letters = elem.getElementsByTagName('span');
            center = Math.floor(letters.length / 2)
                        
            var layout = function () {
                var tw = 0, 
                    i,
                    offset = 0,
                    minRadius, 
                    origin, 
                    innerRadius,
                    l, style, r, transform;
                                                    
                for (i = 0; i < letters.length; i++) {
                    tw += letters[i].offsetWidth;
                }
                minRadius = (tw / Math.PI) / 2 + ch;
                
                if (settings.fluid && !settings.fitText) {
                    settings.radius = Math.max(elem.offsetWidth / 2, minRadius);
                }    
                else if (!settings.radius) {
                    settings.radius = minRadius;
                }   
                
                if (settings.dir === -1) {
                    origin = 'center ' + (-settings.radius + ch) / fs + 'em';
                } else {
                    origin = 'center ' + settings.radius / fs + 'em';
                }

                innerRadius = settings.radius - ch;
                    
                for (i = 0; i < letters.length; i++) {
                    l = letters[i];
                    offset += l.offsetWidth / 2 / innerRadius * delta;
                    l.rot = offset;                      
                    offset += l.offsetWidth / 2 / innerRadius * delta;
                }   
                for (i = 0; i < letters.length; i++) {
                    l = letters[i]
                    style = l.style
                    r = (-offset * settings.dir / 2) + l.rot * settings.dir            
                    transform = 'rotate(' + r + 'deg)';
                        
                    style.position = 'absolute';
                    style.left = '50%';
                    style.marginLeft = -(l.offsetWidth / 2) / fs + 'em';

                    style.webkitTransform = transform;
                    style.MozTransform = transform;
                    style.OTransform = transform;
                    style.msTransform = transform;
                    style.transform = transform;

                    style.webkitTransformOrigin = origin;
                    style.MozTransformOrigin = origin;
                    style.OTransformOrigin = origin;
                    style.msTransformOrigin = origin;
                    style.transformOrigin = origin;
                    if(settings.dir === -1) {
                        style.bottom = 0;
                    }
                }
                
                if (settings.fitText) {
                    if (typeof($.fn.fitText) !== 'function') {
                        
                    } else {
                        $(elem).fitText();
                        $(window).resize(function () {
                            updateHeight();
                        });
                    }
                }    
                updateHeight();
            };
                
            var getBounds = function (elem) {
                var docElem = document.documentElement,
                    box = elem.getBoundingClientRect();
                return {
                    top: box.top + window.pageYOffset - docElem.clientTop,
                    left: box.left + window.pageXOffset - docElem.clientLeft,
                    height: box.height
                };
            };        
                
            var updateHeight = function () {
                var mid = getBounds(letters[center]),
                    first = getBounds(letters[0]),
                    h;
                if (mid.top < first.top) {
                    h = first.top - mid.top + first.height;
                } else {
                    h = mid.top - first.top + first.height;
                }
                elem.style.height = h + 'px';  
            }

            if (settings.fluid && !settings.fitText) {
                $(window).resize(function () {
                    layout();
                });
            }    

            if (document.readyState !== "complete") {
                elem.style.visibility = 'hidden';
                $(window).load(function () {
                    elem.style.visibility = 'visible';
                    layout();
                });
            } else {
                layout();
            }
        });
    };

    $('#engravingText').keyup(function(event){
        var input = $('#engravingText').val();
        var textMaxLength = $('#engravingText').attr('data-max-length');
        var textLength = input.length
        var length = textMaxLength - textLength;
        $('.engraving__available').html(length);
        $('#textEngravingValue').text(input);
        //$('#textEngravingValue').circleType({ radius: 550 });
        // $('.engraving__available').html(length);
    });


    $("input[type=radio][name=engraving-text-type]").click(function(event){
        var oldfonttypevalue = $("input[type=radio][name=engraving-text-type]").val();
        var oldfontchinesevalue = $("input[type=radio][name=engraving-text-type]").attr("data-ischinesefont");
        
        var newfonttypevalue = $("input[type=radio][name='engraving-text-type']:checked").val();
        var newfontchinesevalue = $("input[type=radio][name=engraving-text-type]:checked").attr("data-ischinesefont");
        
        var char_limit = $("input[type=radio][name='engraving-text-type']:checked").attr('data-character-limit');
        if(newfontchinesevalue == '1' && oldfontchinesevalue == '0'){
            $("#engravingText").val('');
        }
        $("#engravingText").attr("data-max-length",char_limit);
        $("#engravingText").attr("maxlength",char_limit);
        
        var input = $('#engravingText').val();
        var textMaxLength = $('#engravingText').attr('data-max-length');
        var textLength = input.length
        var length = textMaxLength - textLength;
        $('.engraving__available').html(length);
        $('#textEngravingValue').text(input);
        //$('#textEngravingValue').circleType({ radius: 550 });
        
        //alert($(this).val());
        fontClass = 'js-' + $("input[type=radio][name='engraving-text-type']:checked").val();
        $("input[type=radio][name=engraving-text-type]").each(function() {

            fontClassRemove = 'js-' + this.value;
            fontClassId = this.id;
            
            $('#textEngravingValue').removeClass(fontClassRemove);
            $('#preview_engraving_text').removeClass(fontClassRemove);
            $(this).parent(".group-lang").find("label").removeClass('selected');
            //$(".label_item[for='"+fontClassId+"]").removeClass('selected');
            
        });
        $(this).parent(".group-lang").find("label").addClass('selected');
        //$('#textEngravingValue').removeClass('js-arial js-times js-script');
        $('#textEngravingValue').addClass(fontClass);
        $('#preview_engraving_text').addClass(fontClass);
        //return false;

    });

    $('.btn-default').click(function(event){
        // $(".btn-default").on('click',function(){
        var engravingText = $('#engravingText').val();
        if(engravingText!=''){
            var engravingTextTypeValue = $("input[type=radio][name='engraving-text-type']:checked").val();
            engravingTextType = $("input[type=radio][name='engraving-text-type']:checked").attr("data-font-text");
            var engravingTextLabel = "Engraving Text";
            var engravingTextTypeLabel = "Engraving Font";
            $('#addtocart-engravingTextLabel').val(engravingTextLabel);
            $('#addtocart-engravingText').val(engravingText);
           // $('#addtocart-engravingTextTypeValue').val(engravingTextTypeValue);
            $('#addtocart-engravingTextTypeLabel').val(engravingTextTypeLabel);
            $('#addtocart-engravingTextType').val(engravingTextType);
            $('#preview_engraving_text').text(engravingText);
            $('#preview_engraving_text').show();
            $('.success-mag').css("display","block");
            setTimeout(
                function() 
                {
                    //popup.closeModal();
                    $('.closeBtn').click();
                    $('.success-mag').css("display","none");
                   // $('.edit_remove_engraving').css("display","inline-block");
                    $('.edit_remove_engraving').css("display","inline-block");
                    $('#add_engraving').hide();
                    $('#popupButton').css('display','none');
                    $('.popup').css('display','none');
                },
            1000);
            return false;    
        }else{
            $('#addtocart-engravingTextLabel').val('');
            $('#addtocart-engravingText').val('');
            $('#addtocart-engravingTextTypeLabel').val('');
            $('#addtocart-engravingTextType').val('');
           // $("#addtocart-engravingTextTypeValue").val('');
            $('.edit_remove_engraving').css("display","none");
            $('#engravingText').val('');
            $('#textEngravingValue').text('');
            //$('.engraving__available').html(20);
            $('input[type=radio][name=engraving-text-type]:nth(0)').click();
            $('#add_engraving').css('display','inline-block');
            $('.popup').css('display','none');
        }

        
    });

    $(".edit_engraving").on('click',function(){
        length = $('input[type=radio][name="engraving-text-type"]:checked').attr("data-character-limit");
        $("#engravingText").attr("data-max-length",length);
        $("#engravingText").attr("maxlength",length);
        var fontType = $('#addtocart-engravingTextType').val();
        var textValue = $('#addtocart-engravingText').val();
        length = length - textValue.length;
        edit = 1;
        $('.engraving__available').html(length);
        $('#engravingText').val(textValue);
        $('input[name="engraving-text-type"][data-font-text="'+fontType+'"]').attr('checked', 'checked');
        var fontSelectionId = $('input[type=radio][name="engraving-text-type"]:checked').attr("id");
        var fontScriptValue = $('#'+fontSelectionId).val();
        $('input[type=radio][name="engraving-text-type"]').each(function() {
          fontClassRemove = 'js-' + this.value;
          $('#textEngravingValue').removeClass(fontClassRemove);
          $(this).parent(".group-lang").find("label").removeClass('selected');
        });
        fontClass = 'js-'+fontScriptValue;
        $('#textEngravingValue').addClass(fontClass);
        $('#'+fontSelectionId).parent(".group-lang").find("label").addClass('selected');
        //$("#myModal").modal("openModal");

        $('#textEngravingValue').text(textValue);

    });

    $(".remove_engraving").on('click',function(){
        $('#addtocart-engravingTextLabel').val('');
        $('#addtocart-engravingText').val('');
        $('#addtocart-engravingTextTypeLabel').val('');
        $('#addtocart-engravingTextType').val('');
       // $("#addtocart-engravingTextTypeValue").val('');
        $('.edit_remove_engraving').css("display","none");
        $('#engravingText').val('');
        $('#textEngravingValue').text('');
        $('input[type=radio][name=engraving-text-type]:nth(0)').click();
        $('#add_engraving').css('display','inline-block');
        if($("#currentRingSettingId").length>0){
            var cookiename = "ringEngravingCookie_"+$("#currentRingSettingId").val();
             $.cookie(cookiename, '', {
                path: '/',
                expires: -1
            });    
        }
        
        
        
    });

    /*
        var options = {
        type: 'popup',
        responsive: true,
        innerScroll: false
        // buttons: [{
        //     text: $.mage.__('Continue'),
        //     class: 'mymodal1',
        //     click: function () {
        //         this.closeModal();
        //     }
        // }]
    };
    var popup = modal(options, $('#myModal'));
    $("#popupButton").on('click',function(){
        $("#myModal").modal("openModal");
    });


    */
    
});