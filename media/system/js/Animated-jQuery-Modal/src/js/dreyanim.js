(function($){
  $.fn.dreyanim = function(options){
    var settings = $.extend({
      animationType : "zoomIn",
      animationTime : 300,
      animationDelay : 0,
    }, options);
    return this.each(function(){
      $animatable = $(this);
      var allClasses = "dreyAnimZoomIn, dreyAnimZoomOut, dreyAnimFadeIn, dreyAnimFadeOut, dreyAnimSlideInFromUp, dreyAnimSlideInFromDown, dreyAnimSlideOutToLeft, dreyAnimSlideInFromRight, dreyAnimSlideOutToUp, dreyAnimSlideOutToDown, dreyAnimSlideOutToLeft, dreyAnimSlideOutToRight, dreyAnimFlipIn, dreyAnimFlipOut, dreyAnimFlipInAlternate, dreyAnimFlipOutAlternate, dreyAnimFallIn, dreyAnimFallOut, dreyAnimFallInAlternate, dreyAnimFallOutAlternate, dreyAnimRotateIn, dreyAnimRotateOut";
      $animatable.removeClass(allClasses);
      $animatable.css({
        "-webkit-animation-duration" : (settings.animationTime/1000) + "s",
        "-webkit-animation-delay" : (settings.animationDelay/1000) + "s"
      })

      var dreyanimmethods = {
        zoomIn : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimZoomIn");
          setTimeout(function(){$animatable.removeClass("dreyAnimZoomIn")},settings.animationTime)
        },

        zoomOut : function(){
          $animatable.addClass("dreyAnimZoomOut");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimZoomOut")},settings.animationTime)
        },

        fadeIn : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimFadeIn");
          setTimeout(function(){$animatable.removeClass("dreyAnimFadeIn")},settings.animationTime)
        },

        fadeOut : function(){
          $animatable.addClass("dreyAnimFadeOut");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimFadeOut")},settings.animationTime)
        },

        slideInFromUp : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimSlideInFromUp");
          setTimeout(function(){$animatable.removeClass("dreyAnimSlideInFromUp")},settings.animationTime)
        },

        slideInFromDown : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimSlideInFromDown");
          setTimeout(function(){$animatable.removeClass("dreyAnimSlideInFromDown")},settings.animationTime)
        },

        slideInFromLeft : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimSlideInFromLeft");
          setTimeout(function(){$animatable.removeClass("dreyAnimSlideInFromLeft")},settings.animationTime)
        },

        slideInFromRight: function(){
          $animatable.removeClass("hidden").addClass("dreyAnimSlideInFromRight");
          setTimeout(function(){$animatable.removeClass("dreyAnimSlideInFromRight")},settings.animationTime)
        },

        slideOutToUp : function(){
          $animatable.addClass("dreyAnimSlideOutToUp");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimSlideOutToUp")},settings.animationTime)
        },

        slideOutToDown : function(){
          $animatable.addClass("dreyAnimSlideOutToDown");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimSlideOutToDown")},settings.animationTime)
        },

        slideOutToLeft : function(){
          $animatable.addClass("dreyAnimSlideOutToLeft");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimSlideOutToLeft")},settings.animationTime)
        },

        slideOutToRight : function(){
          $animatable.addClass("dreyAnimSlideOutToRight");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimSlideOutToRight")},settings.animationTime)
        },

        flipIn : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimFlipIn");
          setTimeout(function(){$animatable.removeClass("dreyAnimFlipIn")},settings.animationTime)
        },

        flipOut : function(){
          $animatable.addClass("dreyAnimFlipOut");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimFlipOut")},settings.animationTime)
        },

        flipInAlternate : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimFlipInAlternate");
          setTimeout(function(){$animatable.removeClass("dreyAnimFlipInAlternate")},settings.animationTime)
        },

        flipOutAlternate : function(){
          $animatable.addClass("dreyAnimFlipOutAlternate");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimFlipOutAlternate")},settings.animationTime)
        },

        fallIn : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimFallIn");
          setTimeout(function(){$animatable.removeClass("dreyAnimFallIn")},settings.animationTime)
        },

        fallOut : function(){
          $animatable.addClass("dreyAnimFallOut");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimFallOut")},settings.animationTime)
        },

        fallInAlternate : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimFallInAlternate");
          setTimeout(function(){$animatable.removeClass("dreyAnimFallInAlternate")},settings.animationTime)
        },

        fallOutAlternate : function(){
          $animatable.addClass("dreyAnimFallOutAlternate");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimFallOutAlternate")},settings.animationTime)
        },

        rotateIn : function(){
          $animatable.removeClass("hidden").addClass("dreyAnimRotateIn");
          setTimeout(function(){$animatable.removeClass("dreyAnimRotateIn")},settings.animationTime)
        },

        rotateOut : function(){
          $animatable.addClass("dreyAnimRotateOut");
          setTimeout(function(){$animatable.addClass("hidden").removeClass("dreyAnimRotateOut")},settings.animationTime)
        },
      }

      dreyanimmethods[settings.animationType]();

      setTimeout(function(){
        $animatable.css({
          "-webkit-animation-duration" : "",
          "-webkit-animation-delay" : "",
        })
      }, settings.animationTime)
    })
  }
}(jQuery))
