/**
 *  Project: Tab Smart
 *  Description: Tab Smart
 *  @author YouTech Company http://www.smartaddons.com
 */

!function ($, window, undefined) {
    // Create the defaults once
    var pluginName = 'tabsmart', document = window.document, defaults = {
        ajaxUpdate: null,
        control: {
            next: '.tabs-next',
            prev: '.tabs-previous'
        },
        selector: {
            tabs: '.tabs-container ul.tabs li',
            contents: '.tabs-content div.tab-content'
        },
        mouseEnable: true,
        touchEnable: true
    };

    // The actual plugin constructor
    var TabSmart = function (element, options) {
        // static variables

        this.element = $(element);
        this.options = options;

        this.tabs = $(this.options.selector.tabs, this.element);
        this.tabs_width = (function (tabs) {
            var tabs_width = 0;
            for (var i = 0; i < tabs.length; i++) {
                tabs_width += $(tabs[i]).width();
            }
            return tabs_width;
        })(this.tabs);

        this.contents = $(this.options.selector.contents, this.element);

        this.tabs_holder = this.tabs.parent();
        this.tabs_container = this.tabs_holder.parent();
        this.tabs_wrap = this.tabs_container.parent();

        if (this.tabs_container && this.tabs_holder) {
            this.tabs_container.css({
                position: 'relative'
            });
            this.tabs_holder.css({
                position: 'absolute'
            });
        }

        this.state = {};

        if (this.options.control.prev || this.options.control.next) {
            this.options.control.prev
            && $(this.options.control.prev, this.element).click(
                $.proxy(this.prev, this));
            this.options.control.next
            && $(this.options.control.next, this.element).click(
                $.proxy(this.next, this));
        }
        this.init();
        this.resize();
        this.setupTabEvent();
        if (this.options.mouseEnable) {
            this._mouse();
        }
        if (this.options.touchEnable) {
            this._touchwipe();
        }
        $(window).bind('resize.TabSlider', $.proxy(this.resize, this));
    }

    TabSmart.prototype = {
        init: function () {
            var tabactive = this.tabs.filter('.selected'), toleft = null;
            toleft = -tabactive.position().left;
            return this.slidingTo(toleft, 1);
        },

        _touchwipe: function () {
            var that = this;
            (this.tabs_holder).touchSwipeLeft(function () {
                    that.next();
                }
            );
            (this.tabs_holder).touchSwipeRight(function () {
                    that.prev();
                }
            );
        },

        _mouse: function () {
            var that = this;
            (this.tabs_holder).mousewheel(function (event, delta) {
                (delta > 0) ? that.next(event) : that.prev(event);
                return false;
            });
        },

        visPosition: function () {
            return {
                left: -this.tabs_holder.position().left,
                right: this.tabs_container.width() + 2 - this.tabs_holder.position().left
            };
        },
        visTabs: function () {
            var retur = [];
            var vleft = 0 - parseInt(this.tabs_holder.position().left);
            var vright = this.tabs_container.width()
                - parseInt(this.tabs_holder.position().left);
            for (var i = 0; i < this.tabs.length; i++) {
                var tabcenter = $(this.tabs[i]).position().left + $(this.tabs[i]).width() / 2;
                !((tabcenter < vleft) || (tabcenter > vright)) && retur.push(this.tabs[i]);
            }
            return $(retur);
        },
        _enabled: function (e) {
            return e.removeClass('disabled');
        },

        _disabled: function (e) {
            return e.addClass('disabled');
        },

        next: function (e) {
            // if($(e.currentTarget).hasClass('disabled')){
            // return false;
            // }
            this.tabs_holder.stop(true, true);
            var lastvis = this.visTabs().last(), next = lastvis.next();

            if (next.length == 0 && (lastvis.position().left + lastvis.width()) > this.visPosition().right) {
                next = lastvis;
            }

            next.length && this.slidingTo(-next.position().left - next.width() + 2 + this.tabs_container.width());
            e.preventDefault();
        },

        prev: function (e) {
            // if($(e.currentTarget).hasClass('disabled')){
            // return false;
            // }
            this.tabs_holder.stop(true, true);
            var firstvis = this.visTabs().first(), prev = firstvis
                .prev();

            if (prev.length == 0 && (firstvis.position().left + firstvis.width()) < this.visPosition().right) {
                prev = firstvis;
            }

            prev.length && this.slidingTo(-prev.position().left);
            e.preventDefault();
        },

        setState: function (type) {
            if (type) this.state.direction = type;
            this.state.container_width = this.tabs_container.width();
        },

        slidingTo: function (toleft, duration) {
            var that = this, prev = $(this.options.control.prev, that.element), next = $(this.options.control.next, that.element);
            if (!duration) {
                duration = 500;
            }

            if (toleft <= this.tabs_container.width() - this.tabs_width) {
                toleft = this.tabs_container.width() - this.tabs_width + 2;
            }
            if (toleft > 0) {
                toleft = 0;
            }
            if (toleft > this.tabs_holder.position().left) {
                this.setState('prev');
            } else {
                this.setState('next');
            }
            Math.abs(toleft) < 0.9999 ? this._disabled(prev) : this._enabled(prev);
            Math.abs(toleft - (this.tabs_container.width() + 2 - this.tabs_width  )) < 0.9999 ? this._disabled(next) : this._enabled(next);
            this.tabs_holder.animate({
                left: toleft
            }, {
                duration: duration
            });

            return 1;
        },

        setupTabEvent: function () {
            var tabs = this.tabs;
            var contents = this.contents;
            var tabs_holder = this.tabs_holder;
            var that = this;
            this.tabs_holder.stop(true, true);
            $(this.tabs).click(function (e) {
                if ($(this).hasClass('selected')) {
                    return false;
                }
                $(tabs).removeClass('selected');
                $(this).addClass('selected');
                var tab_index = tabs.index(this);

                if (contents[tab_index]) {
                    // content current
                    var ccurrent = $(contents[tab_index]);
                    contents.removeClass('selected');
                    ccurrent.addClass('selected');
                    if (that.options.ajaxUpdate) {
                        (that.options.ajaxUpdate).apply(
                            ccurrent, [ ccurrent,
                                that.options ]);
                    }
                }

                try {
                    if (that.tabs_wrap.width() < that.tabs_width) {
                        var tabcurr = $(this);
                        var tabnext = tabcurr.next();
                        var tabprev = tabcurr.prev();
                        var makeVisible = [];
                        var visibles = that.visTabs();

                        var flag = 0, curr_flag = 2, prev_flag = 1, next_flag = 4;
                        if (tabnext.length && visibles.filter(tabnext).index() < 0) {
                            makeVisible.push(tabnext[0]);
                            flag = flag | next_flag;
                        }

                        if (tabprev.length && visibles.filter(tabprev).index() < 0) {
                            makeVisible.push(tabprev[0]);
                            flag = flag | prev_flag;
                        }

                        if (((flag & prev_flag) && (tabcurr.position().left < that.visPosition().left)) || ((flag & next_flag) && (tabcurr.position().left + tabcurr.width() > that.visPosition().right))) {
                            makeVisible.push(tabcurr[0]);
                            flag = flag | curr_flag;
                        }

                        if (flag) {
                            var toleft = null;
                            var totalwidth = tabcurr.width() + tabnext.width() + tabprev.width();
                            var visiblewidth = (that.visPosition().right - that.visPosition().left);
                            if (totalwidth <= visiblewidth) {
                                if (flag & next_flag) {
                                    var tonext = -tabnext.position().left - tabnext.width() + (that.visPosition().right - that.visPosition().left);
                                    that.slidingTo(tonext);
                                    that.setState();
                                } else if (flag & prev_flag) {
                                    var toprev = -tabprev.position().left;
                                    that.slidingTo(toprev);
                                    that.setState();
                                } else {

                                }
                            } else if (tabcurr.width() <= (visiblewidth)) {
                                if (tabcurr.index() != 0 && tabcurr.index() != that.tabs.length - 1) {
                                    var toleft = parseFloat((that.tabs_container.width() - tabcurr.width()) / 2) - tabcurr.position().left;
                                    that.slidingTo(toleft);
                                }

                                if (tabcurr.index() == that.tabs.length - 1) {
                                    var toleft = -tabcurr.position().left - tabcurr.width() + (that.visPosition().right - that.visPosition().left);
                                    that.slidingTo(toleft);
                                }

                                if (tabcurr.index() == 0) {
                                    var toleft = -tabcurr.position().left;
                                    that.slidingTo(toleft);
                                }
                                that.setState();
                            } else {
                                // noop
                            }
                        } else {
                            if (tabcurr.index() == 0) {
                                var toleft = -tabcurr.position().left;
                                that.slidingTo(toleft);
                                that.setState();
                            }
                            if (tabcurr.index() == that.tabs.length - 1) {
                                var toleft = -tabcurr.position().left - tabcurr.width() + (that.visPosition().right - that.visPosition().left);
                                that.slidingTo(toleft);
                                that.setState();
                            }
                        }
                        that.setState();
                    }
                } catch (e) {

                }
            });
        },

        resize: function (e) {
            this.tabs_holder.stop(true, true);
            this.tabs_width = (function (tabs) {
                var tabs_width = 0;
                for (var i = 0; i < tabs.length; i++) {
                    tabs_width += $(tabs[i]).width();
                }
                return tabs_width;
            })(this.tabs);
            if (this.tabs_wrap.width() < this.tabs_width && this.tabs.length > 1) {
                this.tabs_wrap.addClass('tabs_nav');
            } else {
                this.tabs_wrap.removeClass('tabs_nav');
            }
            if (typeof this.state.container_width == 'undefined') {
                this.setState();
            } else {
                this.init();
            }
        }

    };

    $.fn[pluginName] = function (option) {
        return this.each(function () {
            var $this = $(this), data = $this.data('plugin_' + pluginName);
            var options = typeof option == 'object' ? $.extend({}, defaults, option) : defaults;
            //	var options =$.extend( true, {},defaults, option);
            if (!data)
                $this.data('plugin_' + pluginName, (data = new TabSmart(this, options)));
            if (typeof option == 'number')
                data.to(option);
            else if (typeof option == 'string')
                data[option]();
            else if (options.interval)
                data.cycle();
        });
    };
    $.fn[pluginName].Constructor = TabSmart;

}(jQuery, window);