<?php
/**
 * @package        RedactorJS
 * @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * RedactorJS is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Unauthorized Access');

/**
 * RedactorJS Editor Plugin
 *
 * @since    1.0
 * @author    Mark Lee <mark@stackideas.com>
 */
class plgEditorRedactorJS extends JPlugin
{
    /**
     * Constructor
     *
     * @since    1.0.1
     * @access    public
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Retrieve's the editor's contents.
     *
     * @since    1.0.1
     * @access    public
     * @param    string    The name of the editor.
     * @return    string
     */
    public function onGetContent($editor)
    {
        ob_start();
        ?>
        EBEditor.redactor( 'get' );
        <?php
        $js = ob_get_contents();
        ob_end_clean();
        return $js;
    }

    /**
     * Set's the editor's content.
     *
     * @since    1.0.1
     * @access    public
     * @param    string    The name of the editor.
     * @param    string    The html code.
     * @return    string
     */
    public function onSetContent($editor, $html)
    {
        ob_start();
        ?>
        EBEditor.insertHTML( <?php echo $html; ?> );
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * Perform any misc tasks necessary when the save happens.
     *
     * @since    1.0.1
     * @access    public
     * @param    string    The name of the editor.
     * @return    string
     */
    public function onSave($editor)
    {
        ob_start();
        ?>
        document.getElementById( '<?php echo $editor; ?>' ).value    = EBEditor.redactor( 'get' );
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Display the editor area.
     *
     * @param   string   The name of the editor area.
     * @param   string   The content of the field.
     * @param   string   The width of the editor area.
     * @param   string   The height of the editor area.
     * @param   int      The number of columns for the editor area.
     * @param   int      The number of rows for the editor area.
     * @param   boolean  True and the editor buttons will be displayed.
     * @param   string   An optional ID for the textarea. If not supplied the name is used.
     *
     * @return  string
     */
    public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null)
    {
        if (empty($id)) {
            $id = $name;
        }
        // Only add "px" to width and height if they are not given as a percentage
        if (is_numeric($width)) {
            $width .= 'px';
        }
        if (is_numeric($height)) {
            $height .= 'px';
        }
        $editor = '';
        // Include foundry's library.
        require_once(JPATH_ROOT . '/media/foundry/2.1/joomla/bootstrap.php');
        ob_start();
        ?>
        <script type="text/javascript">
            var EBEditor = null;
            dispatch("Foundry/2.1").to(function ($) {
                $.require().library("redactor").done(function () {
                    $('#<?php echo $id;?>').redactor({
                        minHeight: <?php echo str_ireplace('px', '', $height);?>,
                        direction: "<?php echo $this->params->get('text_direction', 'ltr'); ?>",
                        wym: <?php echo $this->params->get('visual_structure', false) ? "true" : "false"; ?>

                    });
                    EBEditor = $('#<?php echo $id;?>');
                });
            });
        </script>
        <textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>" cols="<?php echo $col; ?>" rows="<?php echo $row; ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;"><?php echo $content; ?></textarea>
        <?php
        $contents = ob_get_contents();
        ob_end_clean();
        $editor .= $contents;
        $editor .= $this->displayButtons($name, $buttons, $asset, $author);
        return $editor;
    }

    /**
     * Displays the editor buttons.
     *
     * @param string $name
     * @param mixed $buttons [array with button objects | boolean true to display buttons]
     *
     * @return string HTML
     */
    protected function displayButtons($name, $buttons, $asset, $author)
    {
        // Load modal popup behavior
        JHtml::_('behavior.modal', 'a.modal-button');
        $args['name'] = $name;
        $args['event'] = 'onGetInsertMethod';
        $return = '';
        $results[] = $this->update($args);
        foreach ($results as $result) {
            if (is_string($result) && trim($result)) {
                $return .= $result;
            }
        }
        if (is_array($buttons) || (is_bool($buttons) && $buttons)) {
            $results = $this->_subject->getButtons($name, $buttons, $asset, $author);
            /*
             * This will allow plugins to attach buttons or change the behavior on the fly using AJAX
             */
            $return .= "\n<div id=\"editor-xtd-buttons\" class=\"btn-toolbar pull-left\">\n";
            $return .= "\n<div class=\"btn-toolbar\">\n";
            foreach ($results as $button) {
                /*
                 * Results should be an object
                 */
                if ($button->get('name')) {
                    $modal = ($button->get('modal')) ? ' class="modal-button btn"' : null;
                    $href = ($button->get('link')) ? ' class="btn" href="' . JURI::base() . $button->get('link') . '"' : null;
                    $onclick = ($button->get('onclick')) ? ' onclick="' . $button->get('onclick') . '"' : '';
                    $title = ($button->get('title')) ? $button->get('title') : $button->get('text');
                    $return .= '<a' . $modal . ' title="' . $title . '"' . $href . $onclick . ' rel="' . $button->get('options')
                        . '"><i class="icon-' . $button->get('name') . '"></i> ' . $button->get('text') . "</a>\n";
                }
            }
            $return .= "</div>\n";
            $return .= "</div>\n";
        }
        return $return;
    }

    public function onGetInsertMethod()
    {
        static $done = false;
        // Do this only once.
        if (!$done) {
            $done = true;
            $doc = JFactory::getDocument();
            ob_start();
            ?>
            window.jInsertEditorText = function( text , editorName )
            {
            EBEditor.redactor( 'insertHtml' , text );
            }
            <?php
            $js = ob_get_contents();
            ob_end_clean();
            $doc->addScriptDeclaration($js);
        }
        return true;
    }
}
