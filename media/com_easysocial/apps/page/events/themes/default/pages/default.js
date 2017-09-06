EasySocial.require().script('site/events/browser').done(function($) {
    $('[data-page-events-list]').addController('EasySocial.Controller.Events.Browser', {
        page: '<?php echo $page->id; ?>'
    });
});
