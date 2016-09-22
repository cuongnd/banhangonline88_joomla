/*
* jQuery Textarea.Autoresize
* https://github.com/AndrewDryga/jQuery.Textarea.Autoresize
*
* This plugin resizes textarea height to match it's content height.
*
* Usage:
* <code>jQuery('textarea').autoresize(params_object);</code>
*
* Params can also be passed via data-api:
* <code><textarea data-default-height="min" data-animated="false"></textarea></code>
*
* Available params:
* - minHeight: minimal height for textarea, default is textarea height.
* - maxHeight: maximal height for textarea, default is false (unlimited). If textarea content is bigger than this value, then scrollbar appears.
* - defaultHeight: height that will be set to textarea when it loose focus. Default if false (turned off).
* - animated: animation for focus loose/restore (works only width non-false defaultHeight values). Default is true.
* - onResize: callback function, called each time plugin resizes textarea.
*
* Author: Andrew Dryga <andrew@dryga.com> <http://dryga.com>
* License: MIT
*/

(function (jQuery) {
    "use strict";

    var mirrorred_styles = [
    'padding',
    'paddingTop',
    'paddingBottom',
    'paddingRight',
    'paddingLeft',
    'border',
    'borderTop',
    'borderBottom',
    'borderRight',
    'borderLeft',
    'borderTopWidth',
    'borderRightWidth',
    'borderBottomWidth',
    'borderLeftWidth',
    'fontFamily',
    'fontSize',
    'lineHeight',
    'box-sizing',
  ];

    var Obj = function (element, params) {
        this.$element = jQuery(element);
        this.$element.data('autoresize-api', this);
        params = params || {};

        this.params = jQuery.extend({
            minHeight: this.$element.height(),
            maxHeight: ~ ~parseInt(this.$element.css('max-height'), 10),
            defaultHeight: false,
            animated: false,
            heightCompensation: this.$element.outerHeight() - this.$element.height(),
            onResize: jQuery.noop,
            extraLine: false
        }, this.$element.data(), params);

        if (this.params.defaultHeight && this.params.defaultHeight == 'min') {
            this.params.defaultHeight = this.params.minHeight;
        }

        this.init();
    };

    Obj.prototype = {
        init: function () {
            var $self = this;
            var $element = $self.$element;
            var element = $element.get(0);

            if ($element.prop("tagName").toLowerCase() !== 'textarea') {
                console.error('jQuery.Textarea.Autoresize works only on textarea tags, skipping...');
                console.log('Selected element is: ', $element);
                return;
            }

            $element.addClass('autoresize');
            if ($self.params.maxHeight == false) {
                $element.css('overflow', 'hidden');
            }

            $element.on('focus.autoresize', function () {
                $self.setHeight($self.getContentHeight(), $self.params.animated);
            });

            if ($self.params.defaultHeight) {
                $element.on('focusout.autoresize', function () {
                    $self.setHeight($self.params.defaultHeight, $self.params.animated);
                });
            }

            $element.on('keydown.autoresize', function () { //cut paste drop
                setTimeout(function () {
                    $self.setHeight($self.getContentHeight());
                }, 0);
            });

            $element.on('resize.autoresize', function () {
                setTimeout(function () {
                    $self.getMirror().width($element.width());
                    $self.setHeight($self.getContentHeight());
                }, 0);
            });

            $self.setHeight(this.$element[0].scrollHeight);
        },

        destroy: function () {
            this.getMirror().remove();
            this.$element.removeData('autoresize-api');
            this.$element.off('.autoresize');
            this.$element.removeClass('autoresize');
            this.$element.css('overflow', '');
            this.$element.css('height', '');
            this.$element.removeAttr('style');
        },

        getMirror: function () {
            var mirror_tag = this.$element.nextAll('.autoresize-mirror').first();
            if (!mirror_tag.length) {
                mirror_tag = jQuery('<div/>').addClass('autoresize-mirror');
                for (var i = 0; i < mirrorred_styles.length; i++) {
                    mirror_tag.css(mirrorred_styles[i], this.$element.css(mirrorred_styles[i]));
                }
                mirror_tag.width(this.$element.width());

                this.$element.after(mirror_tag);
            }

            var trailing = '<br />';

            if (this.params.extraLine)
                trailing = '<br />' + '<br />';

            mirror_tag.html(this.$element.val().replace(/&/g, '&amp;').
                                     replace(/"/g, '&quot;').
                                     replace(/'/g, '&#39;').
                                     replace(/</g, '&lt;').
                                     replace(/>/g, '&gt;').
                                     replace(/\n/g, '<br />') + trailing);

            return mirror_tag;
        },

        getContentHeight: function () {
            return this.limitValue(this.getMirror().height(), this.params.minHeight, this.params.maxHeight);
        },

        setHeight: function (height, animated) {
            if (animated) {
                this.$element.stop(true).animate({ height: height + this.params.heightCompensation + 'px' }, 'slow');
            } else {
                this.$element.height(height);
            }
            this.params.onResize(this.$element, { height: height });
        },

        limitValue: function (value, min, max) {
            value = (min == false || value > min) ? value : min;
            value = (max == false || value < max) ? value : max;

            return value;
        }
    };

    jQuery.fn.autoresize = function (params) {
        return this.each(function () {
            if (params == 'destroy') {
                var api;
                if (api = jQuery(this).data('autoresize-api')) {
                    api.destroy();
                } else {
                    console.error("Can't destroy autoresize api, it's not initialized.");
                }
            } else {
                new Obj(this, params);
            }
        });
    };

    jQuery(function () {
        // Instert plugin styles
        jQuery('html > head').append(jQuery('<style>.autoresize-mirror { display: none; word-wrap: break-word; white-space: pre-wrap; } .autoresize { resize: none; }</style>'));
    });
})(jQuery);
