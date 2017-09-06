(function(window, $){
  var Dreymodal = function(modalElement, options){
    this.modalElement = modalElement;
    this.$modalElement = $(modalElement);
    this.options = options;
    this.modalContent = null;
    this.modalClassName = null;
    $('.'+this.$modalElement.attr("class")).hide();
  }

  Dreymodal.prototype = {
    defaults : {
      minWidth : 250,
      maxWidth : 600,
      overlay : true,
      overlayColor : "#000",
      overlayOpacity : 0.8,
      overlayBlur : true,
      closeButton : true,
      inAnimationTime : 600,
      inAnimationType : "fallIn",
      outAnimationTime : 600,
      outAnimationType : "fallOut",
      allowEscapeKey : true,
      title : null,
      append:false,
      titleColor : "#ffffff",
      titleBackColor : "#222222",
      event_after_close:null,
      event_after_show_modal:null
    },

    open : function(){
      if ($('.'+this.modalClassName+"-wrapper").length){
        $('.'+this.modalClassName).parent().remove()
      }
      this.config = $.extend({}, this.defaults, this.options);
      this.ripContent();
      buildModal.call(this);
      try {
        $('.'+this.modalClassName).dreyanim({
          animationType : this.config.inAnimationType,
          animationTime : this.config.inAnimationTime
        });
        if (this.config.overlayBlur === true){
          // $(':not(.dreyModalWrapper, .dreyModalWrapper *, html, body, script, style)').css({"filter":"blur(3px)"});
          // $('*').not(".dreyModalWrapper, .dreyModalWrapper *, html, body, script, style").css({"filter":"blur(3px)"});
          //Updated console.log($('*').not("."+this.modalClassName+"-wrapper, ."+this.modalClassName+"-wrapper *, html, body, script, style, link, head, title, meta"));
          $('*').not("."+this.modalClassName+"-wrapper, ."+this.modalClassName+"-wrapper *, html, body, script, style, link, head, title, meta").css({"filter":"blur(3px)"});
          // $(':not(.dreyModalWrapper, .dreyModalWrapper *, html, body, script, style)').toggleClass("dreyModalOverlayBlur");
          // $(':not(.dreyModalWrapper *)').toggleClass("dreyModalOverlayBlur");
          // $(':not(.'+this.modalClassName + '-wrapper, .'+this.modalClassName + '-wrapper *, html, body, script, style)').css({"filter":"blur(3px)"})
        }
      } catch(err){
        console.error("The DreyAnim library was not found");
        if (this.config.overlayBlur === true){
          $(':not(.dreyModalWrapper, .dreyModalWrapper *, html, body, script, style)').css({"filter":"blur(3px)"})
        }
      }
      if(this.config.event_after_show_modal instanceof Function){
        this.config.event_after_show_modal();
      }
      initializeEvents.call(this);
    },

    close : function(){
      try {
        $('.'+this.modalClassName).dreyanim({
          animationType : this.config.outAnimationType,
          animationTime : this.config.outAnimationTime
        });

        var _ = this;
        setTimeout(function(){
          _.$modalElement.appendTo(_.config.$parent_modalElement).hide();
          $('.'+_.modalClassName).parent().remove();
          $('*').not("."+this.modalClassName+"-wrapper, ."+this.modalClassName+"-wrapper *, html, body, script, style, link, head, title, meta").css({"filter":""});
          if(_.config.event_after_close instanceof Function){
            _.config.event_after_close();
          }
        },600);

      } catch(err){
        console.error("The DreyAnim library was not found");
        $('.'+this.modalClassName).parent().remove();
        $(':not(.dreyModalWrapper, .dreyModalWrapper *, html, body, script, style)').css({"filter":""})
      }
    },

    ripContent : function(){
      this.modalContent = this.$modalElement[0].innerHTML;
      this.modalClassName = this.$modalElement.attr("class")+"-dreymodal";
    }
  }

  function buildModal(){
    $("<div/>", {
      class: "dreyModalWrapper " + this.modalClassName + "-wrapper"
    }).appendTo('body');

    if(this.config.overlay === true){
      $("<div/>",{
        class: "dreyModalOverlay " + this.modalClassName + "-overlay",
        style: "background-color: " + rgbify(this.config.overlayColor, this.config.overlayOpacity)
      }).appendTo($('.' + this.modalClassName + "-wrapper"))
    };

    $("<div/>", {
      class: "dreyModalMain " + this.modalClassName,
      style: "min-width: " + this.config.minWidth + "px; max-width: " + this.config.maxWidth + "px",
    }).appendTo($('.' + this.modalClassName + "-wrapper"));
    if(this.config.append)
    {
      this.config.$parent_modalElement=this.$modalElement.parent();
      this.$modalElement.show().appendTo($('.'+this.modalClassName));
    }else{
      $('.'+this.modalClassName).html(this.modalContent);

    }

    if (this.config.title != null){
      $("<p/>", {
        class: "dreyModalTitle " + this.modalClassName + "-title",
        style: "background-color: " + this.config.titleBackColor + "; color: " + this.config.titleColor,
        text: this.config.title
      }).prependTo($('.'+this.modalClassName))
    }

    if(this.config.closeButton === true){
      $("<div/>", {
        class: "dreyModalCloseButton",
        text: ""
      }).appendTo($('.'+this.modalClassName))
    }
  }

  function initializeEvents() {
    if(this.config.closeButton){
      var _ = this;
      $('.dreyModalCloseButton').click(function(){
        _.close.call(_)
      })
    }

    $('.dreyModalOverlay').click(function(){
      _.close.call(_)
    })

    if(this.config.allowEscapeKey === true){
      $(document).keyup(function(e){
        if (e.which === 27){
          _.close.call(_);
        }
      })
    }
  }

  function rgbify(hex, opacity){
    var hexTable = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"];
    var decimalTable = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
    hex = hex.substr(1);
    if (hex.length == 3){
      hex = hex.toString().split("");
      hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
      hex = hex.toString();
    }
    var hex1 = decimify(hex.substr(0,2));
    var hex2 = decimify(hex.substr(2,2));
    var hex3 = decimify(hex.substr(4,2));

    var decimal = "rgba(" + hex1 + "," + hex2 + "," + hex3 + "," + opacity + ")";
    return decimal;

    function decimify(hex){
      hex = hex.split("");
      if ($.inArray(hex[0], hexTable) != -1 && $.inArray(hex[1], hexTable) != -1){
         return ((decimalTable[hexTable.indexOf(hex[0].toString().toUpperCase())] * 16) + decimalTable[hexTable.indexOf(hex[1].toString().toUpperCase())]);
      } else {
         console.log("Invalid hex value supplied");
      }

      if (hexTable.includes(hex[0].toString().toUpperCase()) && hexTable.includes(hex[1].toString().toUpperCase())){
        return ((decimalTable[hexTable.indexOf(hex[0].toString().toUpperCase())] * 16) + decimalTable[hexTable.indexOf(hex[1].toString().toUpperCase())]);
      } else {
        console.log("Invalid hex value supplied");
      }

    };
  }

  Dreymodal.defaults = Dreymodal.prototype.defaults;

  $.fn.dreymodal = function(options){
    return this.each(function(){
      new Dreymodal(this, options).open();
    })
  }

  window.Dreymodal = Dreymodal;
})(window, jQuery)
