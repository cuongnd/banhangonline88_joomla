(function(){var e=function(e){var t=this,n=e;e.require().script("ui/effect").done(function(){var e=function(){(function(e,t){e.effects.effect.fade=function(t,n){var r=e(this),i=e.effects.setMode(r,t.mode||"toggle");r.animate({opacity:i},{queue:!1,duration:t.duration,easing:t.easing,complete:n})}})(n)};e(),t.resolveWith(e)})};dispatch("ui/effect-fade").containing(e).to("Foundry/2.1 Modules")})();