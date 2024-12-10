define(["jquery",'Magento_Ui/js/lib/view/utils/dom-observer',"domReady!"], function($){
    "use strict";

    return function (config) {
        domObserver = require('Magento_Ui/js/lib/view/utils/dom-observer');
        jQuery(document).ready(function($) {
            var filter = {
                'currentview': config.default_view,
                'p': config.default_page,
                // 'limit': config.default_limit,
                'sortby': config.default_sort
            };

            let updatefilter = function(){
                var url = new URL(window.location.href);
                var page = url.searchParams.get("p");
                if(page != null){
                    filter.p = page;
                }

                var currentview = url.searchParams.get("currentview");
                if(currentview != null){
                    filter.currentview = currentview;
                    $('.changeview').removeClass('active');
                    $("[data-view="+ currentview +"]").addClass('active');
                }

                var sortby = url.searchParams.get("sortby");
                if(sortby != null){
                    filter.sortby = sortby;
                    $('#quote-sorting-filter').val(sortby);
                }
            };

            let buildUrl = function(parameters){
                var url = new URL(config.reset_url);
                url = url.pathname
                var qs = "";
                for (var key in parameters) {
                    var value = parameters[key];
                    qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
                }
                if (qs.length > 0) {
                    qs = qs.substring(0, qs.length - 1); //chop off last "&"
                    url = url + "?" + qs;
                }

                return url;
            };

            let pager = function(elem){
                $(elem).click(function(){
                    var url = new URL(jQuery(this).attr('href'));
                    var page = url.searchParams.get("p");
                    if(page == null){
                        filter.p = config.default_page;
                        callajax(filter);
                        return false;
                    }

                    filter.p = page;
                    callajax(filter);
                    return false;
                });
            }

            let callajax = function(filter){
                $.ajax({
                    showLoader: true,
                    url: config.filter_url,
                    data: filter,
                    cache: false,
                    type: "GET",
                    dataType: 'json'
                }).done(function(data) {
                    if(data.success){
                        var url = buildUrl(filter);
                        $('.sell-diamond-main-div').empty();
                        $('.sell-diamond-main-div').append(data.html.block);
                        $('#sell-diamond-pager').empty();
                        $('#sell-diamond-pager').append(data.html.pager);
                        window.history.pushState("sell-diamond", "sell-diamond", url);
                        pager('.pager .item a');
                    }
                });
            };

            domObserver.get('.changeview', function(elem){
                $(elem).click(function(){
                    if(filter.currentview != $(this).data('view')){
                        filter.currentview = $(this).data('view');
                        callajax(filter);
                        $('.changeview').removeClass('active');
                        $(this).addClass('active');
                    }
                });
            });

            domObserver.get('.pager .item a', function(elem){
                pager(elem);
            });

            domObserver.get('#quote-sorting-filter', function(elem){
                $(elem).change(function(){
                    if(filter.sortby != $(this).val()){
                        filter.sortby = $(this).val();
                        callajax(filter);
                    }
                });
            });

            window.addEventListener('locationchange', function(){
                
            });

            updatefilter();
        });
    }
});