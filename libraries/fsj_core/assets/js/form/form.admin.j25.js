jQuery(document).ready(function () {
    // remove button group class from dropdowns as they dont work in Joomla 2.5 with it
    jQuery('select.btn-group').removeClass('btn-group');

    // add styles to toolbar buttons on inline pages and popups so they are bootstrap styles instead
    // of the messed up styles 
    jQuery('.fsj_inline #toolbar > ul > li').each(function () {
        jQuery(this).find("a").addClass('btn');
        jQuery(this).find("a").addClass('btn-small');
        var span = jQuery(this).find("a > span");
        var cls = jQuery(span).attr('class');
        if (cls)
            jQuery(span).attr('class', cls.replace("32-", ""));
    });

    // set up new and save as green success buttons
    jQuery('.fsj_inline #toolbar #toolbar-new > a').addClass('btn-success');
    jQuery('.fsj_inline #toolbar #toolbar-apply > a').addClass('btn-success');

    // add correct j3 toolbar class so icons are correct color
    jQuery('div.toolbar-list').addClass('btn-toolbar');

    // sort out order arrows
    jQuery('a.jgrid span.uparrow').each(function () {
        var link = jQuery(this).parent();
        link.html("<i class='icon-uparrow'></i>");
        link.addClass('btn');
        link.addClass('btn-micro');
    });
    jQuery('a.jgrid span.downarrow').each(function () {
        var link = jQuery(this).parent();
        link.html("<i class='icon-downarrow'></i>");
        link.addClass('btn');
        link.addClass('btn-micro');
    });

    // remove extra padding at top of page
    var is_inline = jQuery('div.fsj_inline');
    if (is_inline.length > 0) {
        jQuery('body').css('margin-top', '0px');
        jQuery('div.fsj_inline').css('border', '1px solid transparent');
    }

    // fix ordering save icon
    jQuery('th.order').prepend(jQuery('th.order > a.saveorder'));
});