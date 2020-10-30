/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
document.addEventListener('lazybeforeunveil', (function(){
    var parent = document.createElement('div');
  
    return function(e){
      var set, image, load;
  
      if(e.defaultPrevented || !(set = e.target.getAttribute('data-bgset'))){return;}
      image = document.createElement('img');
      image.setAttribute('sizes', e.target.getAttribute('data-sizes') || (e.target.offsetWidth +'px'));
  
      load = function(){
        var bg = image.currentSrc || image.src;
        if(bg){
          e.target.style.backgroundImage = 'url('+ bg +')';
        }
        if(lazySizes.cfg.clearAttr){
          e.target.removeAttribute('data-bgset');
        }
        image.removeEventListener('load', load);
        parent.removeChild(image);
        image = null;
      };
  
      image.addEventListener('load', 
      );
  
      image.setAttribute('srcset', set);
      parent.appendChild(image);
      if(!window.HTMLPictureElement){
        if(window.respimage){
          respimage({elements: [image]});
        } else if(window.picturefill) {
          picturefill({elements: [image]});
        }
      }
  
      if(image.complete && (image.src || image.currentSrc)){
        load();
      }
    };
  })());

require([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!',
    'lazysizes/lazysizes.min',
    
], function ($, keyboardHandler) {
    'use strict';
    
    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

    $('.page-header .header.links.account-links').clone().appendTo('#store\\.links');

    $('.page-header.type4 .brk-header-popup-menu__open-close').click(function(){
        if ($(this).hasClass("is-active")) {
            $(this).removeClass("is-active");
        } else {
            $(this).addClass("is-active");
        }
    });
    $('.page-header.type4 .menu-area').click(function(){
        $('.page-header.type4 .brk-header-popup-menu__open-close').removeClass("is-active");
    });
    $('.page-header').mouseenter(function(){
        if(!$(this).hasClass('header-hover-delay') && !$(this).hasClass('header-hover')) {
            $(this).addClass('header-hover-delay');
            setTimeout(function(){
                $('.page-header').removeClass('header-hover-delay');
                $('.page-header').addClass('header-hover');
            },600);
        }
    }).mouseleave(function(){
        $('.page-header').removeClass('header-hover-delay');
        $('.page-header').removeClass('header-hover');
        setTimeout(function(){
            $('.page-header').removeClass('header-hover-delay');
            $('.page-header').removeClass('header-hover');
        },600);
    });
    $('.brk-info-menu-open').click(function(event){
        if($(this).hasClass('open-active')) {
            $(this).removeClass('open-active');
            $('.brk-info-menu').removeClass('active');
        } else {
            $(this).addClass('open-active');
            $('.brk-info-menu').addClass('active');
        }
    });
    $('.brk-info-menu .brk-info-menu__close, .brk-info-menu-overlay').click(function(){
        $('.brk-info-menu-open').removeClass('open-active');
        $('.brk-info-menu').removeClass('active');
    });

    $('.btn-gradient').on('mousemove',function(event){
        var parentOffset = $(this).offset();
        var x = event.pageX - parentOffset.left;
        var y = event.pageY - parentOffset.top;

        $(this).attr('style', '--x:' + x + 'px;--y:' + y + 'px')
    });

    $.fn.extend({
        scrollToMe: function(){
            if($(this).length){
                var top = $(this).offset().top - 100;
                $('html,body').animate({scrollTop: top}, 300);
            }
        },
        scrollToJustMe: function(){
            if($(this).length){
                var top = jQuery(this).offset().top;
                $('html,body').animate({scrollTop: top}, 300);
            }
        }
    });
    $(document).ready(function(){
        var windowScroll_t;
        $(window).scroll(function(){
            clearTimeout(windowScroll_t);
            windowScroll_t = setTimeout(function(){
                if(jQuery(this).scrollTop() > 100){
                    $('#totop').fadeIn();
                }else{
                    $('#totop').fadeOut();
                }
            }, 500);
        });
        $('#totop').off("click").on("click",function(){
            $('html, body').animate({scrollTop: 0}, 600);
        });
    });

    keyboardHandler.apply();
});

require([
    'jquery',
    'jquery-formstyler/jquery.formstyler.min'
], function ($) {
    $(document).ready(function(){
        $('.toolbar-products.toolbar select').styler({
            selectSearch: true,
        });
        $('.qty-changer > .qty-inc').off('click').on('click', function(e) {
            var qty = parseInt($(this).parent().parent().find('input').val());
            qty ++;
            $(this).parent().parent().find('input').val(qty);
        });

        $('.qty-changer > .qty-dec').off('click').on('click', function(e) {
            var qty = parseInt($(this).parent().parent().find('input').val());
            if (qty == 0)
                return ;
            qty --;
            $(this).parent().parent().find('input').val(qty);
        });
    });
});

require([
    'jquery',
    'js/jquery.lazyload'
], function ($) {
    $(document).ready(function(){
        $("img.berserk-lazyload:not(.berserk-lazyload-loaded)").lazyload({effect:"fadeIn"});
        if ($('.berserk-lazyload:not(.berserk-lazyload-loaded)').closest('.owl-carousel').length) {
            $('.berserk-lazyload:not(.berserk-lazyload-loaded)').closest('.owl-carousel').on('changed.owl.carousel', function() {
                $(this).find('.berserk-lazyload:not(.berserk-lazyload-loaded)').trigger('appear');
            });
            $('.berserk-lazyload:not(.berserk-lazyload-loaded)').closest('.owl-carousel').on('initialized.owl.carousel', function() {
                $(this).find('.berserk-lazyload:not(.berserk-lazyload-loaded)').trigger('appear');
            });
        }
    });
});