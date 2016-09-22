
!function ($) {

    "use strict"; // jshint ;_;


    /* accord PUBLIC CLASS DEFINITION
     * ================================ */

    var Accord = function (element, options) {
        this.$element = $(element)
        this.options = $.extend({}, $.fn.accord.defaults, options)

        if (this.options.parent) {
            this.$parent = $(this.options.parent)
        }
    }

    Accord.prototype = {

        constructor: Accord

    , dimension: function () {
        var hasWidth = this.$element.hasClass('width')
        return hasWidth ? 'width' : 'height'
    }

    , show: function () {
        var dimension
          , scroll
          , actives
          , hasData

        if (this.transitioning || this.$element.hasClass('in')) return

        dimension = this.dimension()
        scroll = $.camelCase(['scroll', dimension].join('-'))

        // find parent element - first parent with class of fsj-accordion-group

        if (typeof (fsj_accord_toggle) != "undefined") {
            var parent = this.$element.parent();
            var count = 20;

            while (count > 0 && parent && parent.length && !parent.hasClass('fsj-accordion-group')) {
                count--;
                parent = parent.parent();
            }

            actives = parent && parent.find('.in');

            if (actives && actives.length) {
                hasData = actives.data('accord')
                //if (hasData && hasData.transitioning) return;
                actives.accord('hide')
                hasData || actives.data('accord', null)
            }
        }

        this.$element[dimension](0)
        this.transition('addClass', $.Event('show'), 'shown')
        $.support.transition && this.$element[dimension](this.$element[0][scroll])

        jQuery('[data-target="#' + this.$element.attr('id') + '"]').addClass('accordion-toggle-open').removeClass('accordion-toggle-closed');
    }

    , hide: function () {
        var dimension
        if (this.transitioning || !this.$element.hasClass('in')) return
        dimension = this.dimension()
        this.reset(this.$element[dimension]())
        this.transition('removeClass', $.Event('hideme'), 'hidden')
        this.$element[dimension](0)
        jQuery('[data-target="#' + this.$element.attr('id') + '"]').addClass('accordion-toggle-closed').removeClass('accordion-toggle-open');
    }

    , reset: function (size) {
        var dimension = this.dimension()

        this.$element
          .removeClass('accord')
          [dimension](size || 'auto')
          [0].offsetWidth

        this.$element[size !== null ? 'addClass' : 'removeClass']('accord')

        return this
    }

    , transition: function (method, startEvent, completeEvent) {
        var that = this
          , complete = function () {
              if (startEvent && startEvent.type == 'show')
                  that.reset()

              that.transitioning = 0
              if (completeEvent) that.$element.trigger(completeEvent)
          }

        if (startEvent) this.$element.trigger(startEvent)

        if (startEvent && startEvent.isDefaultPrevented()) return

        this.transitioning = 1

        this.$element[method]('in')

        $.support.transition && this.$element.hasClass('accord') ?
          this.$element.one($.support.transition.end, complete) :
          complete()
    }

    , toggle: function () {
        this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

    }


    /* accord PLUGIN DEFINITION
     * ========================== */

    var old = $.fn.accord

    $.fn.accord = function (option) {
        return this.each(function () {
            var $this = $(this)
              , data = $this.data('accord')
              , options = $.extend({}, $.fn.accord.defaults, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('accord', (data = new Accord(this, options)))
            if (typeof option == 'string') data[option]()
        })
    }

    $.fn.accord.defaults = {
        toggle: false
    }

    $.fn.accord.Constructor = Accord


    /* accord NO CONFLICT
     * ==================== */

    $.fn.accord.noConflict = function () {
        $.fn.accord = old
        return this
    }

    $.Accord = {
        showAll: function (parent) {
            jQuery(parent).find('.accord').addClass('in').css('height', 'auto');
            jQuery(parent).find('.accordion-toggle-closed').addClass('accordion-toggle-open').removeClass('accordion-toggle-closed');
        }, 

        hideAll: function (parent) {
            jQuery(parent).find('.accord.in').removeClass('in').css('height', '0px');
            jQuery(parent).find('.accordion-toggle-open').addClass('accordion-toggle-closed').removeClass('accordion-toggle-open');
        }
    }

    /* accord DATA-API
     * ================= */

    $(document).on('click.accord.data-api', '[data-toggle=accord]', function (e) {
        var $this = $(this), href
          , target = $this.attr('data-target')
            || e.preventDefault()
            || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
          , option = $(target).data('accord') ? 'toggle' : $this.data()
        $(target).accord(option)
    })

}(window.jQuery);