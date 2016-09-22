EasySocial.require().script('site/events/edit').done(function($) {
    $('[data-events-edit]').addController('EasySocial.Controller.Events.Edit', {
        'id': '<?php echo $event->id; ?>'
    });
});
