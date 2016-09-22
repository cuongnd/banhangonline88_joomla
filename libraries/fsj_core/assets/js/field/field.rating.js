jQuery(document).ready( function() {

    jQuery('.can_rate').mouseover(function () {
        fsj_rating_over(jQuery(this));
    })
    jQuery('.can_rate').mouseout(function () {
        fsj_rating_out(jQuery(this));
    })
    jQuery('.can_rate').click(function () {
        fsj_rating_click(jQuery(this));
    })
});

function fsj_rating_over(el)
{
    var parent = el.parent();
    var current = el.attr('rating');

    parent.find('.can_rate').each(function () {
        var rate = jQuery(this).attr('rating');
        if (rate <= current) {
            jQuery(this).css('color', 'yellow');
        } else {
            jQuery(this).css('color', '#ccc');
        }
    });
}

function fsj_rating_out(el)
{
    var parent = el.parent();
    parent.find('.can_rate').css('color', '');
}

function fsj_rating_click(el)
{
    el.parent().fsj_tooltip('hide');

    var div = el.parent().parent();
    var component = jQuery(div).attr('component');
    var item = jQuery(div).attr('item');
    var itemid = jQuery(div).attr('itemid');
    var rating = jQuery(el).attr('rating');

    var url = jQuery('#fsj_rating_url').text();
    url += "&component=" + encodeURIComponent(component) + "&item=" + encodeURIComponent(item) + "&itemid=" + encodeURIComponent(itemid) + "&rating=" + encodeURIComponent(rating);
  
    div.html(jQuery('#fsj_rating_wait').text());
    jQuery.get(url, function (data) {
        div.html(data);
    });
}