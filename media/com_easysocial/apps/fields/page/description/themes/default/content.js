
EasySocial
    .require()
    .script('apps/fields/page/description/content')
    .done(function($) {

        $('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Page.Description', {
            "required": <?php echo $field->required ? 1 : 0; ?>,
            "editor": {

                getContent: function() {
                    <?php echo 'return ' . $editor->getContent($inputName); ?>
                }
            }
        });
    });
