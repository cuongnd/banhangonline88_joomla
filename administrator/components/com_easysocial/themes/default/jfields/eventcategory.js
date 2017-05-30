EasySocial.require()
.library('dialog')
.done(function($)
{
    <?php if (FD::version()->getVersion() < 3) { ?>
        $('body').addClass('com_easysocial25');
    <?php } ?>

    window.selectEventCategory  = function(obj) {
        $('[data-jfield-eventcategory-title]').val(obj.title);

        $('[data-jfield-eventcategory-value]').val(obj.id + ':' + obj.alias);

        EasySocial.dialog().close();
    }

    $('[data-jfield-eventcategory]').on('click', function() {
        EasySocial.dialog({
            content: EasySocial.ajax('admin/views/events/browseCategory', {
                'jscallback': 'selectEventCategory'
            })
        });
    });
});
