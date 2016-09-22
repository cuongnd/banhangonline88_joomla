
<?php if ($type) { ?>
EasySocial.require()
.script('http://www.skypeassets.com/i/scom/js/skype-uri.js')
.done(function($) {
    Skype.ui({
        "name": "<?php echo $type;?>",
        "element": "SkypeButton-<?php echo $user->id;?>",
        "participants": ["<?php echo $value;?>"],
        "imageSize": 16,
        <?php if ($params->get('theme', 'blue') != 'blue') { ?>
        ,"imageColor": "<?php echo $params->get('theme');?>"
        <?php } ?>
    });
});
<?php } ?>