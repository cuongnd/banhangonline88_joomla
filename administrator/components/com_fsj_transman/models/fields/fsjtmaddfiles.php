<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport("joomla.filesystem.folder");

class JFormFieldfsjtmaddfiles extends JFormField
{
	static $js = false;
	protected function getInput()
	{
		FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.less");
		FSJ_Page::IncludeModal();
		
		$code = $this->form->getValue('langcode');
		if ($code == "")
		{
			$script = "<script>jQuery('#toolbar-plus button').attr('disabled', 'disabled');</script>";
			return $script. JText::_("FSJ_TM_PICK_LANG_FIRST");	
		}
		
		$this->addJS();

		$client = $this->element['fsjtmpickfiles_client'];

		$output = array();
		
		$output[] = "<textarea name='".$this->name."' id='add_files_data' rows='10' cols='80' style='display:none;'>" . $this->value . "</textarea>";
		
		$output[] = "<a id='add_files_button' href='" . JRoute::_("index.php?option=com_fsj_transman&view=pickfiles&tmpl=component&filter_element=" . $code) . "' class='fsj_show_modal_iframe btn btn-small' data_modal_width='700'><i class='icon-plus'></i>".JText::_("FSJ_TM_ADD")."</a>";
		$output[] = "<div class='file_list'>";
		$output[] = "<ul id='file_list'>";
		$output[] = "</ul>";
		$output[] = "</div>";
		$output[] = "<div style='height: 120px;clear:both;'></div>";
		
		return implode($output);	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$data = json_decode($value, true);
		$count = 0;
		if (is_array($data))
		{
			foreach ($data as $key => $cats)
			{
				foreach ($cats as $cat => $files)
				{
					$count += count($files);
				}		
			}
		}
		
		echo $count . " files";
	}
	
	function addJS()
	{
		if (!self::$js)
		{
			$document = JFactory::getDocument();
			$document->addScript( JURI::root().'administrator/components/com_fsj_transman/assets/js/addfiles.js' );
			if (!FSJ_Helper::IsJ3())
			{
				FSJ_Page::Style("libraries/fsj_core/assets/css/bootstrap/bootstrap_fsjonly.less");
				FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.j25.less");
			}
?>
<script>
var tm_translate = {
	g: '<?php echo JText::_('FSJ_TM_GENERAL'); ?>',
	t: '<?php echo JText::_('FSJ_TM_TEMPLATE'); ?>',
	c: '<?php echo JText::_('FSJ_TM_COMPONENT'); ?>',
	m: '<?php echo JText::_('FSJ_TM_MODULE'); ?>',
	p: '<?php echo JText::_('FSJ_TM_PLUGIN'); ?>',
	
	site: '<?php echo JText::_('FSJ_TM_SITE'); ?>',
	admin: '<?php echo JText::_('FSJ_TM_ADMIN'); ?>',
	nocat: '<?php echo JText::_('FSJ_TM_NO_CATEGORY'); ?>'
}
</script>
<?php	
			self::$js = true;	
		}
	}
}
