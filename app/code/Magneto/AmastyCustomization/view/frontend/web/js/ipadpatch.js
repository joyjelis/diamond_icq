define([
    'jquery'
    ], function ($) {
        'use strict';

        return function (widget) {

            $.widget('mage.amShopbyApplyFilters', widget, {
                renderShowButton: function (e, element) {
                    var button = $('.' + $.mage.amShopbyApplyFilters.prototype.showButton),
                    buttonHeight = button.outerHeight();

                    if ($.mage.amShopbyApplyFilters.prototype.isMobile) {
                        $('#narrow-by-list .filter-options-item:last-child').css({
                            "padding-bottom": buttonHeight,
                            "margin-bottom": "15px"
                        });
                        $($.mage.amShopbyApplyFilters.prototype.showButtonContainer).addClass('visible');
                        $('.' + $.mage.amShopbyApplyFilters.prototype.showButton + ' > .am-items').html('').addClass('-loading');

                        return;
                    }

                    var sideBar = $('.sidebar-main .filter-options'),
                    leftPosition = sideBar.length ? sideBar : $('[data-am-js="shopby-container"]'),
                    priceElement = '.am-filter-items-attr_price',
                    orientation,
                    elementType,
                    posTop,
                    posLeft,
                    oneColumn = $('body').hasClass('page-layout-1column'),
                    rightSidebar = $('body').hasClass('page-layout-2columns-right'),
                            marginWidth = 30, // margin for button:before
                            marginHeight = 10, // margin height
                            $element = $(element),
                            oneColumnWrapper = $($.mage.amShopbyApplyFilters.prototype.oneColumnFilterWrapper),
                            topFiltersWrapper = $('.amasty-catalog-topnav'),
                            self = this,
                            elementPosition = element.offset ? element.offset() : [];

                            $(self.showButtonContainer).css('width', 'inherit');
                        // get orientation
                        if ($element.parents('.amasty-catalog-topnav').length || oneColumn) {
                            button.removeClass().addClass($.mage.amShopbyApplyFilters.prototype.showButton + ' -horizontal');
                            orientation = 0;
                        } else {
                            if (rightSidebar) {
                                button.removeClass().addClass($.mage.amShopbyApplyFilters.prototype.showButton + ' -vertical-right');
                            } else {
                                button.removeClass().addClass($.mage.amShopbyApplyFilters.prototype.showButton + ' -vertical');
                            }
                            orientation = 1;
                        }

                        //get position
                        if (orientation) {
                            if (typeof elementPosition !== 'undefined'){
                                elementPosition['top'] = elementPosition ? elementPosition['top'] : 0;
                                posTop = (e.pageY ? e.pageY : elementPosition['top']) - buttonHeight / 2;
                                rightSidebar ?
                                posLeft = leftPosition.offset().left - button.outerWidth() - marginWidth :
                                posLeft = leftPosition.offset().left + leftPosition.outerWidth() + marginWidth;
                            }
                        } else {
                            if (oneColumn) {
                                oneColumnWrapper.length ?
                                posTop = oneColumnWrapper.offset().top - buttonHeight - marginHeight :
                                console.warn('Improved Layered Navigation: You do not have default selector for filters in one-column design.');
                            } else {
                                posTop = topFiltersWrapper.offset().top - buttonHeight - marginHeight;
                            }

                            elementPosition['left'] = elementPosition ? elementPosition['left'] : 0;
                            posLeft = (e.pageX ? e.pageX : elementPosition['left']) - button.outerWidth() / 2;
                        }

                        elementType = self.getShowButtonType($element);

                        switch (elementType) {
                            case 'dropdown':
                            if (orientation) {
                                posTop = $element.offset().top - buttonHeight / 2;
                            } else {
                                posLeft = $element.offset().left - marginHeight;
                            }
                            break;
                            case 'flyout':
                            if (orientation) {
                                rightSidebar ?
                                posLeft = $element.parents('.item').offset().left - button.outerWidth() - marginWidth :
                                posLeft = $element.parents('.item').offset().left
                                + $element.parents('.item').outerWidth() + marginWidth;
                            }
                            break;
                            case 'price':
                            if (orientation) {
                                posTop = $(priceElement).not('.am-top-filters').offset().top - buttonHeight / 2 + marginHeight;
                            } else {
                                posLeft = $(priceElement).offset().left - marginHeight;
                            }
                            break;
                            case 'decimal':
                            if (orientation) {
                                posTop = $element.offset().top - buttonHeight / 2 + marginHeight;
                            } else {
                                posLeft = $element.offset().left - marginHeight;
                            }
                            break;
                            case 'price-widget':
                            if (orientation) {
                                posTop = $element.offset().top - buttonHeight / 2 + marginHeight;
                            } else {
                                posLeft = $element.offset().left - marginHeight;
                            }
                            break;
                        }

                        self.setShowButton(posTop, posLeft, leftPosition);
                    },
                });

return $.mage.amShopbyApplyFilters;
}
});