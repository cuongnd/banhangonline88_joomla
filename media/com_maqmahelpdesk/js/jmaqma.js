/*
 * MaQma Helpdesk
 * Released under the GPL License.
 */

if (LOADJQUERY == "true"){
    var $jMaQma = jQuery.noConflict();
}else{
    var $jMaQma = jQuery.noConflict(true);
}

// Don't clobber any existing jQuery.browser in case it's different
if ( !jQuery.browser ) {
    matched = jQuery.uaMatch( navigator.userAgent );
    browser = {};

    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }

    // Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }

    jQuery.browser = browser;
}