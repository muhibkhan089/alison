/**
 */

define([
    'jquery',
    'Redgiant_LayeredNavigation/js/action/submit-filter',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'accordion',
    'productListToolbarForm'
], function ($, submitFilterAction, ultil) {
    "use strict";
    var next_page = "";
    var loading = false;
    $.widget('Redgiant.layer', $.mage.accordion, {
        options: {
            openedState: 'active',
            collapsible: true,
            multipleCollapsible: true,
            animate: 200,
            container: '#layer-product-list',
            mobileShopbyElement: '#layered-filter-block .filter-title [data-role=title]',
            collapsibleElement: '[data-role=ln_collapsible]',
            header: '[data-role=ln_title]',
            content: '[data-role=ln_content]',
            isCustomerLoggedIn: false,
            isAjax: true,
            params: [],
            active: [],
            checkboxEl: 'input[type=checkbox]',
            sliderElementPrefix: '#ln_slider_',
            sliderTextElementPrefix: '#ln_slider_text_'
        },

        _create: function () {
            this.initActiveItems();

            this._super();

            this.initProductListUrl();
            this.initObserve();
            this.initSlider();
            this.initWishlistCompare();
        },

        initActiveItems: function () {
            var layerActives = this.options.active,
                actives = [];

            if (typeof window.layerActiveTabs !== 'undefined') {
                layerActives = window.layerActiveTabs;
            }
            if (layerActives.length) {
                this.element.find('.filter-options-item').each(function (index) {
                    if (~$.inArray($(this).attr('attribute'), layerActives)) {
                        actives.push(index);
                    }
                });
            }

            this.options.active = actives;

            return this;
        },

        initProductListUrl: function () {
            var isProcessToolbar = false,
                isAjax = this.options.isAjax;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                if (isProcessToolbar) {
                    return;
                }
                if (isAjax) {
                    isProcessToolbar = true;
                }

                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue === defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);
                if (isAjax) {
                    submitFilterAction(baseUrl + (paramData.length ? '?' + paramData : ''));
                }
                else location.href = baseUrl + (paramData.length ? '?' + paramData : '');
            }
        },

        initObserve: function () {
            var self = this;
            var isAjax = this.options.isAjax;

            var pageElements = $('#layer-product-list').find('.pages a');
            pageElements.each(function () {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (isAjax) {
                        submitFilterAction(link);
                    }
                    else location.href = link;
                })
            });

            var currentElements = this.element.find('.filter-current a, .filter-actions a');
            currentElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (isAjax) {
                        submitFilterAction(link);
                    }
                    else location.href = link;
                });
            });

            var optionElements = this.element.find('.filter-options a');
            optionElements.each(function (index) {
                var el = $(this),
                    link = self.checkUrl(el.prop('href'));
                if (!link) {
                    return;
                }

                el.bind('click', function (e) {
                    if (el.hasClass('swatch-option-link-layered')) {
                        self.selectSwatchOption(el);
                    } else {
                        var checkboxEl = el.siblings(self.options.checkboxEl);
                        checkboxEl.prop('checked', !checkboxEl.prop('checked'));
                    }

                    e.stopPropagation();
                    e.preventDefault();
                    self.ajaxSubmit(link);
                });

                var checkbox = el.siblings(self.options.checkboxEl);
                checkbox.bind('click', function (e) {
                    e.stopPropagation();
                    self.ajaxSubmit(link);
                });
            });

            var swatchElements = this.element.find('.swatch-attribute');
            swatchElements.each(function (index) {
                var el = $(this);
                var attCode = el.attr('attribute-code');
                if (attCode) {
                    if (self.options.params.hasOwnProperty(attCode)) {
                        var attValues = self.options.params[attCode].split(",");
                        var swatchOptions = el.find('.swatch-option');
                        swatchOptions.each(function (option) {
                            var elOption = $(this);
                            if ($.inArray(elOption.attr('option-id'), attValues) !== -1) {
                                elOption.addClass('selected');
                            }
                        });
                    }
                }
            });

            var loadmore = $(self.options.container + ' .infinite-loader .btn-load-more');
            if(loadmore.length > 0) {
                next_page = "";
                $(self.options.container + ' .pages a.next').each(function(){
                    next_page = $(this).attr("href");
                });
                if(next_page == ""){
                    loadmore.hide();
                }else{
                    loadmore.bind('click', function(e) {
                        e.stopPropagation();    
                        if(!loading && next_page){
                            loading = true;
                            self.ajaxInfinite(next_page);
                        }
                    });
                }
            }
            $('#layer-product-list select').styler({
                selectSearch: true,
            });

            requirejs(['jquery', 'weltpixel_quickview' ],
            function   ($, quickview) {
                $(document).ready(function() {
                    $('.weltpixel-quickview').bind('click', function() {
                        var prodUrl = $(this).attr('data-quickview-url');
                        if (prodUrl.length) {
                            quickview.displayContent(prodUrl);
                        }
                    });
                });
            });
        },

        selectSwatchOption: function (el) {
            var childEl = el.find('.swatch-option');
            if (childEl.hasClass('selected')) {
                childEl.removeClass('selected');
            } else {
                childEl.addClass('selected');
            }
        },

        initSlider: function () {
            var self = this,
                slider = this.options.slider;

            for (var code in slider) {
                if (slider.hasOwnProperty(code)) {
                    var sliderConfig = slider[code],
                        sliderElement = self.element.find(this.options.sliderElementPrefix + code),
                        priceFormat = sliderConfig.hasOwnProperty('priceFormat') ? JSON.parse(sliderConfig.priceFormat) : null;

                    if (sliderElement.length) {
                        sliderElement.slider({
                            range: true,
                            min: sliderConfig.minValue,
                            max: sliderConfig.maxValue,
                            values: [sliderConfig.selectedFrom, sliderConfig.selectedTo],
                            slide: function (event, ui) {
                                self.displaySliderText(code, ui.values[0], ui.values[1], priceFormat);
                            },
                            change: function (event, ui) {
                                self.ajaxSubmit(self.getSliderUrl(sliderConfig.ajaxUrl, ui.values[0], ui.values[1]));
                            }
                        });
                    }
                    self.displaySliderText(code, sliderConfig.selectedFrom, sliderConfig.selectedTo, priceFormat);
                }
            }
        },
        displaySliderText: function (code, from, to, format) {
            var textElement = this.element.find(this.options.sliderTextElementPrefix + code);
            if (textElement.length) {
                if (format !== null) {
                    from = this.formatPrice(from, format);
                    to = this.formatPrice(to, format);
                }

                textElement.html(from + ' - ' + to);
            }
        },

        getSliderUrl: function (url, from, to) {
            return url.replace('from-to', from + '-' + to);
        },

        formatPrice: function (value, format) {
            return ultil.formatPrice(value, format);
        },

        ajaxSubmit: function (submitUrl) {
            var isAjax = this.options.isAjax;
            this.element.find(this.options.mobileShopbyElement).trigger('click');

            if (isAjax) {
                return submitFilterAction(submitUrl);
            }
            location.href = submitUrl;
        },
        ajaxInfinite: function (submitUrl) {
            var self = this;
            $.ajax({
                url: submitUrl,
                type: 'get',
                beforeSend: function () {
                    $('.infinite-loader .btn-load-more').hide();
                    $('.ln_overlay').show();
                },
                success: function (res) {
                    loading = false;
                    var $products = $(res.products);
                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if($products){
                        $products.each(function(i,el){
                            if($(el).hasClass('products-grid') || $(el).hasClass('products-list')){
                                var items_grid = $(this).find('ol.products .item');
                                $(self.options.container + ' .products.wrapper ol.products').append(items_grid);
                                $(self.options.container).trigger('contentUpdated');
                            }
                            if($(el).hasClass('toolbar-products')){
                                if($(this).find('.pages a.next').length > 0){
                                    $(self.options.container).find('.pages a.next').attr('href', $(this).find('.pages a.next').attr('href'));
                                }else{
                                    $(self.options.container).find('.pages a.next').remove();
                                }
                            }
                        });
                        if($("form[data-role=tocart-form]").length > 0) {
                            $("form[data-role=tocart-form]").catalogAddToCart();
                        }
                        next_page = "";
                        $(self.options.container + " .pages-items li > a.next").each(function(){
                            next_page = $(this).attr("href");
                        });
                        $('.ln_overlay').hide();
                        $('#layer-product-list select').styler({
                            selectSearch: true,
                        });
                        requirejs(['jquery', 'weltpixel_quickview' ],
                        function   ($, quickview) {
                            $(document).ready(function() {
                                $('.weltpixel-quickview').bind('click', function() {
                                    var prodUrl = $(this).attr('data-quickview-url');
                                    if (prodUrl.length) {
                                        quickview.displayContent(prodUrl);
                                    }
                                });
                            });
                        });
                        if(next_page){
							$('.infinite-loader .btn-load-more').show();
							$(".infinite-loader .btn-load-more").unbind("click").click(function(){
								if(!loading && next_page){
									loading = true;
									self.ajaxInfinite(next_page);
								}
							});
						}
                    }
                } 
            });
        },
        checkUrl: function (url) {
            var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

            return regex.test(url) ? url : null;
        },

        //Handling 'add to wishlist' & 'add to compare' event
        initWishlistCompare: function () {
            var isAjax = this.options.isAjax;
            var isCustomerLoggedIn = this.options.isCustomerLoggedIn,
                elClass = 'a.action.tocompare' + (isCustomerLoggedIn ? ',a.action.towishlist' : '');
            $(elClass).each(function () {
                var el = $(this);
                $(el).bind('click', function (e) {
                    var dataPost = $(el).data('post'),
                        formKey = $('input[name="form_key"]').val();
                    if (formKey) {
                        dataPost.data.form_key = formKey;
                    }

                    var paramData = $.param(dataPost.data),
                        url = dataPost.action + (paramData.length ? '?' + paramData : '');

                    e.stopPropagation();
                    e.preventDefault();
                    if (isAjax) {
                        submitFilterAction(url);
                    }
                    else location.href = url;
                });
            })
        }
    });

    return $.Redgiant.layer;
});
