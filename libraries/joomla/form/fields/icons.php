<?php
/**
 * @package         Regular Labs Library
 * @version         16.8.22020
 *
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;


class JFormFieldIcons extends JFormField
{
    public $type = 'Icons';

    protected function getInput()
    {

        $this->params = $this->element->attributes();
        $value = $this->value;


        $doc = JFactory::getDocument();
        $doc->addScript('/libraries/joomla/form/fields/icons/script.js');
        $doc->addLessStyleSheet('/templates/vina_bonnie/themify-icons/themify-icons-view-backend.less');
        $doc->addLessStyleSheet('/libraries/joomla/form/fields/icons/less/style.less', nuull, array(), true);
        $doc->addLessStyleSheet('/templates/vina_bonnie/font-awesome-4.7.0/less/font-awesome.less', nuull, array(), true);
        $doc->addLessStyleSheet('/templates/vina_bonnie/bootstrap-3.3.7/less/glyphicons_backend.less', nuull, array(), true);
        ob_start();
        ?>
        <div class="icons-wrapper">
            <div class="wrapper-selected">
                <div class="wrapper-selected">
                    Enable: <select class="enable">
                        <option <?php echo $value!=""?'selected':'' ?> value="1"><?php echo JText::_('JYES') ?></option>
                        <option <?php echo $value==""?'selected':'' ?> value="0"><?php echo JText::_('JNO') ?></option>
                    </select>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="selected"><i class="<?php echo $value ?>"></i></div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#awesome" data-toggle="tab">Font Awesome</a></li>
                            <li><a href="#themify" data-toggle="tab">Font themify</a></li>
                            <li><a href="#glyphicons" data-toggle="tab">Glyphicons</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="awesome">
                                <?php include_once JPATH_ROOT . DS . 'libraries/joomla/form/fields/icons/awesome.php' ?>
                            </div>
                            <div class="tab-pane " id="themify">
                                <?php include_once JPATH_ROOT . DS . 'libraries/joomla/form/fields/icons/themify.php' ?>
                            </div>
                            <div class="tab-pane" id="glyphicons">
                                <?php include_once JPATH_ROOT . DS . 'libraries/joomla/form/fields/icons/glyphicons.php' ?>
                            </div>

                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
            <input type="hidden" name="<?php echo $this->name ?>" id="<?php echo $this->id ?>"
                   value="<?php echo $this->value ?>">
        </div>
        <?php
        $html = ob_get_clean();
        $js_content = '';
        $doc = JFactory::getDocument();
        ob_start();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.icons-wrapper').icons_wrapper({
                    selected: "<?php echo $value ?>",
                    name: "<?php echo $this->name ?>"
                });
            });
        </script>
        <?php
        $js_content = ob_get_clean();
        $js_content = JUtility::remove_string_javascript($js_content);
        $doc->addScriptDeclaration($js_content);


        return $html;
    }
}
