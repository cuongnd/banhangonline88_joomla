<?php
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_pageload/assets/less/style.less');
$doc->addScript(JUri::root() . 'modules/mod_pageload/assets/js/script.js');
jimport('joomla.html.htmlrender');
JHtml::_('jQuery.checkbox');
JHtml::_('jQuery.modal');
return;
?>
    <div class="mod_pageload" id="mod_pageload_<?php echo $module->id ?>">
        <a href="#" class="show_mod_pageload_content" ></a>
        <div class="mod_pageload_content">
            <div class="row-fluid">
                <div class="span6"><?php echo JText::_('Bạn là') ?>:</div>
                <div class="span6"><?php echo Jhtmlrender::i_am('i_am', '', '', '#mod_pageload_' . $module->id) ?></div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="show-tour">
                        <a href="#"
                           class="action-button shadow animate red tim-hieu-website "><?php echo JText::_('Tìm hiểu về website') ?></a>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <a href="#" class="action-button shadow animate green pull-right"><?php echo JText::_('Xong') ?></a>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <label class="checked"><?php echo JText::_('Không hiển thị lần nữa') ?><input type="checkbox"
                                                                                                  name="dont_show_again"></label>
                </div>
            </div>
        </div>
    </div>
<?php
$js_content = '';
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("#mod_pageload_<?php echo $module->id ?>").mod_pageload({});
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>