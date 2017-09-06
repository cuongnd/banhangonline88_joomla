EasySocial.module('shared/responsive', function($){

var module = this;

$(document)
    .on('click.es.sidebar.toggle', '[data-es-sidebar-toggle]', function() {
        // Locate the closest container
        var button = $(this);
        var container = button.siblings('[data-es-container]');

        if (container.length <= 0) {
            return;
        }

        container.toggleClass('sidebar-open');
    });

$(document).on("click.es.sidebar", "[data-sidebar-toggle]", function(){

    // Prefer sidebar from siblings
    var button = $(this);
    var selector = "[data-sidebar]";
    var sidebar = button.siblings(selector);

    // If not find closest sidebar
    if (sidebar.length < 1) {
        sidebar = button.closest(selector);
    }

    // If not find any sidebar
    if (sidebar.length < 1) {
        sidebar = $(selector);
    }

    sidebar
        .toggleClass("sidebar-open")
        .trigger("sidebarToggle");

});


// simulate the responsive toggle button click.
$(document).on("onEasySocialFilterClick", function(){

    // lets check if this is viewing with mobile app or not. if yes, we
    // do not process further.
    var mobileWrappr = $("[data-es-mobile-wrapper]");
    if (mobileWrappr.length > 0) {
        return;
    }

    var container = $("[data-es-sidebar-toggle]").siblings('[data-es-container]');
    if (container.length <= 0) {
        return;
    }

    container.toggleClass('sidebar-open');
});



module.resolve();

});
