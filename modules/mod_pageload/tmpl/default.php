<?php
defined('_JEXEC') or die;
$doc=JFactory::getDocument();
$doc->addLessStyleSheet(JUri::root().'modules/mod_pageload/assets/less/style.less');
jimport('joomla.html.htmlrender');
JHtml::_('jQuery.checkbox');
?>
{modal content="mycontent" title="<?php echo JText::_('Chào mừng bạn đến với website banhangonline88.com') ?>" open="true" width ="500px"  }{/modal}
{modalcontent mycontent}
<div class="mod_pageload" id="mod_pageload_<?php echo $module->id ?>">
    <div class="row-fluid">
        <div class="span6"><?php echo JText::_('Bạn là') ?>:</div>
        <div class="span6"><?php echo Jhtmlrender::i_am('i_am','','','#mod_pageload_'.$module->id) ?></div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="show-tour">
                <a href="#" class="action-button shadow animate red tim-hieu-website "><?php echo JText::_('Tìm hiểu về website') ?></a>
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
            <label class="checked"><?php echo JText::_('Không hiển thị lần nữa') ?><input type="checkbox" name="dont_show_again"></label>
        </div>
    </div>

</div>

{/modalcontent}

<?php
$js_content = '';
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("#mod_pageload_<?php echo $module->id ?>").mod_pageload({

            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>