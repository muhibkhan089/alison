(function ($) {
    'use strict';
    if ( typeof Berserk == "undefined")
    Berserk = {behaviors: {}, settings: {timeout_delay: 200}};
    // Jquery Counter
    Berserk.behaviors.counter_lib_init = {
        attach: function (context, settings) {
            (function (b) {
                b.fn.counter = function (a) {
                function d(b, e, c) {
                    var f = parseInt(b.html(), 10) + e;
                    f >= c ? (b.html(Math.round(c)), a.callback()) : (b.html(Math.round(f)), setTimeout(function () {
                    d(b, e, c)
                    }, a.step))
                }

                a = b.extend({
                    start: 0,
                    end: 10,
                    time: 10,
                    step: 1E3,
                    callback: function () {
                    }
                }, a);
                var g = b(this);
                b(this).html(Math.round(a.start));
                var h = (a.end - a.start) / (1E3 / a.step * a.time);
                setTimeout(function () {
                    d(g, h, a.end)
                }, a.step)
                }
            })(jQuery);
        }
    };
    
    Berserk.behaviors.tabs_init = {
        attach: function (context, settings) {
          $('.brk-tabs:not(.rendered)', context).addClass('rendered').each(function () {   
                var _ = $(this),
                tabsNav = _.find('.brk-tabs-nav'),
                tab = _.find('.brk-tab'),
                tabItem = _.find('.brk-tab-item'),
                index = _.data('index'),
                index = index ? index : 0;
     
                $(document).ready(function () {
                    tabItem.hide().eq(index).fadeIn();
                    tab.eq(index).addClass('active');
                });
    
                tab.on('click', function () {
                    var $this = $(this);
                    if (!$this.hasClass('active')) {
                        tab.removeClass('active').eq($(this).index()).addClass('active');
                        tabItem.hide().eq($(this).index()).fadeIn();
                    }
                });
            });
        }
    };
    // Fancy box 
  Berserk.behaviors.fancybox_init = {
    attach: function (context, settings) {

      if($('.fancybox:not(.rendered), .fancybox-media:not(.rendered)').length < 1) {
        return;
      }

      // If element is lazyloaded but library still loading, then wait a little
      if (typeof $.fn.fancybox === 'undefined') {
        console.log('Waiting for the fancybox library');
        setTimeout(Berserk.behaviors.fancybox_init.attach, 200, context, settings);
        return;
      }

      var fancybox = $('.fancybox:not(.rendered)', context);
      var fancybox_media = $('.fancybox-media:not(.rendered)', context);

      if (fancybox.length) {
        fancybox.fancybox({
          openEffect: 'elastic',
          closeEffect: 'elastic'
        }).addClass('rendered');
      }

      if (fancybox_media.length) {
        fancybox_media.fancybox({
          openEffect: 'fade',
          closeEffect: 'fade',
          helpers: {
            media: {}
          }
        }).addClass('rendered');
      }
    }
  };
  // Fancy box end
})(jQuery);

(function ($) {
    Berserk.behaviors.media_embeds_init = {
      attach: function (context, settings) {
  
        // To use the Embedded Posts Plugin, or any other Social Plugin, you need
        // to add the Facebook JavaScript SDK to your website.
  
        function facebookSDK(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s);
          js.id = id;
          js.src = 'https://connect.facebook.net/uk_UA/sdk.js#xfbml=1&version=v2.11';
          fjs.parentNode.insertBefore(js, fjs);
        }
  
        //if page has fb-element , init sdk
        var fbElement = $('.fb-video:not(.rendered)',context).addClass("rendered");
        if(fbElement.length){
          facebookSDK(document, 'script', 'facebook-jssdk');
        }
  
        var hostedVideo = $('.brk-hosted-video:not(.rendered)', context).addClass('rendered');
        hostedVideo.each(function (index) {
          var playButton = $(this).find('.brk-hosted-video__btn');
          var video = $(this).find('video');
          var source = $(this).find("source");
          var src = source.attr("data-src");
          var img = $(this).find('.brk-hosted-video__img')
  
          if (!$(this).hasClass('brk-hosted-video_inner')) {
            playButton.attr('href', '#' + 'brk-hosted-video-' + index)
            video.attr('id', 'brk-hosted-video-' + index)
  
            playButton.click(function (event) {
              source.attr("src", src);
              setTimeout(function () {
                video.get(0).play()
              }, 100)
            })
          }
          if ($(this).hasClass('brk-hosted-video_inner')) {
            playButton.click(function (event) {
  
              video.css("display", "block");
              playButton.css("display", "none");
              source.attr("src", src);            
              setTimeout(function () {
                video.get(0).play()
              }, 100)
            })
          }
        })
      }
    }
  })(jQuery);

  (function ($) {
    // =================================================================================
    // Material Card
    // =================================================================================
    Berserk.behaviors.material_card_init = {
      attach: function (context, settings) {
  
        var $mcBtnAction = $(context).parent().find('.mc-btn-action:not(.rendered)').addClass('rendered');
        if ($mcBtnAction.length) {
          $mcBtnAction.click(function () {
            var $this = $(this),
                card = $this.parent('.brk-team-mc'),
                icon = $this.children('i');
            icon.addClass('fa-spin-fast');
  
            if (card.hasClass('mc-active')) {
              card.removeClass('mc-active');
              setTimeout(function () {
                icon
                  .removeClass('fa-arrow-left')
                  .removeClass('fa-spin-fast')
                  .addClass('fa-bars');
              }, 800);
            } else {
              card.addClass('mc-active');
              setTimeout(function () {
                icon
                  .removeClass('fa-bars')
                  .removeClass('fa-spin-fast')
                  .addClass('fa-arrow-left');
              }, 800);
            }
          });
        }
      }
    }
  
  })(jQuery);