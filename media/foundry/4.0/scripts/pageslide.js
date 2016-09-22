(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

$.fn.pageslide = function(content, direction) {

    clearTimeout(this.data("pageslide-reset"));

    var viewport = this.find(".pageslide-viewport"),

        page =
            // Create page
            $('<div class="pageslide-page active"></div>')
                .append(content)

                // Insert page to viewport
                [direction=="prev" ? "prependTo" : "appendTo"](viewport)

                // Remove active class from siblings
                .siblings()
                .removeClass("active")
                .end();

        // Get container and switch class
        container =
            this.switchClass("fx-" + direction);

    // Reset
    this.data("pageslide-reset",
        setTimeout(function(){

            container
                .switchClass("fx-reset")
                .removeClassAfter("fx-reset");

            // Detach inactive pages
            page.siblings().detach();

        }, 500)
    );

    return this;
};
}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD40.module("pageslide", moduleFactory);

}());