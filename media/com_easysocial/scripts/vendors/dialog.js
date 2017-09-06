(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var jQuery = $; 
var exports = function() {

var dialogHtml = '<div id="es" class="es-dialog"> <div class="es-dialog-modal"> <div class="es-dialog-header"> <div class="es-dialog-header__grid"> <div class="es-dialog-header__cell"><span class="es-dialog-title"></span></div> <div class="es-dialog-close-button"><i class="fa fa-close"></i></div> </div> </div> <div class="es-dialog-body"> <div class="es-dialog-container"> <div class="es-dialog-content"></div> <div class="o-loader"></div> <div class="o-empty"> <div class="o-empty__content"><i class="o-empty__icon fa fa-exclamation-triangle"></i> <div class="o-empty__text"><span class="es-dialog-error-message"></span></div> </div> </div> </div> </div> <div class="es-dialog-footer"> <div class=""> <div class="es-dialog-footer-content"></div> </div> </div> </div></div>';
var dialog_ = ".es-dialog";
var dialogModal_ = ".es-dialog-modal";
var dialogContent_ = ".es-dialog-content";
var dialogHeader_ = ".es-dialog-header";
var dialogFooter_ = ".es-dialog-footer";
var dialogFooterContent_ = ".es-dialog-footer-content";
var dialogCloseButton_ = ".es-dialog-close-button";
var dialogTitle_ = ".es-dialog-title";
var dialogErrorMessage_ = ".es-dialog-error-message";

var isFailed = "is-failed";
var isLoading = "is-loading";
var rxBraces = /\{|\}/gi;

var self = EasySocial.dialog = function(options) {

    // For places calling EasySocial.dialog().close();
    if (options === undefined) {
    	return self;
    }

    // Normalize options
    if ($.isString(options)) {
        options = {content: options};
    }

    var method = self.open;

    method.apply(self, [options]);

    return self;
}

$.extend(self, {

    defaultOptions: {
        title: "",
        content: "",
        buttons: "",
        classname: "",
        width: "auto",
        height: "auto",
        escapeKey: true
    },

    open: function(options) {

        // Get dialog
        var dialog = $(dialog_);
        if (dialog.length < 1) {
            dialog = $(dialogHtml).appendTo("body");
        }

        // Normalize options
        var options = $.extend({}, self.defaultOptions, options);

        // Set title
        var dialogTitle = $(dialogTitle_);
        dialogTitle.text(options.title);

        // Set buttons
        var dialogFooterContent = $(dialogFooterContent_);
        dialogFooterContent.html(options.buttons);
        dialog.toggleClass("has-footer", !!options.buttons)

        // Set bindings
        self.setBindings(options);

        // Set content
        var dialogContent = $(dialogContent_).empty();
        var content = options.content;
        var contentType = self.getContentType(content);
        dialog.switchClass("type-" + contentType)

        if (window.es.mobile) {
            dialog.addClass('is-mobile');
        }
        
        // Set width & height
        var dialogModal = $(dialogModal_);
        var dialogWidth = options.width;
        var dialogHeight = options.height;

        if ($.isNumeric(dialogHeight)) {
            var dialogHeader = $(dialogHeader_);
            var dialogFooter = $(dialogFooter_);
            dialogHeight += dialogHeader.height() + dialogFooter.height();
        }

        dialogModal.css({
            width: dialogWidth,
            height: dialogHeight
        });

        dialog.addClassAfter("active");

        // HTML
        switch (contentType) {

            case "html":
                dialogContent.html(content);
                dialog.trigger('init');
                break;

            case "iframe":
                var iframe = $("<iframe>");
                var iframeUrl = content;
                iframe
                    .appendTo(dialogContent)
                    .one("load", function(){

                    })
                    .attr("src", iframeUrl);
                break;

            case "deferred":
                dialog.switchClass(isLoading);
                content
                    .done(function(content) {
                        // Options
                        if ($.isPlainObject(content)) {
                            self.reopen($.extend(true, options, content));
                        // Content
                        } else if ($.isString(content)) {
                            options.content = content;
                            self.reopen(options);
                        // Unknown
                        } else {
                            dialog.switchClass(isFailed);
                        }
                    })
                    .fail(function(exception){
                        dialog.switchClass(isFailed);

                        var dialogErrorMessage = $(dialogErrorMessage_);

                        // Error message
                        if ($.isString(exception)) {
                            dialogErrorMessage.html(exception);
                        }

                        // Exception object
                        if ($.isPlainObject(exception) && exception.message) {
                            dialogErrorMessage.html(exception.message);
                        }
                    });
                return;
                break;

            case "dialog":
                var xmlOptions = self.parseXMLOptions(content);
                self.open($.extend(true, options, xmlOptions));
                return;
                break;
        }
    },

    reopen: function(options) {
        self.close();
        self.open(options);
    },

    close: function() {

        // Unset bindings
        self.unsetBindings();

        // Remove dialog
        var dialog = $(dialog_);
        dialog.remove();
    },

    getContentType: function(content) {

        if (/<dialog>(.*?)/.test(content)) {
            return "dialog";
        }

        if ($.isUrl(content)) {
            return "iframe";
        }

        if ($.isDeferred(content)) {
            return "deferred";
        }

        return "html";
    },

    parseXMLOptions: function(xml) {

        var xmlOptions = $.buildHTML(xml);
        var newOptions = {};

        $.each(xmlOptions.children(), function(i, node){

            var node = $(node);
            var key  = $.String.camelize(this.nodeName.toLowerCase());
            var val  = node.html();
            var type = node.attr("type");

            switch (type) {
                case "json":
                    try {
                        val = $.parseJSON(val);
                    } catch(e) {};
                    break;

                case "javascript":
                    try {
                        val = eval('(function($){ return ' + $.trim(val) + ' })(' + $.globalNamespace + ')');
                    } catch(e) {};
                    break;

                case "text":
                    val = node.text();
                    break;
            }

            // Automatically convert numerical values
            if ($.isNumeric(val)) {
            	val = parseFloat(val);
            }

            newOptions[key] = val;
        });

        return newOptions;
    },

    bindings: {},

    setBindings: function(options) {

        // Remove previous bindings
        self.unsetBindings();

        // Create new bindings
        var selectors = options.selectors;
        var bindings  = options.bindings;
        var dialog = $(dialog_);

        if (selectors && bindings) {

            // Simulate a controller instance
            var controller = {parent: self};
            
            $.each(selectors, function(element, selector){

                var element = element.replace(rxBraces, "");

                // Create selector fn
                var selectorFn = controller[element] = function() {
                    return dialog.find(selector);
                };
                selectorFn.selector = selector;
            });

            // Simulate mvc here
            controller["parent"] = self;
            controller["element"] = dialog;
            controller["self"] = function() {
									return dialog;
								};

            // Make the caller available to the dialog if a caller is provided
            if (options.caller) {
                controller["caller"] = options.caller;
            }
			// controller["self"].selector = dialog.selector;

            $.each(bindings, function(binder, eventHandler){

                // Get element and event name
                var parts = binder.split(" ");
                var element = parts[0].replace(rxBraces, "");
                var eventName = parts[1] + ".es.dialog";

                // Get selector fn
                var selectorFn = controller[element];

                // Custom way of simulating a controller's init method
                if (element == 'init') {
                	dialog.on(element, function() {

                        var args = [this].concat(arguments);

                		eventHandler.apply(controller, args);
                	});
                }

                // No binding if selector fn is not found
                if (!selectorFn) {

                	// These items could be 
                	controller[element] = eventHandler;

                	return;
                }

                // Bind event handler
                var selector = selectorFn.selector;

                dialog.on(eventName, selector, function(){
                	// Convert the argument object into an array first.
                	var args = [].slice.call(arguments);
                	
                    eventHandler.apply(controller, [$(this)].concat(args));
                });

                // Add to bindings
                self.bindings[eventName] = eventHandler;
            });
        }

        if (options.escapeKey) {
            $(document).on("keydown.es.dialog", function(event){
                if (event.keyCode==27) {
                    self.close();
                }
            });
        }
    },

    setMessage: function(response) {
        var element = $('<div class="o-alert o-alert--' + response.type + '"><button type="button" class="close" data-bs-dismiss="alert">×</button></div>');
        var content = $(dialogContent_);

        element.append(response.message);
        element.prependTo(content);
    },

    unsetBindings: function() {

        // Get dialog
        var dialog = $(dialog_);

        // Unbind bindings
        $.each(self.bindings, function(eventName, eventHandler){
            dialog.off(eventName);
        });

        // Unbind escape
        $(document).off("keydown.es.dialog");
    }
});

$(document)
    .on("click", dialogCloseButton_, function(){
        self.close();
    })
    .on("click", dialog_, function(event){
        var dialog = $(dialog_);
        if (event.target==dialog[0]) {
            self.close();
        }
    });


};

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD40.module("dialog", moduleFactory);

}());