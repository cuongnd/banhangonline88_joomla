jQuery(document).ready(function () {
    jQuery('.cron_log_show').click(function () {
        jQuery('#fsj_modal').unbind('hidden');

        jQuery('#fsj_modal').html(jQuery('#fsj_modal_base').html());
        jQuery('#fsj_modal').css('width', '760px');
        jQuery('#fsj_modal').css('margin-left', '-380px');

        var title = jQuery(this).parent().parent().find('td:nth-child(2)').text();
        var date = jQuery(this).parent().parent().find('td:nth-child(3)').text();
        var success = jQuery(this).parent().parent().find('td:nth-child(4)').text();
        var result = jQuery(this).parent().parent().find('td:nth-child(5)').text();

        jQuery('#fsj_modal h3').html(title + " - " + date + " - " + success);
        jQuery('#fsj_modal div.modal-body').html("<h4>" + result + "</h4>" + jQuery(this).html());

        jQuery('#fsj_modal').modal("show");
    })
})